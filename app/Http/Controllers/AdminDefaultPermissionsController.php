<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use App\Model\UserProcessTicket;
use App\Model\AdminDefaultPermission;
use App\Model\AdminFunction;
use App\Model\DimUser;
use App\Model\DimUserType;
use App\Model\DimUserDegreeToUserType;
use Response;
use DB;
use Uuid;

class AdminDefaultPermissionsController extends Controller
{
    private function transformCollection($adminFunctions)
    {
        //$tasksArray = $tasks->toArray();
        $rows = array();
        foreach ($adminFunctions as $adminFunction) {
            //get MainList
            if (!isset($adminFunction->ParentFunctionId)) {
                $rows[] = array(
                  // 'FunctionId' => $adminFunction->FunctionId,
                  'Name' => $adminFunction->FunctionName,
                  'Code' => ((!is_null($adminFunction->FunctionCode)) ? $adminFunction->FunctionCode : '&#xE871;'),
                  'Url' => $adminFunction->FunctionURL,
                  'MenuOrder' => $adminFunction->MenuOrder,
                  'IfValid' => $adminFunction->IfValid,
                  'SubList' => $this->transform($adminFunction->FunctionId, $adminFunctions),
                  'ParentFunctionId' => $adminFunction->ParentFunctionId,
                  // 'IfAccess' => $adminFunction->IfAccess
                );
            }
        }

        return [
        // 'recordsTotal' => $adminDefaultPermissions->total(),
        // 'recordsFiltered' => $adminDefaultPermissions->total(),
        // 'current' => $roles->currentPage(),
        // 'length' => $adminDefaultPermissions->perPage(),
        'data' => $rows,
     ];
    }

    private function transform($ParentFunctionId,$adminFunctions)
    {
        $sublist = array();
        foreach ($adminFunctions as $adminFunction) {
          if ($ParentFunctionId == $adminFunction->ParentFunctionId ) {
            if($adminFunction->IfAccess){
              $sublist[] = array(
                // 'FunctionId' => $adminFunction->FunctionId,
                'Name' => $adminFunction->FunctionName,
                'Url' => $adminFunction->FunctionURL,
                // 'MenuOrder' => $adminFunction->MenuOrder,
                'IfValid' => $adminFunction->IfValid,
                'ParentFunctionId' => $adminFunction->ParentFunctionId,
                'IfAccess' => $adminFunction->IfAccess
              );
            }
          }
        }


        return $sublist;
    }


    public function index(Request $request)
    {
        $limit = $request->input('length') ? $request->input('length') : 10;
        $current = $request->input('start') ? $request->input('start') : 0;
        // $search = $request->input('search') ? $request->input('search')['value'] : '';

        // Make sure that you call the static method currentPageResolver()
        // before querying users
        Paginator::currentPageResolver(function () use ($current) {
            return $current;
        });

        $UserProcessTicketId = $request->header('Authorization') ? $request->header('Authorization') : '';

        $UserProcessTicketId = UserProcessTicket::find($UserProcessTicketId);
        $user = '';
        if (isset($UserProcessTicketId)) {
            $user = DimUser::find($UserProcessTicketId->UID);
        }

        $adminDefaultPermissions = AdminDefaultPermission::where('UserTypeId', $user->UserType)
                                            ->where('UserDegreeId', $user->DegreeId);

        $count = $adminDefaultPermissions->count();
        $adminDefaultPermissions = $adminDefaultPermissions->paginate($count);

        $responseBody['status'] = 1;
        $responseBody['message'] = '';
        $responseBody['data'] = $this->transformCollection($adminDefaultPermissions);

        return Response::json($responseBody, 200);
    }


