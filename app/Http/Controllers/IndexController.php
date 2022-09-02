<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Model\DimUser;
use App\Model\DimHotspot;
use App\Model\DimFirmware;
use App\Model\LinxdotFirmwareStatus;
use Uuid;
use Response;
use Carbon\Carbon;
use App\V7Idea\WebLib;

class IndexController extends Controller
{
    public function index(Request $request)
    {
    	// 取得會員ID
        $UID = WebLib::getCurrentUserID();
        // 取得會員資料並獲得UserType、DegreeId
        $user = DimUser::where('Id',$UID)->select('DegreeId','UserType')->first();
        if($user){
            $UserType = $user->UserType;
            $DegreeId = $user->DegreeId;
            // dd($UID,$UserType,$DegreeId);

            if($UserType == 20 && $DegreeId == 50){
                return redirect()->route('Dashboard.index');
            }else{
                return view('/Default');
            }
        }else{
            return redirect()->route('Admin.Login');
        }
    }

    // // 把excel檔案資料存到資料表
    // protected function showFirmwareList(Request $request)
    // {
    //     $responseBody = array(
    //       'status' => 0,
    //       'errorCode' => '9999',
    //       'message' => 'Unknown error.',
    //       'data' => [],
    //     );

    //     $Name = $request->input('Name', '');
    //     $Firmware = DimFirmware::where('VersionNo',$Name)->get();
    //     if($Name != 'others'){
    //         if($Name != 'null'){
    //             $Version = 'Version Code';
    //             $Firmware = DimFirmware::where('VersionNo',$Name)->where('IfDelete',0)->first();
    //             if($Firmware != null){
    //                 $Firmware = $Firmware->$Version;
    //             }else{
    //                 $Firmware = $Name;
    //             }
    //             $data = DimHotspot::where('Firmware',$Firmware)->where('IfDelete',0)->get();

    //         }else{
    //             $data = DimHotspot::where('Firmware',null)->where('IfDelete',0)->get();
    //         }
    //     }else{
    //         $sql = "SELECT a.Firmware, b.VersionNo, a.TotalCount FROM Linxdot_Firmware_Status AS a LEFT JOIN Dim_Firmware AS b ON a.Firmware = b.`Version Code` WHERE RecordDate IN (SELECT MAX(RecordDate) FROM Linxdot_Firmware_Status) ORDER BY a.TotalCount desc;";
    //         $datas = DB::select($sql);
    //         $Firmwares = [];
    //         foreach ($datas as $key => $value) {
    //             if($key >= 5){
    //                 if($value->VersionNo == null){
    //                     $Firmwares[$key] = $value->Firmware;
    //                 }else{
    //                     $Firmwares[$key] = $value->Firmware;
    //                 }
    //             }
    //         }
    //         $data = DimHotspot::where('IfDelete',0);
    //         foreach ($Firmwares as $key => $value) {
    //             if($key == 5){
    //                 $data = $data->where('Firmware',$value);
    //             }else{
    //                 $data = $data->orwhere('Firmware',$value);
    //             }
    //         }
    //         dd($data->get());
    //     }


    //     $responseBody['status'] = 0;
    //     $responseBody['message'] = 'change success!';
    //     $responseBody['errorCode'] = '0000';
    //     $responseBody['data'] = $data;

    //     return Response::json($responseBody, 200);
    // }
}