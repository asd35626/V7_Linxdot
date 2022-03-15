<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use App\Model\DimUser;
use App\Model\DimUserDegreeToUserType;
use App\Model\AdminDefaultPermission;
use App\Model\UserProcessTicket;
use App\Http\Requests;
use Carbon\Carbon;
use Response;
use Uuid;
use DB;

class SystemPermissionAPIController extends Controller
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
      $UserProcessTicketId = $request->header('Authorization') ? $request->header('Authorization') : '';

      $UserProcessTicketId = UserProcessTicket::find($UserProcessTicketId);
      $user = '';
      if (isset($UserProcessTicketId)) {
          $user = DimUser::find($UserProcessTicketId->UID);
      }

      $id = $request->input('id', '');

      $adminDegree = DimUserDegreeToUserType::find($id);

      if(!$adminDegree){
        $responseBody['status'] = 1;
        $responseBody['message'] = 'UserDegree not found';
        
        return Response::json($responseBody, 200);
      }

      $responseBody['UserTypeId'] = $adminDegree->UserType;
      $responseBody['DegreeId'] = $adminDegree->DegreeId;
      // $searchArr = array(
      //   'UserTypeId' => $adminDegree->UserType,
      //   'DegreeId' => $adminDegree->DegreeId
      // );

      // $adminDefaultPermissions = DB::table('ADM_Functionadm')
      //                             ->LeftJoin('ADM_DefaultPermission',
      //                             function($join) use ($searchArr){
      //                               $join->on('ADM_Function.FunctionId', '=', 'ADM_DefaultPermission.FunctionId')
      //                                 ->where('ADM_DefaultPermission.UserTypeId','=', $searchArr['UserTypeId'])
      //                                 ->where('ADM_DefaultPermission.UserDegreeId','=', $searchArr['DegreeId']);
      //                               }
      //                             );

      //                             // ->where('UserTypeId', $adminDegee->UserType)
      //                             // ->where('UserDegreeId', $adminDegee->DegreeId)
      // $adminDefaultPermissions =$adminDefaultPermissions->where('ADM_Function.IfDelete', 0)
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
      $sql = 'select adm.`FunctionId`, 
              adm.`FunctionName`, 
              adm.`FunctionDesc`, 
              adm.`MenuOrder`,
              (select p.`FunctionName` from `ADM_Function` as p where adm.`ParentFunctionId` = p.`FunctionId`) as ParentName,  
              adm.`IfValid`, 
              adm.`FunctionURL`, 
              adm.`ParentFunctionId`, 
              de.`PermissionId`, 
              de.`UserTypeId`, 
              de.`UserDegreeId`, 
              de.`IfAccess`,
              (select p.`MenuOrder` from `ADM_Function` as p where adm.`ParentFunctionId` = p.`FunctionId`) as ParentMenuOrder 
              from `ADM_Function` as adm
              left join `ADM_DefaultPermission` as de
              on adm.`FunctionId` = de.`FunctionId` 
              and de.`UserTypeId` = '.$adminDegree->UserType.' 
              and de.`UserDegreeId` = '.$adminDegree->DegreeId.' 
              where adm.`IfDelete` = 0 
              and adm.`IfValid` = 1 
              and adm.`ParentFunctionId` is not null 
              order by ParentMenuOrder asc, adm.`MenuOrder` asc';

      $query = DB::select(DB::raw($sql));
      // dd($query);

      if(count($query) > 0){
        $returnArr = array();
        foreach($query as $permission){
            $returnArr[] = array(
                                    'FunctionId' => $permission->FunctionId,
                                    'IfAccess' => $permission->IfAccess,
                                    'MenuOrder' => $permission->MenuOrder,
                                    'FunctionName' => $permission->FunctionName,
                                    'FunctionURL' => $permission->FunctionURL,
                                    'FunctionDesc' => $permission->FunctionDesc,
                                    'ParentName' => $permission->ParentName,
                                    'IfAccess' => $permission->IfAccess,
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
        $permission = AdminDefaultPermission::find($PermissionId);

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
        $update = [
          'IfAccess' => 1,
        ];
        AdminDefaultPermission::on('mysql2')->find($PermissionId)->update($update);

        $responseBody['message'] = 'Updated Succesfully';
      }else{
        $permissions = AdminDefaultPermission::where('FunctionId', $FunctionId)
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
          $check = AdminDefaultPermission::find($id);
          while($check){
            $id = Uuid::generate(4);
            $check = AdminDefaultPermission::find($id);
          }
          // 組成目前需要新增的資料物件;
          $newData = [
            'PermissionId' => $id,
            'FunctionId' =>  $FunctionId,
            'UserTypeId' => $UserTypeId,
            'UserDegreeId' => $UserDegreeId,
            'IfAccess' => 1,
            'CreateBy' => $staff_id,
            'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString() //date("Y-m-d H:i:s")             //'表示為目前時間;
          ];

          // 執行產生資料的動作
          AdminDefaultPermission::on('mysql2')->create($newData);
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
      $permission = AdminDefaultPermission::find($PermissionId);

      if (!$permission) {
        $responseBody['status'] = 1;
        $responseBody['message'] = 'Permission does not exist';
        return Response::json($responseBody, 200);
      }

      $update = [
        'IfAccess' => 0,
      ];
      AdminDefaultPermission::on('mysql2')->find($PermissionId)->update($update);

      $responseBody['message'] = 'Updated Succesfully';

      return Response::json($responseBody, 200);
    }
}
