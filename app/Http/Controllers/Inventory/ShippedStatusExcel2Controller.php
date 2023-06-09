<?php
namespace App\Http\Controllers\Inventory;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Uuid;
use Response;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;
use App\Imports\UsersImport;
use App\Model\LinxdotExcelWarehouseInventoryImport;
use App\Model\LinxdotExcelWarehouseInventoryDetail;
use App\Model\LinxdotExcelWarehouseInventoryLog;
use App\Model\LinxdotWarehouseInventory;
use App\Model\LinxdotFactoryDispatch;

class ShippedStatusExcel2Controller extends Controller
{
    
    public function __construct()
    {
        set_time_limit(6000);
    }
    // 設定blade目錄的位置
    public static $viewPath = "Inventory.ShippedStatusExcel2";
    
    // 設定route目錄的位置
    public static $routePath = "ShippedStatusExcel2";

    // 這個資料表的主要鍵值
    public static $primaryKey = "id";

    // 設定功能主選單名稱名稱
    public static $TOPname = "Inventory";

    // 設定功能名稱
    public static $functionname = "Warehouse Delivery";

    // 設定功能UEL
    public static $functionURL = "/Inventory/ShippedStatus";

    public function create(Request $request)
    {
        $pageNumEachPage = 100;                             // 每頁的基本資料量
        $pageNo = (int) $request->input('Page', '1');       // 目前的頁碼
        $IsNewSearch = $request->input('IfNewSearch', '');  // 是否為新開始搜尋

        // 當按下搜尋的時候，會傳回IfNewSearch = 1; 如果不是，表示空值或是其他數值;
        // 當是其他數值的時候，會依照原來的頁碼去產生回應的頁面;
        if($IsNewSearch != '1') {
            Paginator::currentPageResolver(function () use ($pageNo) {
                return $pageNo;
            });
        }else{
            $pageNo = 1;
        }

        $data = LinxdotExcelWarehouseInventoryImport::where('IfDelete', '0')->orderby('ImportDate','Desc');

        // 分頁
        $data = $data->paginate($pageNumEachPage);

        return view(self::$viewPath.'.create', compact('data'))
            ->with('i', ($pageNo - 1) * $pageNumEachPage)
            ->with('pageNo', $pageNo)
            ->with('pageNumEachPage', $pageNumEachPage)
            ->with('routePath', self::$routePath)
            ->with('Action', "NEW")
            ->with('viewPath', self::$viewPath)
            ->with('functionname', self::$functionname)
            ->with('functionURL', self::$functionURL)
            ->with('TOPname', self::$TOPname);
    }

