<?php
namespace App\Http\Controllers\Device;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Model\DimHotspot;
use Uuid;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class B2BHotspotsController extends Controller
{
    /// <summary>
    /// 檢查權限
    /// </summary>
    function __construct(){
        WebLib::checkUserPermission('B2BHotspots');
    }
    // 設定blade目錄的位置
    public static $viewPath = "Device.B2BHotspots";
    
    // 設定route目錄的位置
    public static $routePath = "B2BHotspots";

    // 這個資料表的主要鍵值
    public static $primaryKey = "id";
    
    // 設定功能主選單名稱名稱
    public static $TOPname = "Device";

    // 設定功能名稱
    public static $functionname = "Hotspots";

    // 設定功能名稱
    public static $functionURL = "/Device/B2BHotspots";

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

    public function index(Request $request)
    {
        $pageNumEachPage = 100;                             // 每頁的基本資料量
        $pageNo = (int) $request->input('Page', '1');       // 目前的頁碼
        $IsNewSearch = $request->input('IfNewSearch', '');  // 是否為新開始搜尋
        $IfSearch = $request->input('IfSearch', '');        // 是否為搜尋
        $orderBy = $request->input('orderBy', '');          // 排序欄位
        $isAsc = $request->input('isAsc', '');              // 是否順序排序
        $user = WebLib::getCurrentUserID();                 // 使用者ID
        $search = $request->input('search', '');            // 查詢
        // dd($search);

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

        $data = DimHotspot::where('IfDelete','0')->where('OwnerID',$user);

        if($IfSearch == 1) {
            // 表示會需要參考搜尋的變數
            if($search != '') {
                $data= $data->where(function($query) use ($search) {
                    $query->where('DeviceSN', 'like', '%'.$search.'%' )
                        ->orwhere('AnimalName', 'like', '%'.$search.'%' );
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
                    ->with('TOPname', self::$TOPname);
    }
}