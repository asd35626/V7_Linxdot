<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use App\Model\DimUser;
use App\Model\DimMemberDegreeToMemberType;
use App\Model\APPFunctionPermission;
use App\Model\UserProcessTicket;
use App\Http\Requests;
use Carbon\Carbon;
use Response;
use Uuid;
use DB;

class FrontEndPermissionAPIController extends Controller
{
    /**
     *  取得等級權限表
     */
    public function PermissionList(Request $request){
      $responseBody = array(
        'status' => 0,
        'message' => 'Unknown Error'
      );
      // dd($responseBody);

      // 用Token取UID
      $UserProcessTicketId = $request->header('Authorization') ? $request->header('Authorization') : '';

      $UserProcessTicketId = UserProcessTicket::find($UserProcessTicketId);
      $user = '';
      if (isset($UserProcessTicketId)) {
          $user = DimUser::find($UserProcessTicketId->UID);
      }

      // id = MTID(權限ID)
      $id = $request->input('id', '');

      $adminDegree = DimMemberDegreeToMemberType::find($id);

      if(!$adminDegree){
        $responseBody['status'] = 1;
        $responseBody['message'] = 'UserDegree not found';
        
        return Response::json($responseBody, 200);
      }

      $responseBody['MemberTypeId'] = $adminDegree->MemberType;
      $responseBody['DegreeId'] = $adminDegree->DegreeId;
      // $searchArr = array(
      //   'UserTypeId' => $adminDegree->UserType,
      //   'DegreeId' => $adminDegree->DegreeId
      // );

      // $APPFunctionPermissions = DB::table('ADM_Functionadm')
      //                             ->LeftJoin('ADM_DefaultPermission',
      //                             function($join) use ($searchArr){
      //                               $join->on('ADM_Function.FunctionId', '=', 'ADM_DefaultPermission.FunctionId')
      //                                 ->where('ADM_DefaultPermission.UserTypeId','=', $searchArr['UserTypeId'])
      //                                 ->where('ADM_DefaultPermission.UserDegreeId','=', $searchArr['DegreeId']);
      //                               }
      //                             );

      //                             // ->where('UserTypeId', $adminDegee->UserType)
      //                             // ->where('UserDegreeId', $adminDegee->DegreeId)
      // $APPFunctionPermissions =$APPFunctionPermissions->where('ADM_Function.IfDelete', 0)
      //                             ->whereNotNull('ADM_Function.ParentFunctionId')
      //                             ->orderBy('ADM_Function.ParentFunctionId','asc')
      //                             ->orderBy('ADM_Function.MenuOrder','asc')
      //                             ->select(
      //                               'ADM_Function.FunctionId',
      //                               'ADM_Function.FunctionName',
      //                               'ADM_Function.FunctionDesc',
      //                               'ADM_Function.MenuOrder',
      //                               'ADM_Function.IfValid',
      //                               'ADM_Function.FunctionURL',
      //                               'ADM_Function.ParentFunctionId',
      //                               'ADM_DefaultPermission.PermissionId',
      //                               'ADM_DefaultPermission.UserTypeId',
      //                               'ADM_DefaultPermission.UserDegreeId',
      //                               'ADM_DefaultPermission.IfAccess'
      //                               );
      $sql = 'select AFUN.`FunctionId`, 
              AFUN.`FunctionName`, 
              AFUN.`FunctionDesc`, 
              AFUN.`SerialNo`,
              (select p.`FunctionName` from `APP_Function` as p where AFUN.`ParentFunctionId` = p.`FunctionId`) as ParentName,  
              AFUN.`IfValid`, 
              AFUN.`FunctionURL`, 
              AFUN.`ParentFunctionId`, 
              de.`PermissionId`, 
              de.`UserTypeID`, 
              de.`UserDegreeID`, 
              de.`IfAccess`,
              (select p.`SerialNo` from `APP_Function` as p where AFUN.`ParentFunctionId` = p.`FunctionId`) as ParentSerialNo 
              from `APP_Function` as AFUN
              left join `APP_Function_Permission` as de
              on AFUN.`FunctionId` = de.`FunctionId` 
              and de.`UserTypeID` = '.$adminDegree->MemberType.' 
              and de.`UserDegreeID` = '.$adminDegree->DegreeId.' 
              where AFUN.`IfDelete` = 0 
              and AFUN.`IfValid` = 1 
              and AFUN.`ParentFunctionId` is not null 
              order by ParentSerialNo asc, AFUN.`SerialNo` asc';

      $query = DB::select(DB::raw($sql));
      // dd($query);

      if(count($query) > 0){
        $returnArr = array();
        foreach($query as $permission){
            $returnArr[] = array(
                                    'UserType' => $adminDegree->MemberType,
                                    'UserDegreeID' => $adminDegree->DegreeId,
                                    'FunctionId' => $permission->FunctionId,
                                    'IfAccess' => $permission->IfAccess,
                                    'MenuOrder' => $permission->SerialNo,
                                    'FunctionName' => $permission->FunctionName,
                                    'ParentName' => $permission->ParentName,
                                    'PermissionId' => $permission->PermissionId
                                  );
        }
        $responseBody['data'] = $returnArr;
      }else{
        $responseBody['data'] = array();
      }

      return Response::json($responseBody, 200);
    }