    public function store(Request $request)
    {
        $file = $request->file('excel_file');
        if ($file) {
            $totle = 0;    // 總筆數
            $extension = $file->getClientOriginalExtension();                   // 副檔名
            $filename = $file->getClientOriginalName();                         // 原始檔名
            $FilePath = 'Import_'.Carbon::now()->format('Y-m-d-H-i-s').'.xlsx'; // 儲存檔名
            $tempPath = $file->getRealPath();                                   // 路徑
            $fileSize = $file->getSize();                                       // 檔案大小

            $this->checkUploadedFileProperties($extension, $fileSize);          // 檢查副檔名及檔案大小
            $location = storage_path('excel/FactoryDispatchImport');            // 組存檔路徑
            $file->move($location, $FilePath);                                  // 把檔案存起來
            $filepath = $location.'/'.$FilePath;                                // 組檔案路徑
            $importData = Excel::toArray(new UsersImport, $filepath);           // 把資料轉成陣列
            // dd($location,count($importData),$importData);
            
            // 把檔案資訊存到主表
            $id = $this->new($FilePath,$filename);
            // dd($id);
            // 把資料存入暫存表
            foreach ($importData as $datas ) {
                foreach ($datas as $key => $data) {
                    // 如果是是標題就跳過
                    if($key != 0){
                        // 檢查有沒有資料
                        if($data[0] != '' && $data[4] != '' && $data[5] != ''){
                            // 總筆數+1
                            $totle += 1;
                            // dd($totle);
                            // 新增到暫存表
                            // 0.IssueDate 1.CartonID 2.OldMac 3.PalletID 4.SkuID 5.DeviceSN 6.IsShipped 7.ShippedDate 8.TrackingNo 9.WarehouseID
                            $MacAddress = $data[2];
                            $MacAddress = strtolower(str_replace("-","",$MacAddress));
                            $MacAddress = strtolower(str_replace(":","",$MacAddress));

                            if($MacAddress != null){
                                if(strlen($MacAddress) != 12){
                                    $IfCompletedImport = 0;
                                    $ImportMemo = '請確認MacAddress長度為12';
                                    $newMacAddress = $data[2];
                                }else{
                                    $IfCompletedImport = -1;
                                    $ImportMemo = '';
                                    $newMacAddress = '';
                                    for ($i=0; $i < 11; $i+=2) { 
                                        $str = substr($MacAddress, $i, 2);
                                        if($i != 10){
                                            $newMacAddress .= $str.":";
                                        }else{
                                            $newMacAddress .= $str;
                                        }
                                    }
                                }
                            }else{
                                
                                $FactoryDispatch = LinxdotFactoryDispatch::where('DeviceSN', $data[5])->select('MacAddress')->first();
                                if($FactoryDispatch != null){
                                    $IfCompletedImport = -1;
                                    $ImportMemo = '';
                                    $newMacAddress = $FactoryDispatch->MacAddress;
                                }else{
                                    $IfCompletedImport = 0;
                                    $ImportMemo = '查無MacAddress';
                                    $newMacAddress = '';
                                }
                            }

                            //把ShippedDate轉成資料庫需要的格式
                            if($data[7] != '' && $data[7] != null){
                                try {
                                    // dd($data[7]);
                                    $ShippedDate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[7]))->format('Y-m-d H:i:s');
                                    // $ShippedDate = $data[7];
                                    // dd($ShippedDate);
                                } catch (Exception $e) {
                                    $ShippedDate = null;
                                    $IfCompletedImport = 0;
                                    $ImportMemo = '日期格式異常，請確認日期格式';
                                    // dd($e);
                                }
                            }else{
                                $ShippedDate = null;
                            }

                            // dd($ShippedDate);
                            if($data[6] == 1){
                                $IfShipped = 1;
                            }else{
                                $IfShipped = 0;
                            }
                            LinxdotExcelWarehouseInventoryDetail::on('mysql2')->create([
                                'ImportID' => $id,
                                'WarehouseID' => $data[9],
                                'SkuID' => $data[4],
                                'PalletId' => $data[3],
                                'CartonId' => $data[1],
                                'DeviceSN' => $data[5],
                                'MacAddress' => $newMacAddress,
                                'IfShipped' => $IfShipped,
                                'ShippedDate' => $ShippedDate,
                                'TrackingNo' => $data[8],
                                'ImportStatus' => 0,
                                'IfCompletedImport' => $IfCompletedImport,
                                'ImportMemo' => $ImportMemo,
                                'IfCompletedImportDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
                                'CreateBy' => WebLib::getCurrentUserID(),
                                'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString()
                            ]);
                        }
                    }
                }                        
            }

