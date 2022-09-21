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
            return view('/Default');
        }
    }

    // 取得Firmware清單
    protected function showFirmwareList(Request $request)
    {
        $responseBody = array(
          'status' => 0,
          'errorCode' => '9999',
          'message' => 'Unknown error.',
          'data' => [],
        );

        $Name = $request->input('Name', '');
        // dd($Name,$Firmware);
        $data = DimHotspot::where('Dim_Hotspot.IfDelete','0')
                ->leftJoin('Linxdot_Factory_Dispatch', function($join){
                    $join->on('Dim_Hotspot.MacAddress','=','Linxdot_Factory_Dispatch.MacAddress')
                        ->where('Linxdot_Factory_Dispatch.IfValid' , 1)
                        ->where('Linxdot_Factory_Dispatch.IfDelete' , 0);
                })
                ->leftJoin('Dim_ProductModel', function($join){
                    $join->on('Linxdot_Factory_Dispatch.HWModelNo','=','Dim_ProductModel.ModelID')
                        ->where('Dim_ProductModel.IfValid' , 1)
                        ->where('Dim_ProductModel.IfDelete' , 0);
                })
                ->leftJoin('Dim_Firmware', function($join){
                    $join->on('Dim_Hotspot.Firmware','=','Dim_Firmware.Version Code')
                        ->where('Dim_Firmware.IfValid' , 1)
                        ->where('Dim_Firmware.IfDelete' , 0);
                })
                ->leftJoin('Linxdot_Warehouse_Inventory', function($join){
                    $join->on('Dim_Hotspot.MacAddress','=','Linxdot_Warehouse_Inventory.MacAddress')
                        ->where('Linxdot_Warehouse_Inventory.IfValid' , 1)
                        ->where('Linxdot_Warehouse_Inventory.IfDelete' , 0);
                })
                ->select('Dim_Hotspot.*','Dim_ProductModel.ModelName','Linxdot_Factory_Dispatch.HWModelNo','Dim_Firmware.VersionNo','Linxdot_Warehouse_Inventory.IfShipped','Linxdot_Warehouse_Inventory.CustomInfo','Linxdot_Warehouse_Inventory.ShippedDate');
        if($Name != 'others'){
            if($Name != 'null'){
                $Firmware = DimFirmware::select('Version Code')->where('VersionNo',$Name)->where('IfDelete',0)->first();
                if($Firmware != null){
                    $Version = 'Version Code';
                    $Firmware = $Firmware->$Version;
                }else{
                    $Firmware = $Name;
                }
                $data = $data->where('Firmware',$Firmware)->get();
            }else{
                $data = $data->where('Firmware',null)->get();
            }
        }else{
            $sql = "SELECT a.Firmware, b.VersionNo, a.TotalCount FROM Linxdot_Firmware_Status AS a LEFT JOIN Dim_Firmware AS b ON a.Firmware = b.`Version Code` WHERE RecordDate IN (SELECT MAX(RecordDate) FROM Linxdot_Firmware_Status) ORDER BY a.TotalCount desc;";
            $datas = DB::select($sql);
            $Firmwares = [];
            foreach ($datas as $key => $value) {
                if($key >= 5){
                    if($value->VersionNo == null){
                        $Firmwares[$key] = $value->Firmware;
                    }else{
                        $Firmwares[$key] = $value->Firmware;
                    }
                }
            }
            foreach ($Firmwares as $key => $value) {
                if($key == 5){
                    $data = $data->where('Firmware',$value);
                }else{
                    $data = $data->orwhere('Firmware',$value);
                }
            }
            $data = $data->get();
        }
        // dd($data);


        $responseBody['status'] = 0;
        $responseBody['message'] = 'change success!';
        $responseBody['errorCode'] = '0000';
        $responseBody['data'] = $data;

        return Response::json($responseBody, 200);
    }

    // 取得Firmware清單
    protected function showMinerList(Request $request)
    {
        $responseBody = array(
          'status' => 0,
          'errorCode' => '9999',
          'message' => 'Unknown error.',
          'data' => [],
        );

        $Name = $request->input('Name', '');
        // dd($Name,$Firmware);
        $data = DimHotspot::where('Dim_Hotspot.IfDelete','0')
                ->leftJoin('Linxdot_Factory_Dispatch', function($join){
                    $join->on('Dim_Hotspot.MacAddress','=','Linxdot_Factory_Dispatch.MacAddress')
                        ->where('Linxdot_Factory_Dispatch.IfValid' , 1)
                        ->where('Linxdot_Factory_Dispatch.IfDelete' , 0);
                })
                ->leftJoin('Dim_ProductModel', function($join){
                    $join->on('Linxdot_Factory_Dispatch.HWModelNo','=','Dim_ProductModel.ModelID')
                        ->where('Dim_ProductModel.IfValid' , 1)
                        ->where('Dim_ProductModel.IfDelete' , 0);
                })
                ->leftJoin('Dim_Firmware', function($join){
                    $join->on('Dim_Hotspot.Firmware','=','Dim_Firmware.Version Code')
                        ->where('Dim_Firmware.IfValid' , 1)
                        ->where('Dim_Firmware.IfDelete' , 0);
                })
                ->leftJoin('Linxdot_Warehouse_Inventory', function($join){
                    $join->on('Dim_Hotspot.MacAddress','=','Linxdot_Warehouse_Inventory.MacAddress')
                        ->where('Linxdot_Warehouse_Inventory.IfValid' , 1)
                        ->where('Linxdot_Warehouse_Inventory.IfDelete' , 0);
                })
                ->select('Dim_Hotspot.*','Dim_ProductModel.ModelName','Linxdot_Factory_Dispatch.HWModelNo','Dim_Firmware.VersionNo','Linxdot_Warehouse_Inventory.IfShipped','Linxdot_Warehouse_Inventory.CustomInfo','Linxdot_Warehouse_Inventory.ShippedDate');
        if($Name != 'others'){
            if($Name != 'null'){
                $data = $data->where('MinerVersion',$Name)->get();
            }else{
                $data = $data->where('MinerVersion',null)->get();
            }
        }else{
            $sql = "SELECT a.MinerVersion,a.TotalCount FROM Linxdot_MinerVersion_Status AS a WHERE RecordDate IN (SELECT MAX(RecordDate) FROM Linxdot_MinerVersion_Status) ORDER BY a.TotalCount desc;";
            $datas = DB::select($sql);
            $Miners = [];
            foreach ($datas as $key => $value) {
                if($key >= 5){
                    $Miners[$key] = $value->MinerVersion;
                }
            }
            foreach ($Miners as $key => $value) {
                if($key == 5){
                    $data = $data->where('MinerVersion',$value);
                }else{
                    $data = $data->orwhere('MinerVersion',$value);
                }
            }
            $data = $data->get();
        }


        $responseBody['status'] = 0;
        $responseBody['message'] = 'change success!';
        $responseBody['errorCode'] = '0000';
        $responseBody['data'] = $data;

        return Response::json($responseBody, 200);
    }
    
}