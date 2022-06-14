<?php
namespace App\Http\Controllers\Inventory;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Uuid;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;
use App\Imports\UsersImport;
use App\Model\LinxdotExcelWarehouseInventoryImport;
use App\Model\LinxdotExcelWarehouseInventoryDetail;
use App\Model\LinxdotWarehouseInventory;

class ShippedStatusExcelController extends Controller
{
    // 設定blade目錄的位置
    public static $viewPath = "Inventory.ShippedStatusExcel";
    
    // 設定route目錄的位置
    public static $routePath = "ShippedStatusExcel";

    // 這個資料表的主要鍵值
    public static $primaryKey = "id";

    // 設定功能主選單名稱名稱
    public static $TOPname = "Inventory";

    // 設定功能名稱
    public static $functionname = "ShippedStatus";

    // 設定功能UEL
    public static $functionURL = "/Inventory/ShippedStatus";

    public function create()
    {
        return view(self::$viewPath.'.create')
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
            try {
                DB::beginTransaction();
                $id = $this->new($FilePath,$filename);
                // dd($id);
                // 把資料存入暫存表
                foreach ($importData as $datas ) {
                    foreach ($datas as $key => $data) {
                        // 如果是是標題就跳過
                        if($key != 0){
                            // 檢查有沒有資料
                            if($data[0] != '' && $data[2] != '' && $data[4] != '' && $data[5] != '' && $data[6] != ''){
                                // 總筆數+1
                                $totle += 1;
                                // dd($totle);
                                // 新增到暫存表
                                // 0.IssueDate 1.CartonID 2.OldMac 3.PalletID 4.SkuID 5.DeviceSN 6.IsShipped 7.ShippedDate 8.TrackingNo 9.WarehouseID
                                $MacAddress = $data[2];
                                $MacAddress = strtolower(str_replace("-","",$MacAddress));
                                $MacAddress = strtolower(str_replace(":","",$MacAddress));

                                if(strlen($MacAddress) != 12){
                                    $ImportStatus = 0;
                                    $ImportMemo = '請確認MacAddress長度為12';
                                    $newMacAddress = $data[2];
                                }else{
                                    $ImportStatus = -1;
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
                                //把ShippedDate轉成資料庫需要的格式
                                // dd($data[7]);
                                if($data[7] != '' && $data[7] != null){
                                    $ShippedDate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[7]))->format('Y-m-d H:i:s');
                                }else{
                                    $ShippedDate = null;
                                }
                                // dd($ShippedDate);

                                LinxdotExcelWarehouseInventoryDetail::on('mysql2')->create([
                                    'ImportID' => $id,
                                    'SkuID' => $data[4],
                                    'PalletId' => $data[3],
                                    'CartonId' => $data[1],
                                    'DeviceSN' => $data[5],
                                    'MacAddress' => $newMacAddress,
                                    'IfShipped' => $data[6],
                                    'ShippedDate' => $ShippedDate,
                                    'TrackingNo' => $data[8],
                                    'IfCompletedImport' => 0,
                                    'ImportStatus' => $ImportStatus,
                                    'ImportMemo' => $ImportMemo,
                                    'IfCompletedImportDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
                                    'CreateBy' => WebLib::getCurrentUserID(),
                                    'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString()
                                ]);
                            }
                        }
                    }                        
                }

                // 把暫存的資料取出來組成陣列，ImportStatus=0代表有錯誤
                $DetailData = LinxdotExcelWarehouseInventoryDetail::where('ImportID',$id)->where('ImportStatus','!=',0)->get();
                // 找有沒有重複資料
                foreach ($DetailData as $key => $data) {
                    // dd($DetailData);
                    $Hotspot = LinxdotWarehouseInventory::where('MacAddress',$data->MacAddress)
                            ->where('DeviceSN',$data->DeviceSN);

                    // 如果MAC號跟SN重複                    
                    if($Hotspot->count() > 0){
                        LinxdotExcelWarehouseInventoryDetail::on('mysql2')
                                ->where('id',$data->id)
                                ->update(['IfCompletedImport' => 1,
                                        'ImportStatus' => 0,
                                        'ImportMemo' => '資料重複']);
                    }else{
                        $HotspotMac = LinxdotWarehouseInventory::where('MacAddress',$data->MacAddress)
                                                ->where('DeviceSN','!=',$data->DeviceSN);
                        $HotspotSN = LinxdotWarehouseInventory::where('DeviceSN',$data->DeviceSN)
                                                ->where('MacAddress','!=',$data->MacAddress);
                        // 如果MAC重複S/N不一樣
                        if($HotspotMac->count() > 0){
                            LinxdotExcelWarehouseInventoryDetail::on('mysql2')
                                    ->where('id',$data->id)
                                    ->update(['IfCompletedImport' => 1,
                                            'ImportStatus' => 0,
                                            'ImportMemo' => 'MacAddress重複，請確認資料']);
                        }elseif($HotspotSN->count() > 0){
                            // 如果S/N重複MAC不一樣
                            LinxdotExcelWarehouseInventoryDetail::on('mysql2')
                                    ->where('id',$data->id)
                                    ->update(['IfCompletedImport' => 1,
                                            'ImportStatus' => 0,
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
                                'IfValid' => 1,
                                'IfDelete' => 0,
                                'CreateBy' => WebLib::getCurrentUserID(),
                                'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString()
                            ];
                            LinxdotWarehouseInventory::on('mysql2')->create($newdata);
                            LinxdotExcelWarehouseInventoryDetail::on('mysql2')
                                    ->where('id',$data->id)
                                    ->update(['IfCompletedImport' => 1,
                                            'ImportStatus' => 1]);
                        }
                    }
                }
                // 修改 LinxdotExcelWarehouseInventoryImport 筆數資料
                $ImportUpdate = ['TotalRecords' => $totle,
                                'IfCompleteImport' => 1];
                LinxdotExcelWarehouseInventoryImport::on('mysql2')
                        ->where('id',$id)
                        ->update($ImportUpdate);

                DB::commit();
            } catch (\Exception $e) {
                //throw $th;
                DB::rollBack();
            }
        }
        return redirect()->route(self::$routePath.'.create')
                        ->with('success','成功新增一筆資料！');
    }
    // 檢查檔案格式(副檔名、大小)
    public function checkUploadedFileProperties($extension, $fileSize)
    {
        $valid_extension = array("csv", "xlsx","xls"); //Only want csv and excel files
        $maxFileSize = 2097152; // Uploaded file size limit is 2mb
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
}