            // 把暫存的資料取出來組成陣列，IfCompletedImport=0代表有錯誤
            $DetailData = LinxdotExcelWarehouseInventoryDetail::where('ImportID',$id)->where('IfCompletedImport','!=',0)->get();
            // 找有沒有重複資料
            foreach ($DetailData as $key => $data) {
                // 檢查工廠出貨清單有沒有存在
                $FactoryDispatch = LinxdotFactoryDispatch::where('MacAddress',$data->MacAddress)
                        ->where('DeviceSN',$data->DeviceSN);
                if($FactoryDispatch->count() != 1){
                    LinxdotExcelWarehouseInventoryDetail::on('mysql2')
                            ->where('id',$data->id)
                            ->update(['IfCompletedImport' => 0,
                                    'ImportStatus' => 1,
                                    'ImportMemo' => '此裝置不在工廠出貨清單內']);
                }else{
                    // dd($DetailData);
                    $Hotspot = LinxdotWarehouseInventory::where('MacAddress',$data->MacAddress)
                            ->where('DeviceSN',$data->DeviceSN);

                    // 如果MAC號跟SN重複                    
                    if($Hotspot->count() > 0){
                        $memo = '';
                        $Hotspot = $Hotspot->first();
                        $newdata = [
                            'MacAddress' => $Hotspot->MacAddress,
                            'DeviceSN' => $Hotspot->DeviceSN,
                            'SkuID' => $Hotspot->SkuID, 
                            'IfShipped' => $Hotspot->IfShipped,
                            'ShippedDate' => $Hotspot->ShippedDate,
                            'CustomInfo' => $Hotspot->CustomInfo,
                            'NewSkuID' => $data->SkuID,
                            'NewIfShipped' => $data->IfShipped,
                            'NewShippedDate' => $data->ShippedDate,
                            'NewCustomInfo' => $data->TrackingNo,
                            'CreateBy' => WebLib::getCurrentUserID(),
                            'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString()
                        ];
                        LinxdotExcelWarehouseInventoryLog::on('mysql2')->create($newdata);

                        $updata = [
                            'SkuID' => $data->SkuID,
                            'IfShipped' => $data->IfShipped,
                            'ShippedDate' => $data->ShippedDate,
                            'CustomInfo' => $data->TrackingNo,
                            'LastModifyBy' => WebLib::getCurrentUserID(),
                            'LastModifyDate' => Carbon::now('Asia/Taipei')->toDateTimeString()
                        ];
                        LinxdotWarehouseInventory::on('mysql2')->where('MacAddress',$data->MacAddress)
                            ->where('DeviceSN',$data->DeviceSN)->update($updata);

                        if($Hotspot->IfShipped == 0 && $data->IfShipped == 1){
                            $memo = '資料重複，需更新Dewi';
                        }else{
                            $memo = '資料重複，已更新狀態';
                        }

                        LinxdotExcelWarehouseInventoryDetail::on('mysql2')
                                ->where('id',$data->id)
                                ->update(['IfCompletedImport' => 1,
                                        'ImportStatus' => 1,
                                        'ImportMemo' => $memo]);
                    }else{
                        $HotspotMac = LinxdotWarehouseInventory::where('MacAddress',$data->MacAddress)
                                                ->where('DeviceSN','!=',$data->DeviceSN);
                        $HotspotSN = LinxdotWarehouseInventory::where('DeviceSN',$data->DeviceSN)
                                                ->where('MacAddress','!=',$data->MacAddress);
                        // 如果MAC重複S/N不一樣
                        if($HotspotMac->count() > 0){
                            LinxdotExcelWarehouseInventoryDetail::on('mysql2')
                                    ->where('id',$data->id)
                                    ->update(['IfCompletedImport' => 0,
                                            'ImportStatus' => 1,
                                            'ImportMemo' => 'MacAddress重複，請確認資料']);
                        }elseif($HotspotSN->count() > 0){
                            // 如果S/N重複MAC不一樣
                            LinxdotExcelWarehouseInventoryDetail::on('mysql2')
                                    ->where('id',$data->id)
                                    ->update(['IfCompletedImport' => 0,
                                            'ImportStatus' => 1,
                                            'ImportMemo' => 'DeviceSN重複，請確認資料']);
                        }else{
                            // 不重複的資料，檢查MacAddress格式是否正確
                            $newdata = [
                                'SkuID' => $data->SkuID,
                                'PalletID' => $data->PalletID,
                                'CatronID' => $data->CartonId,
                                'DeviceSN' => $data->DeviceSN,
                                'MacAddress' => $data->MacAddress,
                                'IfShipped' => $data->IfShipped,
                                'ShippedDate' => $data->ShippedDate,
                                'CustomInfo' => $data->TrackingNo,
                                'WarehouseID' => $data->WarehouseID,
                                'IfValid' => 1,
                                'IfDelete' => 0,
                                'CreateBy' => WebLib::getCurrentUserID(),
                                'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString()
                            ];
                            LinxdotWarehouseInventory::on('mysql2')->create($newdata);
                            $newlog = [
                            'MacAddress' => $data->MacAddress,
                            'DeviceSN' => $data->DeviceSN,
                            
                            'NewSkuID' => $data->SkuID,
                            'NewIfShipped' => $data->IfShipped,
                            'NewShippedDate' => $data->ShippedDate,
                            'NewCustomInfo' => $data->TrackingNo,
                            'CreateBy' => WebLib::getCurrentUserID(),
                            'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString()
                            ];
                            LinxdotExcelWarehouseInventoryLog::on('mysql2')->create($newlog);

                            LinxdotExcelWarehouseInventoryDetail::on('mysql2')
                                    ->where('id',$data->id)
                                    ->update(['IfCompletedImport' => 1,
                                            'ImportStatus' => 1]);
                        }
                    }
                }
            }
            // 修改 LinxdotExcelWarehouseInventoryImport 筆數資料
            $ImportUpdate = ['TotalRecords' => $totle,
                            'IfCompleteImport' => 1];
            LinxdotExcelWarehouseInventoryImport::on('mysql2')
                    ->where('id',$id)
                    ->update($ImportUpdate);
        }
        return redirect()->route(self::$routePath.'.create')
                        ->with('success','成功新增一筆資料！');
    }
    // 檢查檔案格式(副檔名、大小)
    public function checkUploadedFileProperties($extension, $fileSize)
    {
        $valid_extension = array("csv", "xlsx","xls"); //Only want csv and excel files
        $maxFileSize = 2147483648; // Uploaded file size limit is 2GB
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
            } else {
                throw new \Exception('No file was uploaded', Response::HTTP_REQUEST_ENTITY_TOO_LARGE); //413 error
            }
        } else {
            throw new \Exception('Invalid file extension', Response::HTTP_UNSUPPORTED_MEDIA_TYPE); //415 error
        }
    }

    // 把excel檔案資料存到資料表
    protected function new($FilePath,$filename)
    {
        return LinxdotExcelWarehouseInventoryImport::on('mysql2')->insertGetId([
            'ImportDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
            'FilePath' => 'storage\excel\FactoryDispatchImport\\'.$FilePath,
            'FileName' => $filename,
            'IfCompleteImport' => 0,
            'TotalRecords' => 0,
            'CompletedStatus' => 0,
            'IfValid' => 1,
            'IfDelete' => 0,
            'CreateBy' => WebLib::getCurrentUserID(),
            'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString()
        ]);
    }
    // 把excel檔案資料存到資料表
    protected function WarehouseInventoryDetail(Request $request)
    {
        $responseBody = array(
          'status' => 0,
          'errorCode' => '9999',
          'message' => 'Unknown error.',
          'data' => [],
        );

        $ID = $request->input('ID', '');
        $data = LinxdotExcelWarehouseInventoryDetail::select(
            'Linxdot_Excel_WarehouseInventory_Detail.IfCompletedImport',
            'Linxdot_Excel_WarehouseInventory_Detail.ImportStatus',
            'Linxdot_Excel_WarehouseInventory_Detail.SkuID',
            'Linxdot_Excel_WarehouseInventory_Detail.PalletId',
            'Linxdot_Excel_WarehouseInventory_Detail.CartonId',
            'Linxdot_Excel_WarehouseInventory_Detail.DeviceSN',
            'Linxdot_Excel_WarehouseInventory_Detail.IfShipped',
            'Linxdot_Excel_WarehouseInventory_Detail.ShippedDate',
            'Linxdot_Excel_WarehouseInventory_Detail.TrackingNo',
            'Linxdot_Excel_WarehouseInventory_Detail.IfCompletedImport',
            'Linxdot_Excel_WarehouseInventory_Detail.ImportStatus',
            'Linxdot_Excel_WarehouseInventory_Detail.ImportMemo',
            'Dim_Hotspot.IsRegisteredDewi','Dim_Hotspot.LastRegisterDewiMemo','Dim_Hotspot.LastRegisterDewiDate','Dim_Hotspot.LastRegisterDewiStatus')
        ->leftJoin('Dim_Hotspot', function($join){
            $join->on('Dim_Hotspot.DeviceSN','=','Linxdot_Excel_WarehouseInventory_Detail.DeviceSN')
                ->where('Dim_Hotspot.IfValid' , 1)
                ->where('Dim_Hotspot.IfDelete' , 0);
        })
        ->where('Linxdot_Excel_WarehouseInventory_Detail.ImportID',$ID)->get();
        // dd($data);

        $responseBody['status'] = 0;
        $responseBody['message'] = 'change success!';
        $responseBody['errorCode'] = '0000';
        $responseBody['data'] = $data;

        return Response::json($responseBody, 200);
    }
}