    //permission list 開啟功能
    public function TurnOn(Request $request){
      $responseBody = array(
        'status' => 0,
        'message' => 'Unknown Error',
      );

      $UserTypeId = $request->has('UserTypeId') ? $request->input('UserTypeId') : '';
      $UserDegreeId = $request->has('UserDegreeId') ? $request->input('UserDegreeId') : '';
      $FunctionId = $request->input('FunctionId') ? $request->input('FunctionId') : '';
      $PermissionId = $request->input('PermissionId') ? $request->input('PermissionId') : '';

      //check UserTypeId
      if($UserTypeId == ''){
        $responseBody['status'] = 1;
        $responseBody['message'] = 'UserTypeId does not exist';
      }

      //check UserDegreeId
      if($responseBody['status'] == 0){
        if($UserDegreeId == ''){
          $responseBody['status'] = 1;
          $responseBody['message'] = 'UserDegreeId does not exist';
        }
      }

      //check FunctionId
      if($responseBody['status'] == 0){
        if($FunctionId == ''){
          $responseBody['status'] = 1;
          $responseBody['message'] = 'FunctionId does not exist';
        }
      }

      //for test
      $responseBody['UserTypeId'] = $UserTypeId;
      $responseBody['UserDegreeId'] = $UserDegreeId;
      $responseBody['FunctionId'] = $FunctionId;
      $responseBody['PermissionId'] = $PermissionId;

      if($responseBody['status'] == 0){
        $permission = APPFunctionPermission::find($PermissionId);

        $tokenId = $request->header('Authorization') ? $request->header('Authorization') : '';
        $token = UserProcessTicket::find($tokenId);
        $staff_id = '';

        if (isset($token->user->Id)) {
            $staff_id = $token->user->Id;
        }else{
          $responseBody['status'] = 1;
          $responseBody['message'] = 'Token does not exist';
          return Response::json($responseBody, 200);
        }
      }

      if($permission){
        //set enable
        $permission->IfAccess = 1;
        $permission->save();
        $responseBody['message'] = 'Updated Succesfully';
      }else{
        $permissions = APPFunctionPermission::where('FunctionId', $FunctionId)
                                              ->where('UserTypeId', $UserTypeId)
                                              ->where('UserDegreeId', $UserDegreeId);
        if($permissions->count() > 0){
          if($permissions->count() == 1){
            $permission = $permissions->get()->first();
            $permission->save();

            $responseBody['message'] = 'Updated Succesfully';
          }else{
            //異常情況
            $responseBody['status'] = 1;
            return Response::json($responseBody, 200);
          }
        }else{
          //create PermissionId
          $id = Uuid::generate(4);
          $check = APPFunctionPermission::find($id);
          while($check){
            $id = Uuid::generate(4);
            $check = APPFunctionPermission::find($id);
          }
          // 組成目前需要新增的資料物件;
          $newData = [
            'PermissionID' => $id,
            'FunctionID' =>  $FunctionId,
            'UserTypeID' => $UserTypeId,
            'UserDegreeID' => $UserDegreeId,
            'IfAccess' => 1,
            'CreateBy' => $staff_id,
            'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString() //date("Y-m-d H:i:s")             //'表示為目前時間;
          ];

          // 執行產生資料的動作
          APPFunctionPermission::create($newData);
          $responseBody['message'] = 'Updated Succesfully';
        }
      }
      return Response::json($responseBody, 200);
    }

    //permission list 關閉功能
    public function TurnOff(Request $request){
      $responseBody = array(
        'status' => 0,
        'message' => 'Unknown Error',
      );
      
      $tokenId = $request->header('Authorization') ? $request->header('Authorization') : '';
      $token = UserProcessTicket::find($tokenId);
      $staff_id = '';

      if (isset($token->user->Id)) {
          $staff_id = $token->user->Id;
      }else{
        $responseBody['status'] = 1;
        $responseBody['message'] = 'Token does not exist';
        return Response::json($responseBody, 200);
      }

      $PermissionId = $request->input('PermissionId') ? $request->input('PermissionId') : '';
      $permission = APPFunctionPermission::find($PermissionId);

      if (!$permission) {
        $responseBody['status'] = 1;
        $responseBody['message'] = 'Permission does not exist';
        return Response::json($responseBody, 200);
      }

      $permission->IfAccess = 0;
      $permission->save();

      $responseBody['message'] = 'Updated Succesfully';

      return Response::json($responseBody, 200);
    }
}
