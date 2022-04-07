<?php
namespace App\Http\Controllers\Device;
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
use App\Model\LinxdotExcelHotspotImport;
use App\Model\LinxdotExcelHotspotDetail;
use App\Model\DimHotspot;

class ExcelController extends Controller
{
    // 設定blade目錄的位置
    public static $viewPath = "Device.Excel";
    
    // 設定route目錄的位置
    public static $routePath = "Excel";

    // 這個資料表的主要鍵值
    public static $primaryKey = "id";

    // 設定功能名稱
    public static $functionname = "Hotspots";

    public function create()
    {
        return view(self::$viewPath.'.create')
            ->with('routePath', self::$routePath)
            ->with('Action', "NEW")
            ->with('viewPath', self::$viewPath)
            ->with('functionname', self::$functionname);
    }

    public function store(Request $request)
    {
        $file = $request->file('excel_file');
        if ($file) {
            $totle = 0;    // 總筆數
            $extension = $file->getClientOriginalExtension();                   // 副檔名
            $filename = $file->getClientOriginalName();                         // 原始檔名
            $FilePath = Carbon::now()->format('m-d-H-i').'.xlsx'; // 儲存檔名
            $tempPath = $file->getRealPath();                                   // 路徑
            $fileSize = $file->getSize();                                       // 檔案大小

            $this->checkUploadedFileProperties($extension, $fileSize);          // 檢查副檔名及檔案大小
            $location = storage_path('excel\upload\\');                         // 組存檔路徑
            $file->move($location, $FilePath);                                  // 把檔案存起來
            $filepath = $location.$FilePath;                                    // 組檔案路徑
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
                            if($data[1] != '' && $data[2] != '' && $data[3] != '' && $data[4] != '' ){
                                // 總筆數+1
                                $totle += 1;
                                // 新增到暫存表
                                // 0.IssueDate 1.Pallet 2.CartoneID 3.SN 4.Mac 5.Firmware 6.IfValid
                                $MacAddress = $data[4];
                                $MacAddress = strtolower(str_replace("-","",$MacAddress));
                                $MacAddress = strtolower(str_replace(":","",$MacAddress));
                                if(strlen($MacAddress) != 12){
                                    $ImportStatus = 0;
                                    $ImportMemo = '請確認MacAddress長度為12';
                                    $newMacAddress = $data[4];
                                }else{
                                    $ImportStatus = 1;
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
                                $IssueDate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($data[0]))->format('Y-m-d H:i:s');

                                LinxdotExcelHotspotDetail::on('mysql2')->create([
                                    'ImportID' => $id,
                                    'IssueDate' => $IssueDate,
                                    'PalletId' => $data[1],
                                    'CartonId' => $data[2],
                                    'DeviceSN' => $data[3],
                                    'MacAddress' => $newMacAddress,
                                    'Firmware' => $data[6],
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

                // 把暫存的資料取出來組成陣列
                $DetailData = LinxdotExcelHotspotDetail::where('ImportID',$id)->where('ImportStatus','!=',0)->get();
                // 找有沒有重複資料
                foreach ($DetailData as $key => $data) {
                    // dd($DetailData);
                    $Hotspot = DimHotspot::where('MacAddress',$data->MacAddress)
                            ->where('DeviceSN',$data->DeviceSN);
                    
                    if($Hotspot->count() > 0){
                        // 重複的資料把IsVerify改成0，Memo資料重複，IsVerify修改為0
                        DimHotspot::on('mysql2')->where('MacAddress',$data->MacAddress)->update(['IsVerify' => 0]);
                        LinxdotExcelHotspotDetail::on('mysql2')
                                ->where('id',$data->id)
                                ->update(['IfCompletedImport' => 1,
                                        'ImportStatus' => 0,
                                        'ImportMemo' => '資料重複，IsVerify修改為0']);
                    }else{
                        // 不重複的資料，檢查MacAddress格式是否正確
                        $newdata = [
                            'IssueDate' => $data->IssueDate,
                            'PalletId' => $data->PalletId,
                            'CartonId' => $data->CartonId,
                            'DeviceSN' => $data->DeviceSN,
                            'MacAddress' => $data->MacAddress,
                            'Firmware' => $data->Firmware,
                            'IsVerify' => 0,
                            'IfValid' => 1,
                            'IfDelete' => 0,
                            'CreateBy' => WebLib::getCurrentUserID(),
                            'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString()
                        ];
                        DimHotspot::on('mysql2')->create($newdata);
                        LinxdotExcelHotspotDetail::on('mysql2')
                                ->where('id',$data->id)
                                ->update(['IfCompletedImport' => 1,
                                        'ImportStatus' => 1]);
                    }
                }
                // 修改 LinxdotExcelHotspotImport 筆數資料
                $ImportUpdate = ['TotalRecords' => $totle,
                                'IfCompleteImport' => 1];
                LinxdotExcelHotspotImport::on('mysql2')
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

    protected function new($FilePath,$filename)
    {
        return LinxdotExcelHotspotImport::on('mysql2')->insertGetId([
            'ImportDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
            'FilePath' => 'storage\excel\upload\\'.$FilePath,
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