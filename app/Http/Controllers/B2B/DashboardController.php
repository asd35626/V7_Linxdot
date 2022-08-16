<?php
namespace App\Http\Controllers\B2B;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Model\DimHotspot;
use Uuid;
use Response;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DashboardController extends Controller
{
    /// <summary>
    /// 檢查權限
    /// </summary>
    function __construct(){
        WebLib::checkUserPermission('Dashboard');
    }
    // 設定blade目錄的位置
    public static $viewPath = "B2B.Dashboard";
    
    // 設定route目錄的位置
    public static $routePath = "Dashboard";

    // 這個資料表的主要鍵值
    public static $primaryKey = "id";
    
    // 設定功能主選單名稱名稱
    public static $TOPname = "B2B";

    // 設定功能名稱
    public static $functionname = "Hotspots";

    // 設定功能名稱
    public static $functionURL = "/B2B/Dashboard";

    // 定義搜尋的欄位設定;
    public function defineSearchFields() {
        $fields = [ 
            'S/N' =>  [
                'name' => 'search',
                'id' => 'search',
                'label' => 'search',
                'type' => 'search',
                'value' => '',
                'class' => 'md-input label-fixed',
            ]
        ];
        return $fields;
    }

    // 定義新增欄位
    public function defineFormFields() {
        $fields = [
            'AnimalName' => [
                'name' => 'AnimalName',
                'id' => 'AnimalName',
                'label' => 'Animal name',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'Subject' => [
                'name' => 'Subject',
                'id' => 'Subject',
                'label' => 'Subject',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'Description' => [
                'name' => 'Description',
                'id' => 'Description',
                'label' => 'Description',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
            ]
        ];
        return $fields;
    }

    public function index(Request $request)
    {
        $pageNumEachPage = 100;                             // 每頁的基本資料量
        $pageNo = (int) $request->input('Page', '1');       // 目前的頁碼
        $IsNewSearch = $request->input('IfNewSearch', '');  // 是否為新開始搜尋
        $IfSearch = $request->input('IfSearch', '');        // 是否為搜尋
        $orderBy = $request->input('orderBy', '');          // 排序欄位
        $isAsc = $request->input('isAsc', '');              // 是否順序排序
        $status = $request->input('status', '');            // 是否順序排序
        $user = WebLib::getCurrentUserID();                 // 使用者ID
        $search = $request->input('search', '');            // 查詢
        $MacAddress = $search;
        $MacAddress = strtolower(str_replace("-","",$MacAddress));
        $MacAddress = strtolower(str_replace(":","",$MacAddress));
        $newMacAddress = '';
        for ($i=0; $i < 11; $i+=2) { 
            $str = substr($MacAddress, $i, 2);
            if($i != 10){
                $newMacAddress .= $str.":";
            }else{
                $newMacAddress .= $str;
            }
        }
        // dd($search);

        // 取得輸入欄位的定義
        $formFieldDef = self::defineFormFields();
        // 產生需要設定的欄位
        $formFields = WebLib::generateInputs($formFieldDef, false)["data"];


        // 產生搜尋的欄位;
        $searchFields = WebLib::generateInputs(self::defineSearchFields(), true)["data"];
        //dd($searchFields);

        // 當按下搜尋的時候，會傳回IfNewSearch = 1; 如果不是，表示空值或是其他數值;
        // 當是其他數值的時候，會依照原來的頁碼去產生回應的頁面;
        if($IsNewSearch != '1') {
            Paginator::currentPageResolver(function () use ($pageNo) {
                return $pageNo;
            });
        }else{
            $pageNo = 1;
        }

        $now = Carbon::now('Asia/Taipei')->subHours(8)->subMinutes(30)->toDateTimeString();
        // dd($now);


        if($status == 1){
            $data = DimHotspot::where('IfDelete','0')->where('OwnerID',$user)->where('LastUpdateOnLineTime', '>=' ,$now);
        }else{
            $data = DimHotspot::where('IfDelete','0')->where('OwnerID',$user);
        }

        if($IfSearch == 1) {
            // 表示會需要參考搜尋的變數
            if($search != '') {
                $data= $data->where(function($query) use ($search,$newMacAddress) {
                    $query->where('DeviceSN', 'like', '%'.$search.'%' )
                        ->orwhere('AnimalName', 'like', '%'.$search.'%' )
                        ->orwhere('MacAddress', 'like', '%'.$newMacAddress.'%' );
                });
                $pageNo = 1;
                $IfSearch = 0;
            }
        }

        //排序
        if(isset($orderBy) && $orderBy != ''){
            if($isAsc == '1'){
                $data = $data->orderBy($orderBy, 'ASC');
            }else{
                $data = $data->orderBy($orderBy, 'DESC');
            }
        }else{
            $data = $data->orderBy('CreateDate');
        }

        // 分頁
        $data = $data->paginate($pageNumEachPage);

        return view(self::$viewPath.'.index', compact('data'))
                    ->with('i', ($pageNo - 1) * $pageNumEachPage)
                    ->with('IfSearch', $IfSearch)
                    ->with('pageNo', $pageNo)
                    ->with('routePath', self::$routePath)
                    ->with('viewPath', self::$viewPath)
                    ->with('primaryKey', self::$primaryKey)
                    ->with('orderBy', $orderBy)
                    ->with('isAsc', $isAsc)
                    ->with('pageNumEachPage', $pageNumEachPage)
                    ->with('search', $search)
                    ->with('functionname', self::$functionname)
                    ->with('functionURL', self::$functionURL)
                    ->with('TOPname', self::$TOPname)
                    ->with('formFields', $formFields)
                    ->with('status', $status);
    }
    
    public function updateNickName(Request $request){
        // init status
        $responseBody = array(
          'status' => 0,
          'errorCode' => '9999',
          'message' => 'Unknown error.',
          
        );
        // get param
        $name = $request->input('name', '');
        $ID = $request->input('ID', '');

        if($responseBody['status'] == 0) {
            // 更新
            DimHotspot::on('mysql2')
                    ->where('id',$ID)
                    ->update(['NickName' => $name]);
            
            $responseBody['status'] = 0;
            $responseBody['message'] = 'change success!';
            $responseBody['errorCode'] = '0000';
        }
        return Response::json($responseBody, 200);
    }
}