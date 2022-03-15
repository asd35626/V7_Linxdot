<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Model\DimUser;
use App\Model\UserProcessTicket;
use App\Model\DimUserUnlockHistory;
use Uuid;
use Response;

class UserController extends Controller
{
    public static $admin = "d6592f8a-832c-11e7-9eaf-0021917b0f35";

    //解鎖
    public function Unlock(Request $request)
    {
        $responseBody = array(
                'status' => 0,
                'message' => '',
        );

        // Check UserProcessTicket
        $authToken = $request->header('authToken', '');
        $UID    = $request->input('UID', '');
        
        if($authToken == ''){
            $responseBody['status'] = 1;
            $responseBody['message'] = '缺少 Token';
        }elseif($UID == ''){
            $responseBody['status'] = 1;
            $responseBody['message'] = '缺少 UID';
        }else{
            $userProcessTicket = UserProcessTicket::where('ProcessTicketId', $authToken)
                                ->where('IfSuccess', 1)
                                ->where('IfLogout', 0)
                                ->where('ExpireDate', '>=', Carbon::now('Asia/Taipei'));
            if ($userProcessTicket->count() != 1) {
                $responseBody['status'] = 1;
                $responseBody['message'] = 'Token 無效';
            } else {
                $userProcessTicket = $userProcessTicket->first();
            }
        }
        
        //檢查 User 
        if ($responseBody['status'] == 0) {
            $user = DimUser::find($UID);
            if($user === false){
                $responseBody['status'] = 1;
                $responseBody['message'] = 'Token 無效';
            }else{
                //失敗次數歸零
                $user->LoginFailTimes = 0;
                $user->save();
                //log
                $logId = Uuid::generate(4);
                $check = DimUserUnlockHistory::find($logId);
                while($check){
                  $logId = Uuid::generate(4);
                  $check = DimUserUnlockHistory::find($logId);
                }
                DimUserUnlockHistory::create([
                    'ID'              => $logId->string,
                    'UID'             => $UID,
                    'CreateDate'      => Carbon::now('Asia/Taipei')->toDateTimeString(),
                    'CreateBy'        => $userProcessTicket->UID,
                    'CreateIPAddress' => $request->ip() 
                ]);
                $responseBody['message'] = '帳號已解鎖';
            }
        }

        return Response::json($responseBody, 200);
    }


    private function addLog(){

    }
}
