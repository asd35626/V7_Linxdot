<?php
namespace App\Http\Controllers\Device;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Model\DimProductModel;
use Uuid;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductModelController extends Controller
{
    /// <summary>
    /// 檢查權限
    /// </summary>
    function __construct(){
        WebLib::checkUserPermission('ProductModel');
    }
    // 設定blade目錄的位置
    public static $viewPath = "Device.ProductModel";
    
    // 設定route目錄的位置
    public static $routePath = "ProductModel";

    // 這個資料表的主要鍵值
    public static $primaryKey = "ModelID";

    // 設定功能主選單名稱名稱
    public static $TOPname = "Profiles";

    // 設定功能名稱
    public static $functionname = "Device Model";

    // 設定功能名稱
    public static $functionURL = "/Device/ProductModel";

    // 定義搜尋的欄位設定;
    public function defineSearchFields() {
        $fields = [ 
            'ModelNo' =>  [
                'name' => 'ModelNo',
                'id' => 'ModelNo',
                'label' => 'ModelNo',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'ModelName' =>  [
                'name' => 'ModelName',
                'id' => 'ModelName',
                'label' => 'ModelName',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ]
        ];
        return $fields;
    }

    public function defineFormFields() {
        $fields = [
            'ModelNo' => [
                'name' => 'ModelNo',
                'id' => 'ModelNo',
                'label' => 'ModelNo',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'ModelName' => [
                'name' => 'ModelName',
                'id' => 'ModelName',
                'label' => 'ModelName',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'ModelSpec' =>  [
                'name' => 'ModelSpec',
                'id' => 'ModelSpec',
                'label' => 'ModelSpec',
                'type' => 'simple_textarea',
                'validation' => '',
                'value' => '',
                'extras' => [],
                'class' => 'tinymce abel-fixed',
            ],
            'ModelInfo' =>  [
                'name' => 'ModelInfo',
                'id' => 'ModelInfo',
                'label' => 'ModelInfo',
                'type' => 'simple_textarea',
                'validation' => '',
                'value' => '',
                'extras' => [],
                'class' => 'tinymce abel-fixed',
            ],
            'IfValid' => [
                'name' => 'IfValid',
                'id' => 'IfValid',
                'label' => 'Status',
                'type' => 'radio',
                'selectLists' => [
                    '1' => 'Active',
                    '0' => 'Inactive'
                ],
                'value' => '1',
                'validation' => 'required',
                'class' => 'md-input label-fixed',
            ],
            'CreateBy'  => [
                'name' => 'CreateBy',
                'id' => 'CreateBy',
                'label' => 'Created By',
                'type' => 'text',
                'value' => 'CreateBy',
                'validation' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'CreateDate' =>  [
                'name' => 'CreateDate',
                'id' => 'CreateDate',
                'label' => 'Created Date',
                'type' => 'text',
                'validation' => '',
                'value' => 'CreateDate',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ]
        ];
        return $fields;
    }

    public function defineEditFormFields($data) {
        $fields = [
            'ModelNo' => [
                'name' => 'ModelNo',
                'id' => 'ModelNo',
                'label' => 'ModelNo',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'ModelName' => [
                'name' => 'ModelName',
                'id' => 'ModelName',
                'label' => 'ModelName',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'ModelSpec' =>  [
                'name' => 'ModelSpec',
                'id' => 'ModelSpec',
                'label' => 'ModelSpec',
                'type' => 'simple_textarea',
                'validation' => '',
                'value' => '',
                'extras' => [],
                'class' => 'tinymce abel-fixed',
            ],
            'ModelInfo' =>  [
                'name' => 'ModelInfo',
                'id' => 'ModelInfo',
                'label' => 'ModelInfo',
                'type' => 'simple_textarea',
                'validation' => '',
                'value' => '',
                'extras' => [],
                'class' => 'tinymce abel-fixed',
            ],
            'IfValid' => [
                'name' => 'IfValid',
                'id' => 'IfValid',
                'label' => 'Status',
                'type' => 'radio',
                'selectLists' => [
                    '1' => 'Active',
                    '0' => 'Inactive'
                ],
                'value' => '1',
                'validation' => 'required',
                'class' => 'md-input label-fixed',
            ],
            'CreateBy'  => [
                'name' => 'CreateBy',
                'id' => 'CreateBy',
                'label' => 'Created By',
                'type' => 'text',
                'value' => 'CreatedBy',
                'validation' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'CreateDate' =>  [
                'name' => 'CreateDate',
                'id' => 'CreateDate',
                'label' => 'Created Date',
                'type' => 'text',
                'validation' => '',
                'value' => 'CreateDate',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
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
        $excel = $request->input('excel', 0);               // 是否匯出excel

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

        $data = DimProductModel::where('IfDelete','0');

        if ($IfSearch == '1') {
            // 表示會需要參考搜尋的變數
            $searchArray = array(
                'ModelNo' => $searchFields['ModelNo']['value'],
                'ModelName' => $searchFields['ModelName']['value']
            );

            $data= $data->where(function($query) use ($searchArray) {
                if($searchArray['ModelNo'] != '') {
                    $query->where('ModelNo', 'like', '%'.$searchArray['ModelNo'].'%' );
                }
                if($searchArray['ModelName'] != '') {
                    $query->where('ModelName', 'like', '%'.$searchArray['ModelName'].'%' );
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
            $data = $data->orderBy('CreateDate');
        }

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
                    ->with('functionname', self::$functionname)
                    ->with('functionURL', self::$functionURL)
                    ->with('TOPname', self::$TOPname);
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        // 取得輸入欄位的定義
        $formFieldDef = self::defineFormFields();

        // 產生需要設定的欄位
        $formFields = WebLib::generateInputs($formFieldDef, false)["data"];

        return view(self::$viewPath.'.create')
                    ->with('formFields', $formFields)
                    ->with('routePath', self::$routePath)
                    ->with('Action', "NEW")
                    ->with('viewPath', self::$viewPath)
                    ->with('functionname', self::$functionname)
                    ->with('functionURL', self::$functionURL)
                    ->with('TOPname', self::$TOPname);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        // 步驟一：使用預設好的欄位取得資料;
        // 取得輸入欄位的定義
        $formFieldDef = self::defineFormFields();

        // 產生需要設定的欄位  
        $requestResult =  WebLib::generateInputs($formFieldDef, true);
        $formFields = $requestResult["data"];

        // 步驟二：檢查是否有存在相同的值
        if($requestResult['isError'] == false) {
            // for ModelNo
            $existUserType = DimProductModel::where('ModelNo', '=',  $formFields['ModelNo']['value'])
                                        ->where('IfDelete',0)
                                        ->get();
            
            if($existUserType->count() > 0 ){
                $requestResult['isError'] = true;
                $formFields['ModelNo']['isCorrect'] = false;
                $formFields['ModelNo']['error'] = "This Model No. has been taken";
                $formFields['ModelNo']['completeField'] = GenerateData::generateCustomErrorMessage('ModelNo','ModelNo', $formFields['ModelNo']['value'], $formFields['ModelNo']['error'], 'text');
            }
        }

        // 步驟三：檢查回傳的狀態
        if($requestResult['isError'] == false) {
            $id = Uuid::generate(4);

            // 組成目前需要新增的資料物件;
            $newData = [
                'ModelID' => $id,
                'ModelNo' => $formFields['ModelNo']['value'],
                'ModelName' => $formFields['ModelName']['value'],
                'ModelSpec' => $formFields['ModelSpec']['value'],
                'ModelInfo' => $formFields['ModelInfo']['value'],

                'IfValid' => $formFields['IfValid']['value'],
                'IfDelete' => 0,
                'CreateBy' => WebLib::getCurrentUserID(),
                'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString() // 表示為目前時間;
            ];

            // 執行產生資料的動作
            DimProductModel::on('mysql2')->create($newData);

            return redirect()->route(self::$routePath.'.index')
                        ->with('success','成功新增一筆資料！');
        }else{
            // 表示檢查失敗，必須要重新產生要新增的頁面;
            $IfValid= ($formFields['IfValid']['value'] == '') ? 1 : $formFields['IfValid']['value'];
            $formFields['IfValid']['completeField'] = GenerateData::getIfValidHtml($IfValid);

            $formFields['CreateBy']['completeField'] = GenerateData::generateData('Created By','CreateBy', 'Generated by System', '', 'text');

            $formFields['CreateDate']['completeField'] = GenerateData::generateData('Created Date','CreateDate', 'Generated by System', '', 'text');

            return view(self::$viewPath.'.create')
                ->with('formFields', $formFields)
                ->with('routePath', self::$routePath)
                ->with('Action', "NEW")
                ->with('viewPath', self::$viewPath)
                ->with('functionname', self::$functionname)
                    ->with('functionURL', self::$functionURL)
                    ->with('TOPname', self::$TOPname);
        }
    }
    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        return redirect()->route(self::$routePath.'.index');
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $id = (string) $id;
        $data= DimProductModel::where('ModelID', $id)
                            ->where('IfDelete', 0)
                            ->get();

        // 取得輸入欄位的定義
        $formFieldDef = self::defineEditFormFields($data->first());
        // dd($formFieldDef);

        // 把資料放進對應欄位
        $requestResult =  WebLib::generateInputsWhthData($formFieldDef, $data);
        // 修正Created By
        $requestResult['CreateBy']['value'] = GenerateData::getCreater($data->first()->CreateBy);
        // 產生需要設定的欄位  
        $requestResult = WebLib::generateInputs($requestResult, false);
        // 把產生好的欄位取出來
        $formFields = $requestResult["data"];

        return view(self::$viewPath.'.edit', compact('data'))
                    ->with('formFields', $formFields)
                    ->with('routePath', self::$routePath)
                    ->with('targetId', $id)
                    ->with('Action', "EDIT")
                    ->with('primaryKey', self::$primaryKey)
                    ->with('viewPath', self::$viewPath)
                    ->with('functionname', self::$functionname)
                    ->with('functionURL', self::$functionURL)
                    ->with('TOPname', self::$TOPname);
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
        $id = (string) $id;
        $data= DimProductModel::where('ModelID', $id)->first();
        $formFieldDef = $this->defineEditFormFields($data);
        $requestResult = WebLib::generateInputs($formFieldDef, true);
        $formFields = $requestResult["data"];
        $errmsg = "";

        // 檢查是否有存在相同的值
        if($requestResult['isError'] == false) {
            // for ModelNo
            $existUserType = DimProductModel::where('ModelNo', '=',  $formFields['ModelNo']['value'])
                                        ->where('IfDelete',0)
                                        ->where('ModelID','!=',$id)
                                        ->get();
            
            if($existUserType->count() > 0 ){
                $requestResult['isError'] = true;
                $formFields['ModelNo']['isCorrect'] = false;
                $formFields['ModelNo']['error'] = "This Model No. has been taken";
                $formFields['ModelNo']['completeField'] = GenerateData::generateCustomErrorMessage('ModelNo','ModelNo', $formFields['ModelNo']['value'], $formFields['ModelNo']['error'], 'text');
            }
        }

        if($requestResult['isError'] == false) {
            $updateValue = [
                'ModelNo' => $formFields['ModelNo']['value'],
                'ModelName' => $formFields['ModelName']['value'],
                'ModelSpec' => $formFields['ModelSpec']['value'],
                'ModelInfo' => $formFields['ModelInfo']['value'],

                'IfValid' => $formFields['IfValid']['value'],
                'UpdateBy' => WebLib::getCurrentUserID(),
                'UpdateDate' => Carbon::now('Asia/Taipei')->toDateTimeString() // 表示為目前時間;
            ];

            if($data->IfValid == '1'&& $formFields['IfValid']['value'] == 0 ) {
                // 表示要變更IfValid狀態由1變成0,需要變更IfNotValidBy跟IfNotValidDate
                $updateValue['IfNotValidBy'] =  WebLib::getCurrentUserID();
                $updateValue['IfNotValidDate'] = Carbon::now('Asia/Taipei')->toDateTimeString();
            }

            DimProductModel::on('mysql2')->where('ModelID', $id)->update($updateValue);
            return redirect()->route(self::$routePath.'.index')
                            ->with('success','更新完成');
        }else{
            //重新產生原本的資料            
            $IfValid= ($formFields['IfValid']['value'] == '') ? $data->IfValid : $formFields['IfValid']['value'];
            $formFields['IfValid']['completeField'] = GenerateData::getIfValidHtml($IfValid);

            $CreateBy = GenerateData::getCreater($data->first()->CreateBy);
            $formFields['CreateBy']['value'] = $CreateBy;
            $formFields['CreateBy']['completeField'] = GenerateData::generateData('Created By','CreateBy', $CreateBy, '', 'text');

            $formFields['CreateDate']['value'] = ($formFields['CreateDate']['value'] == '') ? $data->CreateDate : $formFields['CreateDate']['value'];
            $formFields['CreateDate']['completeField'] = GenerateData::generateData('Created Date','CreateDate', $formFields['CreateDate']['value'], '', 'text');

            return view(self::$viewPath.'.edit', compact('data'))
                    ->with('formFields', $formFields)
                    ->with('routePath', self::$routePath)
                    ->with('targetId', $id)
                    ->with('primaryKey', self::$primaryKey)
                    ->with('Action', "EDIT")
                    ->with('viewPath', self::$viewPath)
                    ->with('errmsg', $errmsg)
                    ->with('functionname', self::$functionname)
                    ->with('functionURL', self::$functionURL)
                    ->with('TOPname', self::$TOPname);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $authToken = \Cookie::get('authToken');
        $DeleteValue = [
            'IfDelete' => 1,
            'IfDeleteBy' => WebLib::getCurrentUserID(),
            'IfDeleteDate' => Carbon::now('Asia/Taipei')->toDateTimeString() // 表示為目前時間;
        ];
        DimProductModel::on('mysql2')->where('ModelID', $id)->update($DeleteValue);
        return redirect()->route(self::$routePath.'.index')
                        ->with('success','資料已經刪除');
    }
}