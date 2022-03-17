<?php

namespace App\Http\Controllers\Login;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Model\DimUser;
use App\Model\KEYHistory as Key;
use App\Model\UserProcessTicket;
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
        // 步驟一：檢查輸入
        $memberNo = $request->input('loginName', '');
        $password = $request->input('loginPassword', '');
        if($memberNo === ''){
            $responseBody['status'] = 1;
            $responseBody['message'] = 'Please enter account';
            return Response::json($responseBody, 200);
        }

        if($password === ''){
            $responseBody['status'] = 1;
            $responseBody['message'] = 'Please enter password';
            return Response::json($responseBody, 200);
        }

        // 檢查帳號是否存在並正常
        $users = DimUser::where('MemberNo', $memberNo)
                ->where('IfValid', 1)
                ->where('IfDelete', 0);
        $count = $users->count();

        //dd($users->count());
        if ($count == 1) {
            $user = $users->first();

            // 檢查錯誤次數
            if($user->LoginFailTimes > 5){
                // 密碼錯誤次數過多，已被鎖定，無法登入
                $responseBody['message'] = 'Too many incorrect password entered, Account is locked';
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

                // 用hash檢查密碼是否正確
                if (Hash::check($hash, $password)) {
                    // 產生token
                    $processTicket = $this->generateProcessTicket($user, $request);
                    // 登入成功將失敗次數歸零
                    if($user->LoginFailTimes > 0){
                        $update = [
                            'LoginFailTimes' => 0,
                        ];
                        // dd($user->LoginFailTimes);
                        DimUser::on('mysql2')->where('MemberNo', $memberNo)
                                ->where('IfValid', 1)
                                ->where('IfDelete', 0)->first()->update($update);
                    }
                    // 檢查是否取得token
                    if ($processTicket) {
                        $responseBody['status'] = 0;
                        $responseBody['message'] = 'Login Successfully';
                        $responseBody['userProcessTicket'] = $processTicket;
                        $_SESSION['authToken'] = $processTicket;
                    }else{
                        $responseBody['status'] = 1;
                        $responseBody['message'] = 'Operation error (0002)';
                    }
                } else {
                    $errorMsg = 'Login Fail(Password not match)';
                    // 記錄登入失敗原因
                    $this->loginFailLog($user, $request, $errorMsg);
                    // 更新密碼錯誤次數
                    $times = $user->LoginFailTimes;
                    $uid = $user->Id;
                    DimUser::on('mysql2')->find($uid)->update(['LoginFailTimes' => ($times+1)]);
                    // 錯誤訊息，密碼錯誤。
                    $responseBody['status'] = 1;
                    $responseBody['message'] = 'Incorrect password';
                }
            }else{
                $errorMsg = 'Login Fail(Key not found)';
                // 找不到登入Key
                $this->loginFailLog($user, $request, $errorMsg);
                $responseBody['status'] = 1;
                // 未取得驗證金鑰
                $responseBody['message'] = 'Operation error (0001)';
            }
        } else if($count == 0){
            $errorMsg = 'Login Fail(MemberNo not found)';
            // 記錄登入失敗原因
            $this->loginFailLog(null, $request, $errorMsg);
            // 找不到帳號
            $responseBody['status'] = 1;
            $responseBody['message'] = 'Non-existant account';
        } else {
            $errorMsg = 'Login Fail(MemberNo confilict)';
            // 記錄登入失敗原因
            $this->loginFailLog(null, $request, $errorMsg);
            // 多個重複的帳號
            $responseBody['status'] = 1;
            $responseBody['message'] = 'Exception account';
        }

        return Response::json($responseBody, 200);
    }

    public function destroy(Request $request,$id)
    {
        $responseBody = array(
            'status' => 0,
            'message' => 'Unknown Error',
        );

        //Token
        $userProcessTickets = UserProcessTicket::where('ProcessTicketId', $id);

        if (!$userProcessTickets) {
            $responseBody['status'] = 0;
            $responseBody['message'] = 'UserProcessTicket does not exist';

            return Response::json($responseBody, 404);
        }
        $count = $userProcessTickets->count();

        if ($count > 0) {
            $update = [
                'IfLogout' => 1,
                'LogoutIssue' => 'Logout Successfully',
                'LogoutIPAddress' => $request->ip(),
                'LogoutDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
            ];
            UserProcessTicket::on('mysql2')->where('ProcessTicketId', $id)->update($update);
            $_SESSION['authToken'] = null;
        }

        $responseBody['status'] = 1;
        $responseBody['message'] = 'Deleted Succesfully';

        return Response::json($responseBody, 200);
    }

    //登入驗證通過，產生 ticket 並回傳
    private function generateProcessTicket($user, $request){
        // step 1 : find old tokens and delete them
        // 步驟一：刪除舊的token
        $tokens = UserProcessTicket::on('mysql2')->where('UID', $user->UID)
                                   ->where('IfSuccess', 1)
                                   ->where('IfLogout', 0)
                                   ->update(array(
                                        'IfLogout' => 1,
                                        'LogoutDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
                                        'LogoutIssue' => 'logout by user login',
                                        'LogoutIPAddress' => $request->ip()
                                    ));


        // step 2 : create new one
        // 步驟二：新增token
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

        $userProcessTicket = UserProcessTicket::on('mysql2')->create($storeArray);
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

        $userProcessTicket = UserProcessTicket::on('mysql2')->create($storeArray);
    }

}
