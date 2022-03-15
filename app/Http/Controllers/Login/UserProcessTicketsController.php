<?php

namespace App\Http\Controllers\Login;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Model\DimUser;
use App\Model\KEYHistory as Key;
use App\Model\UserProcessTicket;
use App\Model\APP_DeviceToken;
use Uuid;
use Hash;
use Response;

class UserProcessTicketsController extends Controller
{
    public static $admin = "d6592f8a-832c-11e7-9eaf-0021917b0f35";

    public function show($id)
    {
        $responseBody = array(
                'status' => 0,
                'message' => '',
        );

        // Check UserProcessTicket

        $userProcessTicket = UserProcessTicket::where('ProcessTicketId', $id)
                                ->where('IfSuccess', 1)
                                ->where('IfLogout', 0)
                                ->where('ExpireDate', '>=', Carbon::now('Asia/Taipei'));

        if ($userProcessTicket->count() != 1) {
            $responseBody['status'] = 1;
            $responseBody['message'] = 'Token 無效';
        } else {
            $userProcessTicket = $userProcessTicket->first();
        }

        if ($responseBody['status'] == 0) {
            $responseBodyData = array(
                'UserName' => $userProcessTicket->user->RealName,
                'UserType' => $userProcessTicket->user->UserType,
                'DegreeId' => $userProcessTicket->user->DegreeId,
                'UID' => $userProcessTicket->UID,
                'ExpireDate' => $userProcessTicket->ExpireDate,
                'IfSuccess' => $userProcessTicket->IfSuccess,
                'IfLogout' => $userProcessTicket->IfLogout,
            );

            $responseBody['message'] = 'Token 有效';
            $responseBody['data'] = $responseBodyData;
        }

        return Response::json($responseBody, 200);
    }

    public function store(Request $request)
    {
        $responseBody = array(
            'status' => 0,
            'message' => 'Unknown Error',
            'errors' => ""
        );
        //initail
        session_start();
        $memberNo = $request->input('loginName', '');
        $password = $request->input('loginPassword', '');
        $captcha = $request->input('captcha', '');
        if($memberNo === ''){
            $responseBody['message'] = '請輸入帳號';
            return Response::json($responseBody, 200);
        }

        if($password === ''){
            $responseBody['message'] = '請輸入密碼';
            return Response::json($responseBody, 200);
        }
        //dd($request->all());

        /** PHP 數字驗證 **/
        if(isset($_REQUEST['authcode'])){
            //strtolower()小寫函數
            if(strtolower($_REQUEST['authcode'])== $_SESSION['authcode']){
                //pass
            }else{
                $responseBody['message'] = '未通過圖形驗證';
                return Response::json($responseBody, 200);
            }
        }

        /** PHP 數字驗證 **/

        $users = DimUser::where('MemberNo', $memberNo)
                ->where('IfValid', 1)
                ->where('IfDelete', 0);
        $count = $users->count();

        //dd($users->count());
        if ($count == 1) {
            $user = $users->first();
            if($user->LoginFailTimes > 5){
                $responseBody['message'] = '密碼錯誤次數過多，已被鎖定，無法登入';
                return Response::json($responseBody, 200);
            }
            //get Key
            $keys = Key::where('MemberNo', $memberNo)
                        ->where('IfValid', 1)
                        ->where('IfDelete', 0)
                        ->orderBy('CreateDate', 'DESC');
            //$responseBody['debug']=$keys->toSql();
            
            if($keys->count() > 0){
                $key = $keys->get()->first()->Key;
                $hash = $user->UserPassword.$key;
                //dd($key);
                if (Hash::check($hash, $password)) {
                    
                    // The passwords match...

                    $processTicket = $this->generateProcessTicket($user, $request);
                    //登入成功將失敗次數歸零
                    if($user->LoginFailTimes > 0){
                        $user->LoginFailTimes = 0;
                        // dd($user->LoginFailTimes);
                        $user->save();
                    }
                    //toekn
                    if ($processTicket) {
                        $responseBody['status'] = 1;
                        $responseBody['message'] = 'Login Successfully';
                        $responseBody['userProcessTicket'] = $processTicket;
                        $_SESSION['authToken'] = $processTicket;
                    }else{
                        $responseBody['status'] = 0;
                        $responseBody['message'] = '系統異常，無法通過驗證，請記錄您的操作步驟並聯絡管理人員處理';
                    }
                } else {
                    $errorMsg = 'Login Fail(Password not match)';
                    //記錄登入失敗原因
                    $this->loginFailLog($user, $request, $errorMsg);
                    //更新密碼錯誤次數
                    $times = $user->LoginFailTimes;
                    $user->LoginFailTimes = ($times+1);
                    $user->save();
                    //錯誤訊息
                    $responseBody['status'] = 0;
                    $responseBody['message'] = '密碼錯誤。';
                    // $responseBody['hash'] = $hash;
                    // $responseBody['key'] = $key;
                }
            }else{
                $errorMsg = 'Login Fail(Key not found)';
                //找不到登入Key
                $this->loginFailLog($user, $request, $errorMsg);
                $responseBody['status'] = 0;
                $responseBody['message'] = '未取得驗證金鑰';
            }
        } else if($count == 0){
            $errorMsg = 'Login Fail(MemberNo not found)';
            //記錄登入失敗原因
            $this->loginFailLog(null, $request, $errorMsg);
            $responseBody['status'] = 0;
            $responseBody['message'] = '帳號不存在。';
        } else {
            $errorMsg = 'Login Fail(MemberNo confilict)';
            //多個重複的帳號
            $this->loginFailLog(null, $request, $errorMsg);
            $responseBody['status'] = 0;
            $responseBody['message'] = '帳號狀態異常，請連路管理人員。';
        }

        // return Response::json($responseBody, 200)->withCookie($cookie);
        return Response::json($responseBody, 200);
    }