    //傳入 token 取得主選單的列表
    public function show($id)
    {
        $responseBody = array(
                'status' => 0,
                'message' => 'Unknown Error',
        );
        $user = '';
        $userType = '';
        $userDegree = '';
        $userMemberNo = '';

        $UserProcessTicketId = UserProcessTicket::find($id);

        if (isset($UserProcessTicketId)) {
            $user = DimUser::find($UserProcessTicketId->UID);
        }else{
          $responseBody['status'] = 1;
          $responseBody['message'] = 'token invalid';
          return Response::json($responseBody, 401);
        }

        if(!isset($user)){
          $responseBody['status'] = 1;
          $responseBody['message'] = 'User not exist';
          return Response::json($responseBody, 409);
        }

        $userMemberNo = $user->MemberNo;
        $userType = $user->userType->UserTypeName;
        $userDegreeType = DimUserDegreeToUserType::where('UserType', $user->UserType)
                                            ->where('DegreeId', $user->DegreeId)
                                            ->where('IfValid', 1)
                                            ->where('IfDelete', 0);
        if($userDegreeType->count() == 1){
          $userDegree = $userDegreeType->first()->DegreeName;
        }else{
          $responseBody['status'] = 1;
          $responseBody['message'] = 'UserDegree confict';
          return Response::json($responseBody, 409);
        }

        $searchArr = array(
          'UserTypeId' => $user->UserType,
          'DegreeId' => $user->DegreeId
        );

        $adminFunctions = DB::table('ADM_Function')
                              ->LeftJoin('ADM_DefaultPermission',
                              function($join) use ($searchArr){
                                $join->on('ADM_Function.FunctionId', '=', 'ADM_DefaultPermission.FunctionId')
                                  ->where('ADM_DefaultPermission.UserTypeId','=', $searchArr['UserTypeId'])
                                  ->where('ADM_DefaultPermission.UserDegreeId','=', $searchArr['DegreeId']);
                                }
                              )->where('ADM_Function.IfValid', 1)
                              ->where('ADM_Function.IfDelete', 0)
                              ->orderBy('ADM_Function.ParentFunctionId','asc')
                              ->orderBy('ADM_Function.MenuOrder','asc')
                              ->select(
                                'ADM_Function.FunctionId',
                                'ADM_Function.FunctionName',
                                'ADM_Function.FunctionCode',
                                'ADM_Function.MenuOrder',
                                'ADM_Function.IfValid',
                                'ADM_Function.FunctionURL',
                                'ADM_Function.ParentFunctionId',
                                'ADM_DefaultPermission.IfAccess'
                                )->get();
        // $count = $adminFunctions->count();
        // $adminFunctions = $adminFunctions->paginate($count);
        $responseBody['data'] = $this->transformCollection($adminFunctions);
        

        $responseBody['status'] = 0;
        $responseBody['message'] = '';
        $responseBody['data']['MemberNo'] = $userMemberNo;
        $responseBody['data']['MemberName'] = $user->RealName;//for 登入後顯示
        $responseBody['data']['UserType'] = $user->UserType;
        $responseBody['data']['TypeName'] = $userType;
        $responseBody['data']['DegreeName'] = $userDegree;
        return Response::json($responseBody, 200);
    }

    public function store(Request $request){
      if ($request->input('action') == "getPermissonlist") {
            return    $this->getPermissonlist($request);
        }

        $responseBody = array(
          'status' => 0,
          'message' => 'Unknown Error',
        );

        if(
          !$request->has('functionId')
          or !$request->has('userTypeId')
          or !$request->has('userDegreeId')
          or !$request->has('ifAccess')
        ){
          $responseBody['message'] = 'Please Provide Data';
          return Response::json($responseBody, 422);
        }

        $tokenId = $request->header('Authorization') ? $request->header('Authorization') : '';

        $token = UserProcessTicket::find($tokenId);

        $staff_id = '';

        if (isset($token->user->Id)) {
            $staff_id = $token->user->Id;
        }else{
          $responseBody['message'] = 'Token does not exist';
          return Response::json($responseBody, 401);
        }

        $permissionFunctionId = $request->input('functionId') ? $request->input('functionId') : '';
        $permissionUserTypeId = $request->input('userTypeId') ? $request->input('userTypeId') : '';
        $permissionUserDegreeId = $request->input('userDegreeId') ? $request->input('userDegreeId') : '';
        $permissionIfAccess = $request->input('ifAccess') ? $request->input('ifAccess') : 0;

        $count = AdminDefaultPermission::where('FunctionId', $permissionFunctionId)
                                        ->where('UserTypeId', $permissionUserTypeId)
                                        ->where('UserDegreeId', $permissionUserDegreeId)
                                        ->count();
        if($count == 0){
          $id = Uuid::generate(4);
          $check = AdminDefaultPermission::find($id);
          while($check){
            $id = Uuid::generate(4);
            $check = AdminDefaultPermission::find($id);
          }
          //add permission
          $storeArray = array(
            "PermissionId" => Uuid::generate(4),
            "UserTypeId" => $permissionUserTypeId,
            "UserDegreeId" => $permissionUserDegreeId,
            "FunctionId" => $permissionFunctionId,
            "IfAccess" => $permissionIfAccess,
            "CreateBy" => $staff_id,
            "CreateDate" => Carbon::now('Asia/Taipei')->toDateTimeString(),
          );
          $permission = AdminDefaultPermission::create($storeArray);

          $responseBody['status'] = 1;
          $responseBody['message'] = 'Created Succesfully';

          return Response::json($responseBody, 200);
        }else{
          dd(123);


        }
    }

