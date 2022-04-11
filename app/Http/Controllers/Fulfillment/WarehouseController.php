<?php

namespace App\Http\Controllers\Fulfillment;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Model\DimUser;
use App\Model\DimUserType;
use App\Model\DimUserDegreeToUserType;
use App\Model\DimMember;
use Hash;
use Uuid;
use Mail;
use Response;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\GenerateData;

/**
 *  這是用在管理物流商資料Controller, 將對應的幾個不用的View
 */
class WarehouseController extends Controller
{
    /// <summary>
    /// 檢查權限
    /// </summary>
    function __construct(){
        WebLib::checkUserPermission('Warehouse');
    }
    // 設定blade目錄的位置
    public static $viewPath = "Fulfillment.Warehouse";
    
    // 設定route目錄的位置
    public static $routePath = "Warehouse";

    // 這個資料表的主要鍵值
    public static $primaryKey = "Id";

    // 設定功能名稱
    public static $functionname = "Warehouse";

    // 定義搜尋的欄位設定;
    public function defineSearchFields() {
        $fields = [ 
            'SelectKeyword' => [
                'name' => 'SelectKeyword',
                'id' => 'SelectKeyword',
                'label' => 'keyword',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ]
        ];
        return $fields;
    }

    public function defineFormFields($userType='',$userDegree='',$id='') {//for create
        $fields = [ 
            'MemberNo' => [
                'name' => 'MemberNo',
                'id' => 'MemberNo',
                'label' => 'Login Name',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'UserPassword' => [
                'name' => 'UserPassword',
                'id' => 'UserPassword',
                'label' => 'Login Password',
                'type' => 'password',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'RealName' => [
                'name' => 'RealName',
                'id' => 'RealName',
                'label' => 'Name',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'UserEmail' => [
                'name' => 'UserEmail',
                'id' => 'UserEmail',
                'label' => 'Email',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'ContactPhone' => [
                'name' => 'ContactPhone',
                'id' => 'ContactPhone',
                'label' => 'Phone',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'ContactAddress' => [
                'name' => 'ContactAddress',
                'id' => 'ContactAddress',
                'label' => 'Address',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'CompanyName' => [
                'name' => 'CompanyName',
                'id' => 'CompanyName',
                'label' => 'Contact',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'CompanyPhone' => [
                'name' => 'CompanyPhone',
                'id' => 'CompanyPhone',
                'label' => 'Contact Phone',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'CompanyEmail' => [
                'name' => 'CompanyEmail',
                'id' => 'CompanyEmail',
                'label' => 'Contact Email',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'IfValid' => [
                'name' => 'IfValid',
                'id' => 'IfValid',
                'label' => 'Active/ Inactive',
                'type' => 'radio',
                'selectLists' => [
                    '0' => 'Inactive',
                    '1' => 'Active'
                ],
                'value' => '1',
                'validation' => 'required',
                'class' => 'md-input label-fixed',
            ],
            'CreateBy' => [
                'name' => 'CreateBy',
                'id' => 'CreateBy',
                'label' => 'Created By',
                'type' => 'text',
                'value' => 'Generated by System',
                'validation' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'CreateDate' => [
                'name' => 'CreateDate',
                'id' => 'CreateDate',
                'label' => 'Created Date',
                'type' => 'text',
                'validation' => '',
                'value' => 'Generated by System',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'LastLogin' => [
                'name' => 'LastLogin',
                'id' => 'LastLogin',
                'label' => 'Last Log-in Time',
                'type' => 'text',
                'validation' => '',
                'value' => 'Generated by System',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ]
        ];
        return $fields;
    }

    public function defineEditFormFields($user) {//for edit
        $fields = [
            'MemberNo' => [
                'name' => 'MemberNo',
                'id' => 'MemberNo',
                'label' => 'Login Name',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'UserPassword' => [
                'name' => 'UserPassword',
                'id' => 'UserPassword',
                'label' => 'Login Password',
                'type' => 'password',
                'validation' => '',
                'value' => '********',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'RealName' => [
                'name' => 'RealName',
                'id' => 'RealName',
                'label' => 'Name',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'UserEmail' => [
                'name' => 'UserEmail',
                'id' => 'UserEmail',
                'label' => 'Email',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'ContactPhone' => [
                'name' => 'ContactPhone',
                'id' => 'ContactPhone',
                'label' => 'Phone',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'ContactAddress' => [
                'name' => 'ContactAddress',
                'id' => 'ContactAddress',
                'label' => 'Address',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'CompanyName' => [
                'name' => 'CompanyName',
                'id' => 'CompanyName',
                'label' => 'Contact',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'CompanyPhone' => [
                'name' => 'CompanyPhone',
                'id' => 'CompanyPhone',
                'label' => 'Contact Phone',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'CompanyEmail' => [
                'name' => 'CompanyEmail',
                'id' => 'CompanyEmail',
                'label' => 'Contact Email',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'IfValid' => [
                'name' => 'IfValid',
                'id' => 'IfValid',
                'label' => 'Active/ Inactive',
                'type' => 'radio',
                'selectLists' => [
                    '0' => 'Inactive',
                    '1' => 'Active'
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
                'value' =>  isset($data->Creater) ? $data->Creater->RealName : '系統管理員',
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
                'value' => isset($data->CreateDate) ? $data->CreateDate : 'Generated by System',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'LastLogin' => [
                'name' => 'LastLogin',
                'id' => 'LastLogin',
                'label' => 'Last Log-in Time',
                'type' => 'text',
                'validation' => '',
                'value' => isset($data->LastLoginTime) ? $data->LastLoginTime : '無登入紀錄',
                'class' => 'md-input label-fixed',
                'extras' => ['readonly' => 'readonly'],
            ],
        ];
        return $fields;
    }

    public function index(Request $request)
    {
        $pageNumEachPage = 50;                              // 每頁的基本資料量
        $pageNo = (int) $request->input('Page', '1');       // 目前的頁碼
        $IsNewSearch = $request->input('IfNewSearch', '');  // 是否為新開始搜尋
        $IfSearch = $request->input('IfSearch', '');        // 是否為搜尋
        $orderBy = $request->input('orderBy', '');          // 排序欄位
        $isAsc = $request->input('isAsc', '');              // 是否順序排序
        $UserType = '';
        $DegreeId = '';

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

        $data = DimUser::where('IfDelete', '0')
                        ->where('UserType', 80)
                        ->where('DegreeId', 50)
                        ->select(
                            'Id',
                            'MemberNo',
                            'RealName',
                            'UserEmail',
                            'ContactPhone',
                            'ContactAddress',
                            'CompanyName',
                            'CompanyPhone',
                            'CompanyEmail',
                            'IfValid',
                            'LoginFailTimes',
                        );

        if ($IfSearch == '1') {
            // 表示會需要參考搜尋的變數
            $searchArray = array(
                'selectKeyword' => $searchFields['SelectKeyword']['value']
            );

            $data= $data->where(function($query) use ($searchArray) {
                if($searchArray['selectKeyword'] != '') {
                    $query->Where('MemberNo', 'like', '%'.$searchArray['selectKeyword'].'%' )
                        ->orWhere('RealName', 'like', '%'.$searchArray['selectKeyword'].'%' )
                        ->orWhere('UserMobile', 'like', '%'.$searchArray['selectKeyword'].'%')
                        ->orWhere('UserEmail', 'like', '%'.$searchArray['selectKeyword'].'%');
                }
            });
        }

        $data = $data->where('IfDelete', 0);

        //排序
        $forward = '';
        if($isAsc == '1'){
            $forward = 'ASC';
        }else{
            $forward = 'DESC';
        }
        // dd($orderBy);
        switch ($orderBy) {
            case '':
                $data = $data->orderBy('RealName', 'ASC');
                break;
            default:
                $data = $data->orderBy($orderBy, $forward);
                break;
        }

        // 分頁設定
        $data = $data->paginate($pageNumEachPage);

        return view(self::$viewPath.'.index', compact('data'))
                    ->with('i', ($pageNo - 1) * $pageNumEachPage)
                    ->with('IfSearch', $IfSearch)
                    ->with('pageNo', $pageNo)
                    ->with('searchFields',  $searchFields)
                    ->with('routePath', self::$routePath)
                    ->with('viewPath', self::$viewPath)
                    ->with('primaryKey', self::$primaryKey)
                    ->with('pageNumEachPage', $pageNumEachPage)
                    ->with('orderBy', $orderBy)
                    ->with('isAsc', $isAsc)
                    ->with('UserType', $UserType)
                    ->with('DegreeId', $DegreeId)
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

        // 步驟二：檢查資料是否正確
        if($requestResult['isError'] == false) {
            //check MemberNo
            $exist = DimUser::where('MemberNo', '=',  $formFields['MemberNo']['value'])
                                    ->where('IfDelete', '=', 0);;
            if($exist->count() > 0) {
                $requestResult['isError'] = true;
                $formFields['MemberNo']['isCorrect'] = false;
                $formFields['MemberNo']['error'] = "此帳號已被使用";
                $formFields['MemberNo']['completeField'] = GenerateData::generateCustomErrorMessage('Login Name','MemberNo', $formFields['MemberNo']['value'], $formFields['MemberNo']['error'], 'text');
            }

            //check Password       
            if(strlen($formFields['UserPassword']['value'])< 6) {
                $requestResult['isError'] = true;
                $formFields['UserPassword']['isCorrect'] = false;
                $formFields['UserPassword']['error'] = "密碼過短，請輸入六個字元以上";
                $formFields['UserPassword']['completeField'] = GenerateData::generateCustomErrorMessage('Login Password','UserPassword', $formFields['UserPassword']['value'], $formFields['UserPassword']['error'], 'Password');
            }
        }

        // 步驟三：檢查回傳的狀態
        if($requestResult['isError'] == false) {

            $id = Uuid::generate(4);
            $responseBody = DimUser::where('Id',$id->string)->get();
            if($responseBody->count() == 1){
                $id = Uuid::generate(4);
                $responseBody = DimUser::where('Id',$id->string)->get();
            }

            // 組成目前需要新增的資料物件;
            $newData = [
                'Id' =>  $id,
                'MemberNo' => $formFields['MemberNo']['value'],
                'UserPassword' => md5('123456'),
                'RealName' => $formFields['RealName']['value'],
                'UserEmail' => $formFields['UserEmail']['value'],
                'ContactPhone' => $formFields['ContactPhone']['value'],
                'ContactAddress' => $formFields['ContactAddress']['value'],
                'CompanyName' => $formFields['CompanyName']['value'],
                'CompanyPhone' => $formFields['CompanyPhone']['value'],
                'CompanyEmail' => $formFields['CompanyEmail']['value'],
                'IfValid' => $formFields['IfValid']['value'],
                'UserType' => 80,
                'DegreeId' => 50,
                'IfDelete' => 0,
                'CreateBy' => WebLib::getCurrentUserID(),
                'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString() //date("Y-m-d H:i:s")             //'表示為目前時間;
            ];

            // 執行產生資料的動作
            DimUser::on('mysql2')->create($newData);
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
        $data= DimUser::where('Id', $id)->first();

        // 取得輸入欄位的定義
        $formFieldDef = self::defineEditFormFields($data->first());
        // dd($formFieldDef);

        // 把資料放進欄位
        $requestResult = WebLib::generateInputsWhthData($formFieldDef, $data);
        // dd($requestRsesult);

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

        $data= DimUser::where('Id','=', $id)
                    ->where('IfDelete', '=', 0)
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

        $lastLogin = '';
        if($data->first()->tokens->count() > 0){
            $lastLogin = $data->first()->tokens->first()->RequestDate;
            $formFields['LastLogin']['value'] = $lastLogin;
            $formFields['LastLogin']['completeField'] = '<div class="parsley-row"><label for="LastLogin">Last Log-in Time</label><input disabled="disabled" id="LastLogin" class="md-input label-fixed" name="LastLogin" type="text" value="'.$lastLogin.'"></div><div class="parsley-errors-list filled"><span class="parsley-required"></span></div>';
        }

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
        $user = DimUser::find($id);
        $formFieldDef = $this->defineEditFormFields($user);
        $requestResult = WebLib::generateInputs($formFieldDef, true);
        $formFields = $requestResult["data"];

        $data= DimUser::where('Id','=', $id)->get();

        // 檢查回傳資料
        if($requestResult['isError'] == false) {
            //check Password       
            if(strlen($formFields['UserPassword']['value']) != 0 && strlen($formFields['UserPassword']['value'])< 6) {
                $requestResult['isError'] = true;
                $formFields['UserPassword']['isCorrect'] = false;
                $formFields['UserPassword']['error'] = "密碼過短，請輸入六個字元以上";
                $formFields['UserPassword']['completeField'] = GenerateData::generateCustomErrorMessage('Login Password','UserPassword', $formFields['UserPassword']['value'], $formFields['UserPassword']['error'], 'Password');
            }
        }

        if($requestResult['isError'] == false) {
            // dd($formFields);  
            $updateValue = [
                'RealName' => $formFields['RealName']['value'],
                'UserEmail' => $formFields['UserEmail']['value'],
                'ContactPhone' => $formFields['ContactPhone']['value'],
                'ContactAddress' => $formFields['ContactAddress']['value'],
                'CompanyName' => $formFields['CompanyName']['value'],
                'CompanyPhone' => $formFields['CompanyPhone']['value'],
                'CompanyEmail' => $formFields['CompanyEmail']['value'],
                'IfValid' => $formFields['IfValid']['value'],
            ];

            if($formFields['UserPassword']['value'] != '') {
                $updateValue['UserPassword'] = md5($formFields['UserPassword']['value']);
            }

            if($data->first()->IfValid == '1'&& $formFields['IfValid']['value'] == 0 ) {
                // 表示要變更IfValid狀態由1變成0, 需要變更IfNotValidBy跟IfNotValidDate
                $updateValue['IfNotValidBy'] =  WebLib::getCurrentUserID();
                $updateValue['IfNotValidDate'] = Carbon::now('Asia/Taipei')->toDateTimeString();
            }

            DimUser::on('mysql2')->where('Id', '=', $id)->update($updateValue);

            return redirect()->route(self::$routePath.'.index')
                        ->with('success','更新完成');
        }else{
            //重新產生原本的資料            
            $IfValid= ($formFields['IfValid']['value'] == '') ? $data->IfValid : $formFields['IfValid']['value'];
            $formFields['IfValid']['completeField'] = GenerateData::getIfValidHtml($IfValid);

            $CreateBy = GenerateData::getCreater($data->first()->CreateBy);
            $formFields['CreateBy']['value'] = $CreateBy;
            $formFields['CreateBy']['completeField'] = GenerateData::generateData('Created By','CreateBy', $CreateBy, '', 'text');

            // dd($formFields['CreateDate']);
            $formFields['CreateDate']['value'] = ($formFields['CreateDate']['value'] == '') ? $data->first()->CreateDate : $formFields['CreateDate']['value'];
            $formFields['CreateDate']['completeField'] = GenerateData::generateData('Created Date','CreateDate', $formFields['CreateDate']['value'], '', 'text');

            if($data->first()->tokens->count() > 0){
                $lastLogin = $data->first()->tokens->first()->RequestDate;
                $formFields['LastLogin']['value'] = $lastLogin;
                $formFields['LastLogin']['completeField'] = GenerateData::generateData('Last Log-in Time','LastLogin', $lastLogin, '', 'text');
            }

            return view(self::$viewPath.'.edit', compact('data'))
                    ->with('formFields', $formFields)
                    ->with('routePath', self::$routePath)
                    ->with('targetId', $id)
                    ->with('primaryKey', self::$primaryKey)
                    ->with('Action', "EDIT")
                    ->with('viewPath', self::$viewPath)
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
            'IfDeleteDate' => Carbon::now('Asia/Taipei')->toDateTimeString() //date("Y-m-d H:i:s")
        ];

        DimUser::on('mysql2')->where('Id', $id)->update($DeleteValue);

        return redirect()->route(self::$routePath.'.index')
                        ->with('success','資料已經刪除');
    }

    //簡易Email格式檢查
    private function emailValidation($mail){
        //format aa@aa.aa
        $result = false;
        $rule = "/^[-A-Za-z0-9_]+[-A-Za-z0-9_.]*[@]{1}[-A-Za-z0-9_]+[-A-Za-z0-9_.]*[.]{1}[A-Za-z]{2,5}$/";
        if(preg_match($rule, $mail)) $result = true;

        return $result;
    }

    public function SendNewPasswordAPI(Request $request){
        $responseBody = array(
            'status' => 0,
            'message' => 'Unknown Error'
        );

        $Id = $request->input('Id', '');
        
        if($Id == ''){
            $responseBody['status'] = 1;
            $responseBody['message'] = '無法寄發新密碼，錯誤原因：資料不足';
        }else{
            $user = DimUser::find($Id);
        }

        if($responseBody['status'] == 0){
            if($user){
                $email = $user->UserEmail;
            }else{
                $responseBody['status'] = 1;
                $responseBody['message'] = '無法寄發新密碼，錯誤原因：Email不存在';
            }
        }

        if($responseBody['status'] == 0){
            if($email){
                //get new password
                $newPassword = $this->generateCode(6);
                $responseBody['message'] = $newPassword;
                //send mail
                // $user->UserPassword = Hash::make($newPassword);
                $user->UserPassword = md5($newPassword);
                $data['title'] = '後台登入密碼';
                $data['password'] = $newPassword;
                $data['user'] = $user;
                // dd(env('AdminName'));
                try{
                    Mail::send(
                        'email.NewPassword',
                        $data,
                        function ($m) use ($user) {
                        $m->subject('後台登入密碼');
                        $m->from(env('AdminMailAddress'), env('AdminName'));
                        $m->to($user->UserEmail, $user->RealName);
                        // $m->attach($pathToFile);
                    });
                }catch(\Exception $e){
                    // dd($e);
                    $responseBody['status'] = 1;
                    $responseBody['message'] = '無法寄發新密碼，錯誤原因：'.$e->getMessage().'';
                }
                
            }else{
                $responseBody['status'] = 1;
                $responseBody['message'] = '無法寄發新密碼，錯誤原因：Email不存在';
            }
        }

        if($responseBody['status'] == 0){
            //先嘗試 mail 成功才 save
            $user->save();
            $responseBody['message'] = '新密碼已寄出，請確認是否收到電子郵件。';
        }

        return Response::json($responseBody, 200);
    }

    //產生密碼
    private function generateCode($length){
      $characters = 'WABX89GHCD45EFSTY23JKLMNPQR67';
      $charactersLength = strlen($characters);

      $code = '';

      for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, $charactersLength - 1)];
      }

      return $code;
    }
}