    public function destroy(Request $request,$id)
    {
        $responseBody = array(
                'status' => 0,
                'message' => 'Unknown Error',
            );
        session_start();
        // dd($_SESSION);
        //Token
        $userProcessTickets = UserProcessTicket::where('ProcessTicketId', $id);

        if (!$userProcessTickets) {
            $responseBody['status'] = 0;
            $responseBody['message'] = 'UserProcessTicket does not exist';

            return Response::json($responseBody, 404);
        }

        if ($userProcessTickets->count() > 0) {
            $userProcessTicket = $userProcessTickets->first();
            $userProcessTicket->IfLogout = 1;
            $userProcessTicket->LogoutIssue = 'Logout Successfully';
            $userProcessTicket->LogoutIPAddress = $request->ip();
            $userProcessTicket->LogoutDate = Carbon::now('Asia/Taipei')->toDateTimeString();

            $userProcessTicket->save();
            $userProcessTicket->update([
                'IfLogout'          => 1,
                'LogoutIssue'       => 'Logout Successfully',
                'LogoutIPAddress'   => $request->ip(),
                'LogoutDate'        => Carbon::now('Asia/Taipei')->toDateTimeString()
            ]);
            $_SESSION['authToken'] = null;
        }


        $responseBody['status'] = 1;
        $responseBody['message'] = 'Deleted Succesfully';

        return Response::json($responseBody, 200);
    }

    //登入驗證通過，產生 ticket 並回傳
    private function generateProcessTicket($user, $request){
        //step 1 : find old tokens and delete them
        $tokens = UserProcessTicket::where('UID', $user->UID)
                                   ->where('IfSuccess', 1)
                                   ->where('IfLogout', 0)
                                   ->update(array(
                                        'IfLogout' => 1,
                                        'LogoutDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
                                        'LogoutIssue' => 'logout by user login',
                                        'LogoutIPAddress' => $request->ip()
                                    ));


        //step 2 : create new one
        $id = Uuid::generate(4);
        $check = UserProcessTicket::find($id);
        while($check){
          $id = Uuid::generate(4);
          $check = UserProcessTicket::find($id);
        }

        $storeArray = array(
            'ProcessTicketId' => $id->string,
            'UID' => $user->Id,
            'IfSuccess' => 1,
            'IfLogout' => 0,
            'AppName'   => '管理後台',
            'RequestDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
            'RequestIssue' => 'Login Successfully',
            'RequestIPAddress' => $request->ip(),
            'DeviceInfo' => json_encode($request->header()),//$request->header() will get array or string
            //'ExpireDate' => Carbon::now('Asia/Taipei')->addHours(12) //->addYears addDays
            'ExpireDate' => Carbon::now('Asia/Taipei')->addHours(env('EXPIREDATE',12))
        );

        $userProcessTicket = UserProcessTicket::create($storeArray);
        return $id->string;
    }

    //記錄登入失敗原因
    private function loginFailLog($user=null, $request, $errorMsg){
        $id = Uuid::generate(4);
        $check = UserProcessTicket::find($id);
        while($check){
          $id = Uuid::generate(4);
          $check = UserProcessTicket::find($id);
        }

        $storeArray = array(
            'ProcessTicketId' => $id->string,
            'UID' => !(is_null($user)) ? $user->Id : null,
            'IfSuccess' => 0,
            'IfLogout' => 0,
            'RequestDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
            'RequestIssue' => $errorMsg,
            'RequestIPAddress' => $request->ip(),
            'DeviceInfo' => json_encode($request->header()),//$request->header() will get array or string
            'ExpireDate' => Carbon::now('Asia/Taipei')->addHours(12) //->addYears addDays
        );

        $userProcessTicket = UserProcessTicket::create($storeArray);
    }

}
