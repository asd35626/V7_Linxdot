<?php
namespace App\Http\Controllers\System;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use App\Model\DimUserType;
use Hash;
use Uuid;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;
/**
 *  這是用在管理Dim_UserType資料Controller, 將對應的幾個不用的View
 */
class SystemUserTypeController extends Controller
{
    /// <summary>
    /// 檢查權限
    /// </summary>
    function __construct(){
        WebLib::checkUserPermission('UserType');
    }
    // 設定blade目錄的位置
    public static $viewPath = "System.UserType";
    
    // 設定route目錄的位置
    public static $routePath = "UserType";

    // 這個資料表的主要鍵值
    public static $primaryKey = "UserTypeId";

    // 設定功能名稱
    public static $functionname = "用戶型別定義";

    // 定義搜尋的欄位設定;
    public function defineSearchFields() {
        $fields = [ 
            'SelectKeyword' =>  [
                'name' => 'SelectKeyword',
                'id' => 'SelectKeyword',
                'label' => '關鍵字',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
        ];
        return $fields;
    }

    public function defineFormFields() {
        $fields = [ 
            'UserTypeId' => [
                'name' => 'UserTypeId',
                'id' => 'UserTypeId',
                'label' => '類別ID',
                'type' => 'text',
                'validation' => 'required|integer',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'UserTypeName' => [
                'name' => 'UserTypeName',
                'id' => 'UserTypeName',
                'label' => '用戶類別名稱',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'UserTypeMark' => [
                'name' => 'UserTypeMark',
                'id' => 'UserTypeMark',
                'label' => '標記',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'UserTypeMemo' => [
                'name' => 'UserTypeMemo',
                'id' => 'UserTypeMemo',
                'label' => '備註',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'IfValid' => [
                'name' => 'IfValid',
                'id' => 'IfValid',
                'label' => '啟用/停用',
                'type' => 'radio',
                'selectLists' => [
                    '0' => '停用',
                    '1' => '啟用'
                ],
                'value' => '1',
                'validation' => 'required',
                'class' => 'md-input label-fixed',
            ],
            'CreateBy'  => [
                'name' => 'CreateBy',
                'id' => 'CreateBy',
                'label' => '資料建立者',
                'type' => 'text',
                'value' => '系統自動產生',
                'validation' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'CreateDate' =>  [
                'name' => 'CreateDate',
                'id' => 'CreateDate',
                'label' => '資料建立日期',
                'type' => 'text',
                'validation' => '',
                'value' => '系統自動產生',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ]
        ];
        return $fields;
    }

    public function defineEditFormFields($data) {
        $fields = [ 
            'UserTypeId' => [
                'name' => 'UserTypeId',
                'id' => 'UserTypeId',
                'label' => '類別ID',
                'type' => 'text',
                'validation' => 'required|integer',
                'value' => $data->UserTypeId,
                'class' => 'md-input label-fixed',
            ],
            'UserTypeName' => [
                'name' => 'UserTypeName',
                'id' => 'UserTypeName',
                'label' => '用戶類別名稱',
                'type' => 'text',
                'validation' => 'required',
                'value' => $data->UserTypeName,
                'class' => 'md-input label-fixed',
            ],
            'UserTypeMark' => [
                'name' => 'UserTypeMark',
                'id' => 'UserTypeMark',
                'label' => '標記',
                'type' => 'text',
                'validation' => '',
                'value' => $data->UserTypeMark,
                'class' => 'md-input label-fixed',
            ],
            'UserTypeMemo' => [
                'name' => 'UserTypeMemo',
                'id' => 'UserTypeMemo',
                'label' => '備註',
                'type' => 'text',
                'value' => $data->UserTypeMemo,
                'class' => 'md-input label-fixed',
            ],
            'IfValid' => [
                'name' => 'IfValid',
                'id' => 'IfValid',
                'label' => '啟用/停用',
                'type' => 'radio',
                'selectLists' => [
                    '0' => '停用',
                    '1' => '啟用'
                ],
                'value' => $data->IfValid,
                'validation' => 'required',
                'class' => 'md-input label-fixed',
            ],
            'CreateBy'  => [
                'name' => 'CreateBy',
                'id' => 'CreateBy',
                'label' => '資料建立者',
                'type' => 'text',
                'value' =>  isset($data->Creater) ? $data->Creater->RealName : '系統管理員',
                'validation' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'CreateDate' =>  [
                'name' => 'CreateDate',
                'id' => 'CreateDate',
                'label' => '資料建立日期',
                'type' => 'text',
                'validation' => '',
                'value' => isset($data->CreateDate) ? $data->CreateDate : '系統自動產生',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ]
        ];
        return $fields;
    }

    public function index(Request $request)
    {
        $pageNumEachPage = 50;                              // 每頁的基本資料量
        $pageNo = (int) $request->input('Page', '1');       // 目前的頁碼
        $IsNewSearch = $request->input('IfNewSearch', '');  // 是否為新開始搜尋
        $IfSearch = $request->input('IfSearch', '');        // 是否為搜尋

        // 產生搜尋的欄位;
        $searchFields = WebLib::generateInputs(self::defineSearchFields(), true)["data"];
        //dd($searchFields);

        // 當按下搜尋的時候，會傳回IfNewSearch = 1; 如果不是，表示空值或是其他數值;
        // 當是其他數值的時候，會依照原來的頁碼去產生回應的頁面;
        if($IsNewSearch != '1') {
            // Make sure that you call the static method currentPageResolver()
            // before querying users
            Paginator::currentPageResolver(function () use ($pageNo) {
                return $pageNo;
            });
        }else{
            $pageNo = 1;
        }

        $data = DimUserType::where('IfDelete', '=', '0');

        if ($IfSearch == '1') {
            // 表示會需要參考搜尋的變數
            $searchArray = array(
                'selectKeyword' => $searchFields['SelectKeyword']['value'],
            );

            $data= $data->where(function($query) use ($searchArray) {
                // dd($searchArray['userTypeID']);
                if($searchArray['selectKeyword'] != '') {
                    $query->where('UserTypeName', 'like', '%'.$searchArray['selectKeyword'].'%' )
                            ->orwhere('userTypeID', 'like', '%'.$searchArray['selectKeyword'].'%' );
                }
            });
        }

        $data = $data->orderBy('UserTypeId','DESC')->paginate($pageNumEachPage);
        
        return view(self::$viewPath.'.index', compact('data'))
                    ->with('i', ($pageNo - 1) * $pageNumEachPage)
                    ->with('IfSearch', $IfSearch)
                    ->with('pageNo', $pageNo)
                    ->with('searchFields',  $searchFields)
                    ->with('routePath', self::$routePath)
                    ->with('viewPath', self::$viewPath)
                    ->with('pageNumEachPage', $pageNumEachPage)
                    ->with('functionname', self::$functionname);
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
                    ->with('functionname', self::$functionname);
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
            // for UserTypeId
            $existUserType = DimUserType::where('UserTypeId', '=',  $formFields['UserTypeId']['value'])
                                        // ->where('IfDelete',0)
                                        ->get();
            
            if($existUserType->count() > 0 ){
                $requestResult['isError'] = true;
                $formFields['UserTypeId']['isCorrect'] = false;
                $formFields['UserTypeId']['error'] = "這個數值已經被用過了，不可以重複";
                $formFields['UserTypeId']['completeField'] = GenerateData::generateCustomErrorMessage('類別代碼','UserTypeId', $formFields['UserTypeId']['value'], $formFields['UserTypeId']['error'], 'text');
            }

            // for UserTypeName
            $existUserType = DimUserType::where('UserTypeName', '=',  $formFields['UserTypeName']['value'])
                                        ->where('IfDelete',0)
                                        ->get();
            
            if($existUserType->count() > 0 ){
                $requestResult['isError'] = true;
                $formFields['UserTypeName']['isCorrect'] = false;
                $formFields['UserTypeName']['error'] = "這個數值已經被用過了，不可以重複";
                $formFields['UserTypeName']['completeField'] = GenerateData::generateCustomErrorMessage('用戶類別名稱','UserTypeName', $formFields['UserTypeName']['value'], $formFields['UserTypeName']['error'], 'text');
            }
        }

        // 步驟三：檢查回傳的狀態
        if($requestResult['isError'] == false) {
            // 組成目前需要新增的資料物件;
            $newData = [
                'UserTypeId' => $formFields['UserTypeId']['value'],
                'UserTypeName' => $formFields['UserTypeName']['value'],
                'UserTypeMark' => $formFields['UserTypeMark']['value'],
                'UserTypeMemo' => $formFields['UserTypeMemo']['value'],
                'IfValid' => $formFields['IfValid']['value'],
                'IfDelete' => 0,
                'CreateBy' => WebLib::getCurrentUserID(),
                'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString() //date("Y-m-d H:i:s")             //'表示為目前時間;
            ];

            // 執行產生資料的動作
            DimUserType::on('mysql2')->create($newData);

            return redirect()->route(self::$routePath.'.index')
                        ->with('success','成功新增一筆資料！');
        }else{
            // 表示檢查失敗，必須要重新產生要新增的頁面;
            $IfValid= ($formFields['IfValid']['value'] == '') ? 1 : $formFields['IfValid']['value'];
            $formFields['IfValid']['completeField'] = GenerateData::getIfValidHtml($IfValid);

            $formFields['CreateBy']['completeField'] = GenerateData::generateData('資料建立者','CreateBy', '系統自動產生', '', 'text');

            $formFields['CreateDate']['completeField'] = GenerateData::generateData('資料建立日期','CreateDate', '系統自動產生', '', 'text');

            return view(self::$viewPath.'.create')
                ->with('formFields', $formFields)
                ->with('routePath', self::$routePath)
                ->with('Action', "NEW")
                ->with('viewPath', self::$viewPath)
                ->with('functionname', self::$functionname);
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
        //取出對應資料
        $id = (string) $id;
        $data= DimUserType::where('UserTypeId', $id)->first();
        // 取得輸入欄位的定義
        $formFieldDef = self::defineEditFormFields($data->first());
        // dd($formFieldDef);

        // 把資料放進欄位
        $requestResult = WebLib::generateInputsWhthData($formFieldDef, $data);
        // dd($requestResult);

        // 設定extras = 無效
        foreach ($requestResult as $key => $value) {
            // dd($key);
            switch($key){
                case 'IfValid':
                    //設定成無效
                    $requestResult[$key]['extras'] = ['disabled' => 'disabled'];
                break;
                default:
                    //設定成唯讀
                    $requestResult[$key]['extras'] = ['readonly' => 'readonly'];
                break;
            }
        }

        // 修正資料建立者
        $requestResult['CreateBy']['value'] = GenerateData::getCreater($data->first()->CreateBy);
        // 產生需要設定的欄位
        $requestResult = WebLib::generateInputs($requestResult, false);
        // 取出設定好的欄位
        $formFields = $requestResult["data"];
        // dd($formFields);

        return view(self::$viewPath.'.edit', compact('data'))
                    ->with('formFields', $formFields)
                    ->with('routePath', self::$routePath)
                    ->with('targetId', $id)
                    ->with('Action', "SHOW")
                    ->with('primaryKey', self::$primaryKey)
                    ->with('viewPath', self::$viewPath)
                    ->with('functionname', self::$functionname);
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
        $data= DimUserType::where('UserTypeId', $id)
                            ->where('IfDelete', 0)
                            ->get();

        // 取得輸入欄位的定義
        $formFieldDef = self::defineEditFormFields($data->first());
        // dd($formFieldDef);

        // 把資料放進對應欄位
        $requestResult =  WebLib::generateInputsWhthData($formFieldDef, $data);
        // 修正資料建立者
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
                    ->with('functionname', self::$functionname);
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
        $data= DimUserType::where('UserTypeId', $id)->first();
        $formFieldDef = $this->defineEditFormFields($data);
        $requestResult = WebLib::generateInputs($formFieldDef, true);
        $formFields = $requestResult["data"];
        $errmsg = "";

        // 檢查是否有存在相同的值
        if($requestResult['isError'] == false) {
            // for UserTypeId
            $exist = DimUserType::where('UserTypeId', '=',  $formFields['UserTypeId']['value'])
                                ->where('UserTypeId', '!=', $id)
                                ->where('IfDelete',0)
                                ->get();

            if($exist->count() > 0 ){
                $requestResult['isError'] = true;
                $formFields['UserTypeId']['isCorrect'] = false;
                $formFields['UserTypeId']['error'] = "這個數值已經被用過了，不可以重複";
                $formFields['UserTypeId']['completeField'] = GenerateData::generateCustomErrorMessage('類別代碼','UserTypeId', $formFields['UserTypeId']['value'], $formFields['UserTypeId']['error'], 'text');
            }

            // for UserTypeName
            $existUserType = DimUserType::where('UserTypeName', '=',  $formFields['UserTypeName']['value'])
                                        ->where('UserTypeId', '!=', $id)
                                        ->where('IfDelete',0)
                                        ->get();
            
            if($existUserType->count() > 0 ){
                $requestResult['isError'] = true;
                $formFields['UserTypeName']['isCorrect'] = false;
                $formFields['UserTypeName']['error'] = "這個數值已經被用過了，不可以重複";
                $formFields['UserTypeName']['completeField'] = GenerateData::generateCustomErrorMessage('用戶類別名稱','UserTypeName', $formFields['UserTypeName']['value'], $formFields['UserTypeName']['error'], 'text');
            }
        }

        if($requestResult['isError'] == false) {
            $updateValue = [
                'UserTypeId' => $formFields['UserTypeId']['value'],
                'UserTypeName' => $formFields['UserTypeName']['value'],
                'UserTypeMark' => $formFields['UserTypeMark']['value'],
                'UserTypeMemo' => $formFields['UserTypeMemo']['value'],
                'IfValid' => $formFields['IfValid']['value'],
                'UpdateBy' => WebLib::getCurrentUserID(),
                'UpdateDate' => Carbon::now('Asia/Taipei')->toDateTimeString() //date("Y-m-d H:i:s")             //'表示為目前時間;
            ];

            if($data->IfValid == '1'&& $formFields['IfValid']['value'] == 0 ) {
                // 表示要變更IfValid狀態由1變成0,需要變更IfNotValidBy跟IfNotValidDate
                $updateValue['IfNotValidBy'] =  WebLib::getCurrentUserID();
                $updateValue['IfNotValidDate'] = Carbon::now('Asia/Taipei')->toDateTimeString();
            }

            DimUserType::on('mysql2')->find($id)->update($updateValue);
            return redirect()->route(self::$routePath.'.index')
                            ->with('success','更新完成');
        }else{
            //重新產生原本的資料            
            $IfValid= ($formFields['IfValid']['value'] == '') ? $data->IfValid : $formFields['IfValid']['value'];
            $formFields['IfValid']['completeField'] = GenerateData::getIfValidHtml($IfValid);

            $CreateBy = GenerateData::getCreater($data->first()->CreateBy);
            $formFields['CreateBy']['value'] = $CreateBy;
            $formFields['CreateBy']['completeField'] = GenerateData::generateData('資料建立者','CreateBy', $CreateBy, '', 'text');

            $formFields['CreateDate']['value'] = ($formFields['CreateDate']['value'] == '') ? $data->CreateDate : $formFields['CreateDate']['value'];
            $formFields['CreateDate']['completeField'] = GenerateData::generateData('資料建立日期','CreateDate', $formFields['CreateDate']['value'], '', 'text');

            return view(self::$viewPath.'.edit', compact('data'))
                    ->with('formFields', $formFields)
                    ->with('routePath', self::$routePath)
                    ->with('targetId', $id)
                    ->with('primaryKey', self::$primaryKey)
                    ->with('Action', "EDIT")
                    ->with('viewPath', self::$viewPath)
                    ->with('errmsg', $errmsg)
                    ->with('functionname', self::$functionname);
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
            'DeleteDate' => Carbon::now('Asia/Taipei')->toDateTimeString() //date("Y-m-d H:i:s")
        ];

        DimUserType::on('mysql2')->where('UserTypeId', $id)->update($DeleteValue);

        return redirect()->route(self::$routePath.'.index')
                        ->with('success','資料已經刪除');
    }
}