    //後台pemission管理功能的列表
    private function getPermissonlist(Request $request){
      $responseBody = array(
        'status' => 0,
        'message' => 'Unknown Error',
      );

      $limit = $request->input('length') ? $request->input('length') : 10;
      $current = $request->input('start') ? $request->input('start') : 0;
      // $search = $request->input('search') ? $request->input('search')['value'] : '';

      $current = $current / $limit + 1;

      $adminUserTypeId = $request->input('functionUserTypeId') ? $request->input('functionUserTypeId') : '';
      $adminDegreeId = $request->input('functionDegreeId') ? $request->input('functionDegreeId') : '';
      // Make sure that you call the static method currentPageResolver()
      // before querying users
      Paginator::currentPageResolver(function () use ($current) {
          return $current;
      });

      $UserProcessTicketId = $request->header('Authorization') ? $request->header('Authorization') : '';

      $UserProcessTicketId = UserProcessTicket::find($UserProcessTicketId);
      $user = '';
      if (isset($UserProcessTicketId)) {
          $user = DimUser::find($UserProcessTicketId->UID);
      }

      // $adminDegree = DimUserDegreeToUserType::find($adminDegreeUTID);

      // if(!$adminDegree){
      //   // $responseBody['status'] = 0;
      //   // $responseBody['message'] = 'UserDegree not found';
      //   // return Response::json($responseBody, 404);
      //   $retrunArr = array(
      //                   'recordsTotal' => 0,
      //                   'recordsFiltered' => 0,
      //                   'length' => 10,
      //                   'data' => []
      //                 );
      //   return Response::json($retrunArr, 404);
      // }
      $searchArr = array(
        'UserTypeId' => $adminUserTypeId,
        'DegreeId' => $adminDegreeId
      );

      $adminDefaultPermissions = DB::table('ADM_Function')
                                  ->LeftJoin('ADM_DefaultPermission',
                                  function($join) use ($searchArr){
                                    $join->on('ADM_Function.FunctionId', '=', 'ADM_DefaultPermission.FunctionId')
                                      ->where('ADM_DefaultPermission.UserTypeId','=', $searchArr['UserTypeId'])
                                      ->where('ADM_DefaultPermission.UserDegreeId','=', $searchArr['DegreeId']);
                                    }
                                  );

                                  // ->where('UserTypeId', $adminDegee->UserType)
                                  // ->where('UserDegreeId', $adminDegee->DegreeId)
      $adminDefaultPermissions =$adminDefaultPermissions->where('ADM_Function.IfDelete', 0)
                                  ->whereNotNull('ADM_Function.ParentFunctionId')
                                  ->orderBy('ADM_Function.ParentFunctionId','asc')
                                  ->orderBy('ADM_Function.MenuOrder','asc')
                                  ->select(
                                    'ADM_Function.FunctionId',
                                    'ADM_Function.FunctionName',
                                    'ADM_Function.FunctionDesc',
                                    'ADM_Function.MenuOrder',
                                    'ADM_Function.IfValid',
                                    'ADM_Function.FunctionURL',
                                    'ADM_Function.ParentFunctionId',
                                    'ADM_DefaultPermission.PermissionId',
                                    'ADM_DefaultPermission.UserTypeId',
                                    'ADM_DefaultPermission.UserDegreeId',
                                    'ADM_DefaultPermission.IfAccess'
                                    );
      $count = $adminDefaultPermissions->count();
      if($count > 0){
        $adminDefaultPermissions = $adminDefaultPermissions->paginate($count);
      }else{
        $retrunArr = array(
                        'recordsTotal' => 0,
                        'recordsFiltered' => 0,
                        'length' => 10,
                        'data' => []
                      );
        return Response::json($retrunArr, 200);
      }

      return Response::json($this->transformPermissionCollection($adminDefaultPermissions), 200);
    }

    private function transformPermissionCollection($adminDefaultPermissions)
    {
        $adminDefaultPermissionsArray = $adminDefaultPermissions->toArray();
        return [
        'recordsTotal' => $adminDefaultPermissionsArray['total'],
        'recordsFiltered' => $adminDefaultPermissionsArray['total'],
        'length' => $adminDefaultPermissionsArray['per_page'],
        'data' => array_map([$this, 'transformPermission'], $adminDefaultPermissionsArray['data'])
     ];
    }

    private function transformPermission($permission){
      // print_r($permission);
      if($permission->ParentFunctionId) $parentFunction = AdminFunction::find($permission->ParentFunctionId);
      return [
          'FunctionId' => $permission->FunctionId,
          'IfAccess' => $permission->IfAccess,
          'MenuOrder' => $permission->MenuOrder,
          'ParentFunctionName' => $parentFunction->FunctionName,
          'FunctionName' => $permission->FunctionName,
          'IfValid' => $permission->IfValid,
          'IfAccess' => $permission->IfAccess,
          'PermissionId' => $permission->PermissionId,
      ];
    }

    //permission list 關閉功能
    public function update(Request $request, $id){
      $responseBody = array(
        'status' => 0,
        'message' => 'Unknown Error',
      );

      if(
        !$request->has('editIfAccess')
      ){
        $responseBody['message'] = 'Please Provide Data';
        return Response::json($responseBody, 422);
      }

      $permissionIfAccess = $request->input('editIfAccess') ? $request->input('editIfAccess') : 0;

      if($permissionIfAccess != 1) $permissionIfAccess = 0;
      $permission = AdminDefaultPermission::find($id);

      if (!$permission) {
          $responseBody['message'] = 'Permission does not exist';

          return Response::json($responseBody, 404);
      }

      $tokenId = $request->header('Authorization') ? $request->header('Authorization') : '';
      $token = UserProcessTicket::find($tokenId);
      $staff_id = '';

      if (isset($token->user->Id)) {
          $staff_id = $token->user->Id;
      }else{
        $responseBody['message'] = 'Token does not exist';
        return Response::json($responseBody, 401);
      }

      $permission->IfAccess = $permissionIfAccess;
      $permission->save();

      $responseBody['status'] = 1;
      $responseBody['message'] = 'Updated Succesfully';

      return Response::json($responseBody, 200);
    }
}
