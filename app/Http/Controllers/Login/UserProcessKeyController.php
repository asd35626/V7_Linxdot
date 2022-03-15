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
    //
    public static $admin = "d6592f8a-832c-11e7-9eaf-0021917b0f35";

    public function store(Request $request)
    {

        $responseBody = array(
                'status' => 0,
                'message' => '',
        );

        $memberNo = $request->has('loginName') ? $request->input('loginName') : '';
        
        if($memberNo == ''){
        	$responseBody['status'] = 1;
        	$responseBody['message'] = 'Please provide data';
        } 

        if ($responseBody['status'] == 0) {
        	$users = DimUser::where('MemberNo', $memberNo)
        					->where('IfValid', 1)
        					->where('IfDelete', 0);              
        	//dd($users->count());
            if($users->count() == 1){
        		//pass
        	}else if($users->count() == 0){
        		$responseBody['status'] = 1;
        		$responseBody['message'] = '帳號不存在';
        	}else {
                $responseBody['status'] = 1;
                $responseBody['message'] = '帳號狀態異常，拒絕存取。';
            }
        }
        
        if ($responseBody['status'] == 0) {
            //刪除舊的key
            $keys = Key::where('MemberNo', $memberNo)
            			->where('IfValid', 1)
            			->where('IfDelete', 0);
            if($keys->count() > 0){
                $keys->update([
                    'IfDelete'          => 1,
                    'IfDeleteBy'        => self::$admin,
                    'IfDeleteDate'      => Carbon::now('Asia/Taipei')->toDateTimeString(),
                    'IfDeleteIPAddress' => $request->ip()
                ]);
            }
        }
        
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

        	Key::create($storeArray);
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
