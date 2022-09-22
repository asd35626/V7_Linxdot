<?php
namespace App\Http\Controllers\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Model\DimHotspot;
use App\Model\DimProductModel;
use App\Model\HotspotBlackLog;
use App\Model\HotspotMaintainLog;
use App\Model\DimUser;
use Uuid;
use Response;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class IssuesController extends Controller
{
    /// <summary>
    /// 檢查權限
    /// </summary>
    function __construct(){
        WebLib::checkUserPermission('Issues');
    }
    // 設定blade目錄的位置
    public static $viewPath = "CustomerService.Issues";
    
    // 設定route目錄的位置
    public static $routePath = "Issues";

    // 這個資料表的主要鍵值
    public static $primaryKey = "id";

    // 設定功能主選單名稱名稱
    public static $TOPname = "Customer Service";

    // 設定功能名稱
    public static $functionname = "Issues";

    // 設定功能名稱
    public static $functionURL = "/CustomerService/Issues";

    // 取得model選單
    public function getProductModel() {
        return DimProductModel::where('IfDelete', 0)
                        ->orderBy('ModelName', 'asc');
    }

    // 定義搜尋的欄位設定;
    public function defineSearchFields() {
        $fields = [ 
            'MacAddress' =>  [
                'name' => 'MacAddress',
                'id' => 'MacAddress',
                'label' => 'lan mac',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'DeviceSN' =>  [
                'name' => 'DeviceSN',
                'id' => 'DeviceSN',
                'label' => 's/n',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'LogType' => [
                'name' => 'LogType',
                'id' => 'LogType',
                'label' => 'Type',
                'type' => 'radio',
                'selectLists' => [
                    'all' => 'All',
                    '0' => 'H/W issue.',
                    '1' => 'Helium related.',
                    '2' => 'Setting error.',
                    '99' => 'others.',
                ],
                'value' => 'all',
                'class' => 'md-input label-fixed',
            ],
            'DateFrom' => [
                'name' => 'DateFrom',
                'id' => 'DateFrom',
                'label' => 'Date From',
                'type' => 'date',
                'value' => '',
                'extras' => [
                    'placeholder' => '',
                    'data-parsley-trigger' => 'change',
                    'autocomplete' => 'off',
                ],
                'class' => 'md-input label-fixed',
            ],
            'DateTo' => [
                'name' => 'DateTo',
                'id' => 'DateTo',
                'label' => 'Date To',
                'type' => 'date',
                'extras' => [
                    'placeholder' => '',
                    'data-parsley-trigger' => 'change',
                    'autocomplete' => 'off',
                ],
                'value' => '',
                'class' => 'md-input label-fixed',
            ]
        ];
        return $fields;
    }

    // 定義回報問題欄位
    public function defineIssueFields() {
        $fields = [
            'AnimalName' => [
                'name' => 'AnimalName',
                'id' => 'AnimalName',
                'label' => 'Animal name',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'DeviceSN' => [
                'name' => 'DeviceSN',
                'id' => 'DeviceSN',
                'label' => 's/n',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'MacAddress' => [
                'name' => 'MacAddress',
                'id' => 'MacAddress',
                'label' => 'lan mac',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'LogType' => [
                'name' => 'LogType',
                'id' => 'LogType',
                'label' => 'Type',
                'type' => 'radio',
                'selectLists' => [
                    '0' => 'H/W issue.',
                    '1' => 'Helium related.',
                    '2' => 'Setting error.',
                    '99' => 'others.',
                ],
                'value' => '0',
                'class' => 'md-input label-fixed',
            ],
            'Subject' => [
                'name' => 'Subject',
                'id' => 'Subject',
                'label' => 'Subject',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'Description' => [
                'name' => 'Description',
                'id' => 'Description',
                'label' => 'Description',
                'type' => 'textarea',
                'validation' => '',
                'value' => '',
                'extras' => [],
                'class' => 'tinymce label-fixed',
                'extras' => ['style' => 'width: 100%']
            ],
            'CompletedReport' => [
                'name' => 'CompletedReport',
                'id' => 'CompletedReport',
                'label' => 'CompletedReport',
                'type' => 'textarea',
                'validation' => '',
                'value' => '',
                'extras' => [],
                'class' => 'tinymce label-fixed',
                'extras' => ['style' => 'width: 100%']
            ],
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
        $status = $request->input('status', '');            // 是否順序排序
        $isAsc = $request->input('isAsc', '');              // 是否檢查onlin
        $excel = $request->input('excel', 0);               // 是否匯出excel

        // 取得輸入欄位的定義
        $formFieldDef = self::defineIssueFields();
        // 產生需要設定的欄位
        $formFields = WebLib::generateInputs($formFieldDef, false)["data"];

        // 產生搜尋的欄位;
        $searchFields = WebLib::generateInputs(self::defineSearchFields(), true)["data"];

        // 調整欄位CSS
        if($searchFields['LogType']['value'] != ''){
            $LogType = $searchFields['LogType']['value'];
        }else{
            $LogType = 'all';
        }
        $searchFields['LogType']['completeField'] = str_replace('checked','',$searchFields['LogType']['completeField']);
        $searchFields['LogType']['completeField'] = str_replace('value="'.$LogType.'"','value="'.$LogType.'" checked="checked"',$searchFields['LogType']['completeField']);

        // 當按下搜尋的時候，會傳回IfNewSearch = 1; 如果不是，表示空值或是其他數值;
        // 當是其他數值的時候，會依照原來的頁碼去產生回應的頁面;
        if($IsNewSearch != '1') {
            Paginator::currentPageResolver(function () use ($pageNo) {
                return $pageNo;
            });
        }else{
            $pageNo = 1;
        }

        $data = HotspotMaintainLog::where('Hotspot_Maintain_Log.IfDelete','0')
                ->leftJoin('Dim_Hotspot', function($join){
                    $join->on('Hotspot_Maintain_Log.MacAddress','=','Dim_Hotspot.MacAddress')
                        ->where('Dim_Hotspot.IfValid' , 1)
                        ->where('Dim_Hotspot.IfDelete' , 0);
                })
                ->select('Hotspot_Maintain_Log.*',
                    'Dim_Hotspot.AnimalName'
                );

        if ($IfSearch == '1') {
            // 表示會需要參考搜尋的變數
            $searchArray = array(
                'DeviceSN' => $searchFields['DeviceSN']['value'],
                'MacAddress' => strtolower(str_replace("-",":",$searchFields['MacAddress']['value'])),
                'LogType' => $searchFields['LogType']['value'],
                'DateFrom' => $searchFields['DateFrom']['value'],
                'DateTo' => $searchFields['DateTo']['value'],
            );
            // dd($searchArray);

            $data= $data->where(function($query) use ($searchArray) {
                if($searchArray['DeviceSN'] != '') {
                    $query->where('Hotspot_Maintain_Log.DeviceSN', 'like', '%'.$searchArray['DeviceSN'].'%' );
                }
                if($searchArray['MacAddress'] != '') {
                    $query->where('Hotspot_Maintain_Log.MacAddress', 'like', '%'.$searchArray['MacAddress'].'%' );
                }
                if($searchArray['LogType'] != 'all') {
                    $query->where('Hotspot_Maintain_Log.LogType', 'like', '%'.$searchArray['LogType'].'%' );
                }
                if ($searchArray['DateFrom'] != '') {
                    $query->where('Hotspot_Maintain_Log.LogDate', '>=', ($searchArray['DateFrom'] . ' 00:00:00'));
                }
                if ($searchArray['DateTo'] != '') {
                    $query->where('Hotspot_Maintain_Log.LogDate', '<=', ( $searchArray['DateTo'] . ' 23:59:59'));
                }
            });
        }

        //排序
        if(isset($orderBy) && $orderBy != ''){
            if($isAsc == '1'){
                $data = $data->orderBy($orderBy, 'ASC');
            }else{
                $data = $data->orderBy($orderBy, 'DESC');
            }
        }else{
            $data = $data->orderBy('Hotspot_Maintain_Log.IsCompleted')->orderBy('Hotspot_Maintain_Log.LogDate');
        }

        // 分頁
        $data = $data->paginate($pageNumEachPage);

        return view(self::$viewPath.'.index', compact('data'))
                    ->with('i', ($pageNo - 1) * $pageNumEachPage)
                    ->with('IfSearch', $IfSearch)
                    ->with('pageNo', $pageNo)
                    ->with('searchFields',  $searchFields)
                    ->with('routePath', self::$routePath)
                    ->with('viewPath', self::$viewPath)
                    ->with('primaryKey', self::$primaryKey)
                    ->with('orderBy', $orderBy)
                    ->with('isAsc', $isAsc)
                    ->with('pageNumEachPage', $pageNumEachPage)
                    ->with('formFields', $formFields)
                    ->with('functionname', self::$functionname)
                    ->with('functionURL', self::$functionURL)
                    ->with('TOPname', self::$TOPname);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return redirect()->route(self::$routePath.'.index');
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return redirect()->route(self::$routePath.'.index');
    }

    // 回報問題
    public function solvingIssue(Request $request){
        // init status
        $responseBody = array(
          'status' => 0,
          'errorCode' => '9999',
          'message' => 'Unknown error.',
        );

        $id = $request->input('LogId', '');
        $CompletedReport = $request->input('CompletedReport', '');

        if($responseBody['status'] == 0) {
            $newData = [
                'LogId' => $id,
                'IsCompleted' => 1,
                'CompletedReport' => $CompletedReport,
                'IsCompletedBy' => WebLib::getCurrentUserID(),
                'IsCompletedDate' => Carbon::now('Asia/Taipei')->toDateTimeString() // 表示為目前時間;
            ];
            // dd( $newData);

            // 執行產生資料的動作
            HotspotMaintainLog::on('mysql2')->where('LogId',$id)->update($newData);
            
            
            $responseBody['status'] = 0;
            $responseBody['message'] = 'change success!';
            $responseBody['errorCode'] = '0000';
        }
        return Response::json($responseBody, 200);
    }
}