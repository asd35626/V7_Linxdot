<?php

namespace App\Http\Controllers\Login;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Model\DimUser;
use App\Model\KEYHistory as Key;
use Uuid;
use Response;

class UserProcessKeyController extends Controller
{
    public static $admin = "d6592f8a-832c-11e7-9eaf-0021917b0f35";

    public function store(Request $request)
    {
        $responseBody = array(
            'status' => 0,
            'message' => '',
        );
        // 步驟一：檢查驗證碼

        /** PHP 數字驗證 **/
        if(isset($_REQUEST['authcode'])){
            session_start();
            //strtolower()小寫函數
            if(strtolower($_REQUEST['authcode'])== $_SESSION['authcode']){
                //pass
            }else{
                // 未通過圖形驗證
                $responseBody['status'] = 1;
                $responseBody['message'] = 'Invalid CAPTCHA number';
                return Response::json($responseBody, 200);
            }
        }
        /** PHP 數字驗證 **/

        // 步驟二：確認帳號
        $memberNo = $request->has('loginName') ? $request->input('loginName') : '';
        
        if($memberNo == ''){
            $responseBody['status'] = 1;
            $responseBody['message'] = 'Please provide data';
        } 

        if($responseBody['status'] == 0){
            $users = DimUser::where('MemberNo', $memberNo)
                            ->where('IfValid', 1)
                            ->where('IfDelete', 0);
            //dd($users->count());
            if($users->count() == 1){
                //pass
            }else if($users->count() == 0){
                // 帳號不存在
                $responseBody['status'] = 1;
                $responseBody['message'] = 'Non-existant account';
            }else {
                // 帳號狀態異常，拒絕存取。
                $responseBody['status'] = 1;
                $responseBody['message'] = 'Exception account';
            }
        }

        // 步驟三：刪除舊的key     
        if ($responseBody['status'] == 0) {
            $keys = Key::where('MemberNo', $memberNo)
                        ->where('IfValid', 1)
                        ->where('IfDelete', 0);            
            if($keys->count() > 0){
                $update = [
                    'IfDelete' => 1,
                    'IfDeleteBy' => self::$admin,
                    'IfDeleteDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
                    'IfDeleteIPAddress' => $request->ip(),
                ];
                Key::on('mysql2')->where('MemberNo', $memberNo)
                        ->where('IfValid', 1)
                        ->where('IfDelete', 0)
                        ->update($update);
            }
        }

        // 步驟四：產生新的key        
        if ($responseBody['status'] == 0) {
            $id = Uuid::generate(4);
            $check = Key::find($id);
            while($check){
                $id = Uuid::generate(4);
                $check = Key::find($id);
            }

            $key = $this->generateKey(8);

            $storeArray = array(
                            'ID' => $id->string,
                            'Key' => $key,
                            'MemberNo' => $memberNo,
                            'IfValid' => 1,
                            'IfDelete' => 0,
                            'CreateBy' => self::$admin,
                            'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
                            'CreateIPAddress' => $request->ip()
                            );

            Key::on('mysql2')->create($storeArray);
            //dd($storeArray);
            $responseBody['message'] = 'success';
            $responseBody['data'] = $key;
        }

        return Response::json($responseBody, 200);
    }

    //產生key
    private function generateKey($length){
      $characters = '8394072516';
      $charactersLength = strlen($characters);
      $code = '';

      for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, $charactersLength - 1)];
      }

      return $code;
    }
}
