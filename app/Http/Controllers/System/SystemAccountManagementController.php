<?php

namespace App\Http\Controllers\System;
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
 *  這是用在管理Dim_UserType資料Controller, 將對應的幾個不用的View
 */
class SystemAccountManagementController extends Controller
{
    /// <summary>
    /// 檢查權限
    /// </summary>
    function __construct(){
        WebLib::checkUserPermission('AccountManagement');
    }
    // 設定blade目錄的位置
    public static $viewPath = "System.AccountManagement";
    
    // 設定route目錄的位置
    public static $routePath = "AccountManagement";

    // 這個資料表的主要鍵值
    public static $primaryKey = "Id";

    // 設定功能名稱
    public static $functionname = "後台帳號管理";

    public function getFromUserID(){
        $data = array(
            '' => '請選擇'
        );
        //經紀公司統一放在 UserType 20, 用 DegreeID 區分權限 
        $Companies = DimUser::select('Id','RealName')
                    ->where('IfValid', 1)
                    ->where('IfDelete', 0)
                    ->where('UserType', 20)
                    ->where('DegreeID', 20)
                    ->orderBy('RealName', 'desc');
        if($Companies->count() > 0){
            foreach($Companies->get() as $p){
                $name = $p->RealName;
                $data[$p->Id] = $name;
            }
        }
        return $data;
    }

    // 定義搜尋的欄位設定;
    public function defineSearchFields() {
        $fields = [ 
            'SelectKeyword' => [
                'name' => 'SelectKeyword',
                'id' => 'SelectKeyword',
                'label' => '關鍵字',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'company' => [
                'name' => 'company',
                'id' => 'company',
                'label' => '上層人員',
                'type' => 'text',
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'UserType' => [
                'name' => 'UserType',
                'id' => 'UserType',
                'label' => '會員群組',
                'type' => 'select',
                'selectLists' => $this->getUserTypeList(2),
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
            'DegreeId' => [
                'name' => 'DegreeId',
                'id' => 'DegreeId',
                'label' => '會員身份',
                'type' => 'select',
                'selectLists' => [
                    '' => '請選擇'
                ],
                'value' => '',
                'class' => 'md-input label-fixed',
            ],
        ];
        return $fields;
    }

    public function defineFormFields($userType='',$userDegree='',$id='') {//for create
        $fields = [ 
            'MemberNo' => [
                'name' => 'MemberNo',
                'id' => 'MemberNo',
                'label' => '登入帳號',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'UserPassword' => [
                'name' => 'UserPassword',
                'id' => 'UserPassword',
                'label' => '登入密碼',
                'type' => 'password',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'RealName' => [
                'name' => 'RealName',
                'id' => 'RealName',
                'label' => '姓名',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            // 'Member' => [
            //     'name' => 'Member',
            //     'id' => 'Member',
            //     'label' => '綁定帳號',
            //     'type' => 'text',
            //     'validation' => '',
            //     'value' => '',
            //     'class' => 'md-input label-fixed',
            //     'extras' => ['placeholder' => '帳號格式 +886-912345678']
            // ],
            'UserType' => [
                'name' => 'UserType',
                'id' => 'UserType',
                'label' => '使用者群組',
                'type' => 'select',
                'selectLists' => $this->getUserTypeList(2),
                'validation' => 'required',
                'value' => $userType,
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'DegreeId' => [
                'name' => 'DegreeId',
                'id' => 'DegreeId',
                'label' => '所屬身份',
                'type' => 'select',
                'selectLists' => $this->getUserDegreeList($userType),
                'validation' => 'required',
                'value' => $userDegree,
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            // 'FromUserID' => [
            //     'name' => 'FromUserID',
            //     'id' => 'FromUserID',
            //     'label' => '經紀公司',
            //     'type' => 'select',
            //     'selectLists' => $this->getFromUserID(),
            //     'validation' => '',
            //     'value' => '',
            //     'class' => 'md-input label-fixed',
            //     'extras' => []
            // ],
            // 'FromUser' => [
            //     'name' => 'FromUser',
            //     'id' => 'FromUser',
            //     'label' => '經紀人',
            //     'type' => 'select',
            //     'selectLists' => ['' => '請選擇'],
            //     'validation' => '',
            //     'value' => '',
            //     'class' => 'md-input label-fixed',
            //     'extras' => []
            // ],
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
            'CreateBy' => [
                'name' => 'CreateBy',
                'id' => 'CreateBy',
                'label' => '資料建立者',
                'type' => 'text',
                'value' => '系統自動產生',
                'validation' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'CreateDate' => [
                'name' => 'CreateDate',
                'id' => 'CreateDate',
                'label' => '資料建立日期',
                'type' => 'text',
                'validation' => '',
                'value' => '系統自動產生',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'LastLogin' => [
                'name' => 'LastLogin',
                'id' => 'LastLogin',
                'label' => '最後登入時間',
                'type' => 'text',
                'validation' => '',
                'value' => '系統自動產生',
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
                'label' => '登入帳號',
                'type' => 'text',
                'validation' => '',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['disabled' => 'disabled']
            ],
            'UserPassword' => [
                'name' => 'UserPassword',
                'id' => 'UserPassword',
                'label' => '登入密碼',
                'type' => 'password',
                'validation' => '',
                'value' => '********',
                'class' => 'md-input label-fixed',
                'extras' => []
            ],
            'RealName' => [
                'name' => 'RealName',
                'id' => 'RealName',
                'label' => '姓名',
                'type' => 'text',
                'validation' => 'required',
                'value' => '',
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            // 'Member' => [
            //     'name' => 'Member',
            //     'id' => 'Member',
            //     'label' => '綁定帳號',
            //     'type' => 'text',
            //     'validation' => '',
            //     'value' => '',
            //     'class' => 'md-input label-fixed',
            //     'extras' => ['placeholder' => '帳號格式 +886-912345678']
            // ],
            'UserType' => [
                'name' => 'UserType',
                'id' => 'UserType',
                'label' => '使用者群組',
                'type' => 'select',
                'selectLists' => $this->getUserTypeList(2),
                'validation' => 'required',
                'value' => $user->UserType,
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            'DegreeId' => [
                'name' => 'DegreeId',
                'id' => 'DegreeId',
                'label' => '所屬身份',
                'type' => 'select',
                'selectLists' => $this->getUserDegreeList($user->UserType),
                'validation' => 'required',
                'value' => $user->DegreeId,
                'class' => 'md-input label-fixed',
                'extras' => ['required' => 'required']
            ],
            // 'FromUserID' => [
            //     'name' => 'FromUserID',
            //     'id' => 'FromUserID',
            //     'label' => '經紀公司',
            //     'type' => 'select',
            //     'selectLists' => $this->getFromUserID(),
            //     'validation' => '',
            //     'value' => '',
            //     'class' => 'md-input label-fixed',
            //     'extras' => []
            // ],
            // 'FromUser' => [
            //     'name' => 'FromUser',
            //     'id' => 'FromUser',
            //     'label' => '經紀人',
            //     'type' => 'select',
            //     'selectLists' => ['' => '請選擇'],
            //     'validation' => '',
            //     'value' => '',
            //     'class' => 'md-input label-fixed',
            //     'extras' => []
            // ],
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
            ],
            'LastLogin' => [
                'name' => 'LastLogin',
                'id' => 'LastLogin',
                'label' => '最後登入時間',
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
            // Make sure that you call the static method currentPageResolver()
            // before querying users
            Paginator::currentPageResolver(function () use ($pageNo) {
                return $pageNo;
            });
        }else{
            $pageNo = 1;
        }
        // $company = '(SELECT A.RealName FROM Dim_User as A WHERE A.Id = Dim_User.FromUserID) AS company';

        $data = DimUser::where('IfDelete', '=', '0')
                        ->select(
                            'Id',
                            'UserType',
                            'DegreeId',
                            'MemberNo',
                            'RealName',
                            'IfValid',
                            'LoginFailTimes',
                            // DB::raw($company)
                        );

        if ($IfSearch == '1') {
            $UserType = $searchFields['UserType']['value'];
            $DegreeId = $searchFields['DegreeId']['value'];
            // 表示會需要參考搜尋的變數

            $searchArray = array(
                'selectKeyword' => $searchFields['SelectKeyword']['value'],
                'company' => $searchFields['company']['value'],
                'UserType' =>  $searchFields['UserType']['value'],
                'DegreeId' =>  $searchFields['DegreeId']['value']
            );

            $data= $data->where(function($query) use ($searchArray) {
                if($searchArray['selectKeyword'] != '') {
                    $query->Where('MemberNo', 'like', '%'.$searchArray['selectKeyword'].'%' )
                        ->orWhere('RealName', 'like', '%'.$searchArray['selectKeyword'].'%' )
                        ->orWhere('UserMobile', 'like', '%'.$searchArray['selectKeyword'].'%')
                        ->orWhere('UserEmail', 'like', '%'.$searchArray['selectKeyword'].'%');
                }
                if ($searchArray['company'] != '') {
                    $query->whereHas('company',function ($query) use($searchArray)
                    {
                        $query->where('RealName', 'like', '%' . $searchArray['company'] . '%');
                    });
                }
            });

            $data= $data->where(function($query) use ($searchArray) {
                if($searchArray['UserType'] != '') {
                    $query->where('UserType', $searchArray['UserType']);
                }

                if($searchArray['DegreeId'] != '') {
                    $query->where('DegreeId', $searchArray['DegreeId']);
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
                $data = $data->orderBy('UserType', 'ASC')
                            ->orderBy('DegreeId', 'ASC')
                            ->orderBy('RealName', 'ASC');
                break;
            case 'DegreeId':
                $data = $data->orderBy('UserType', $forward)
                            ->orderBy('DegreeId', $forward)
                            ->orderBy('RealName', $forward);
                break;
            case 'userType':
                $data = $data->orderBy('UserType', $forward)
                            ->orderBy('DegreeId', $forward)
                            ->orderBy('RealName', $forward);
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

        // 取得需要的變數
        $FromUserID = null;
        $UserType = $formFields['UserType']['value'];
        $DegreeId = $formFields['DegreeId']['value'];

        // 步驟二：檢查資料是否正確
        if($requestResult['isError'] == false) {
            //check MemberNo
            $exist = DimUser::where('MemberNo', '=',  $formFields['MemberNo']['value'])
                                    ->where('IfDelete', '=', 0);;
            if($exist->count() > 0) {
                $requestResult['isError'] = true;
                $formFields['MemberNo']['isCorrect'] = false;
                $formFields['MemberNo']['error'] = "此帳號已被使用";
                $formFields['MemberNo']['completeField'] = GenerateData::generateCustomErrorMessage('登入帳號','MemberNo', $formFields['MemberNo']['value'], $formFields['MemberNo']['error'], 'text');
            }

            //check Password       
            if(strlen($formFields['UserPassword']['value'])< 6) {
                $requestResult['isError'] = true;
                $formFields['UserPassword']['isCorrect'] = false;
                $formFields['UserPassword']['error'] = "密碼過短，請輸入六個字元以上";
                $formFields['UserPassword']['completeField'] = GenerateData::generateCustomErrorMessage('登入密碼','UserPassword', $formFields['UserPassword']['value'], $formFields['UserPassword']['error'], 'Password');
            }

            //check UserType
            if($formFields['UserType']['value'] == '') {
                $requestResult['isError'] = true;
                $formFields['UserType']['isCorrect'] = false;
                $formFields['UserType']['error'] = "請選擇使用者群組";
                $formFields['UserType']['completeField'] = $this->getUserTypeIdList($formFields['UserType']['value']);
            }

            // //check FromUserID
            // if($formFields['FromUserID']['value'] == '' && $UserType == 20 && $DegreeId == 10) {
            //     $requestResult['isError'] = true;
            //     $formFields['FromUserID']['isCorrect'] = false;
            //     $formFields['FromUserID']['error'] = "請選擇經紀公司";
            //     $formFields['FromUserID']['completeField'] = $this->getFromUserList($formFields['FromUserID']['value']);
            // }

            // //check FromUser
            // if($formFields['FromUser']['value'] == '' && $UserType == 20 && $DegreeId == 5 && $formFields['FromUserID']['value'] != '') {
            //     $requestResult['isError'] = true;
            //     $formFields['FromUser']['isCorrect'] = false;
            //     $formFields['FromUser']['error'] = "請選擇經紀人";
            //     $formFields['FromUser']['completeField'] = $this->getFromUserIDList($formFields['FromUserID']['value'],$formFields['FromUser']['value']);
            // }

            // //check Member
            // if($formFields['Member']['value'] != '' && $formFields['Member']['value'] != null) {
            //     // 先檢查有沒有這個會員
            //     $member = DimMember::select('BindUserID')
            //                         ->where('MemberNo',$formFields['Member']['value'])
            //                         ->where('IfDelete',0);
            //     if($member->count() == 0){
            //         $requestResult['isError'] = true;
            //         $formFields['Member']['isCorrect'] = false;
            //         $formFields['Member']['error'] = "前台會員不存在";
            //         $formFields['Member']['completeField'] = GenerateData::generateCustomErrorMessage('綁定會員','Member', $formFields['Member']['value'], $formFields['Member']['error'], 'text');
            //     }elseif($member->count() > 1){
            //         $requestResult['isError'] = true;
            //         $formFields['Member']['isCorrect'] = false;
            //         $formFields['Member']['error'] = "前台會員帳號異常";
            //         $formFields['Member']['completeField'] = GenerateData::generateCustomErrorMessage('綁定會員','Member', $formFields['Member']['value'], $formFields['Member']['error'], 'text');
            //     }elseif($member->count() == 1){
            //         if($member->first()->BindUserID != '' && $member->first()->BindUserID != null){
            //             $requestResult['isError'] = true;
            //             $formFields['Member']['isCorrect'] = false;
            //             $formFields['Member']['error'] = "前台會員已被綁定";
            //             $formFields['Member']['completeField'] = GenerateData::generateCustomErrorMessage('綁定會員','Member', $formFields['Member']['value'], $formFields['Member']['error'], 'text');
            //         }
            //     }
            // }
        }

        // 步驟三：檢查回傳的狀態
        if($requestResult['isError'] == false) {

            $id = Uuid::generate(4);
            $responseBody = DimUser::where('Id',$id->string)->get();
            if($responseBody->count() == 1){
                $id = Uuid::generate(4);
                $responseBody = DimUser::where('Id',$id->string)->get();
            }

            // if($UserType == 20 && $DegreeId == 10){
            //     $FromUserID = $formFields['FromUserID']['value'];
            // }elseif($UserType == 20 && $DegreeId == 5){
            //     $FromUserID = $formFields['FromUser']['value'];
            // }elseif($UserType == 20 && $DegreeId == 19){
            //     $FromUserID = $formFields['FromUserID']['value'];
            // }

            // 組成目前需要新增的資料物件;
            $newData = [
                'Id' =>  $id,
                'MemberNo' => $formFields['MemberNo']['value'],
                'UserPassword' => md5($formFields['UserPassword']['value']),
                'RealName' => $formFields['RealName']['value'],
                'IfValid' => $formFields['IfValid']['value'],
                'UserType' => $formFields['UserType']['value'],
                'DegreeId' => $formFields['DegreeId']['value'],
                // 'FromUserID' => $FromUserID,
                'IfDelete' => 0,
                'CreateBy' => WebLib::getCurrentUserID(),
                'CreateDate' => Carbon::now('Asia/Taipei')->toDateTimeString() //date("Y-m-d H:i:s")             //'表示為目前時間;
            ];

            // 執行產生資料的動作
            DimUser::on('mysql2')->create($newData);
            // update Member
            // if($formFields['Member']['value'] != '' && $formFields['Member']['value'] != null) {
            //     // 檢查有沒有會員BindUserID = UID
            //     $update1 = [
            //         'BindUserID' =>  ''
            //     ];
            //     DimMember::on('mysql2')->where('BindUserID',$id)
            //                         ->where('IfDelete',0)
            //                         ->update($update1);
            //     $update2 = [
            //         'BindUserID' =>  $id
            //     ];
            //     DimMember::on('mysql2')->where('MemberNo',$formFields['Member']['value'])
            //                         ->where('IfDelete',0)
            //                         ->update($update2 );
            // }

            return redirect()->route(self::$routePath.'.index')
                        ->with('success','成功新增一筆資料！');
        }else{
            // 表示檢查失敗，必須要重新產生要新增的頁面;
            $IfValid= ($formFields['IfValid']['value'] == '') ? 1 : $formFields['IfValid']['value'];
            $formFields['IfValid']['completeField'] = GenerateData::getIfValidHtml($IfValid);

            $formFields['CreateBy']['completeField'] = GenerateData::generateData('資料建立者','CreateBy', '系統自動產生', '', 'text');

            $formFields['CreateDate']['completeField'] = GenerateData::generateData('資料建立日期','CreateDate', '系統自動產生', '', 'text');

            //補齊DegreeId的select list
            $formFields['DegreeId']['completeField'] = $this->getDegreeIdList($formFields['UserType']['value'],$formFields['DegreeId']['value']);

            // //補齊FromUser的select list
            // $formFields['FromUser']['completeField'] = $this->getFromUserIDList($formFields['FromUserID']['value'],$formFields['FromUser']['value']);

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
        $fromUser = DimUser::where('Id','=', $data->first()->FromUserID)
                    ->where('IfDelete', '=', 0)
                    ->get();

        // 取得輸入欄位的定義
        $formFieldDef = self::defineEditFormFields($data->first());
        // dd($formFieldDef);

        // 把資料放進對應欄位
        $requestResult =  WebLib::generateInputsWhthData($formFieldDef, $data);
        // 修正資料建立者
        $requestResult['CreateBy']['value'] = GenerateData::getCreater($data->first()->CreateBy);
        if( $data->first()->DegreeId == 5 &&  $data->first()->UserType == 20){
            // 修正資料
            $requestResult['FromUserID']['value'] = $fromUser->first()->FromUserID;
            $requestResult['FromUser']['value'] = $data->first()->FromUserID;
        }

        // $member = DimMember::select('MemberNo')->where('BindUserID',$id)->where('IfDelete',0);
        // if($member->count() == 1){
        //     $requestResult['Member']['value'] = $member->first()->MemberNo;
        // }

        // 產生需要設定的欄位  
        $requestResult = WebLib::generateInputs($requestResult, false);
        // 把產生好的欄位取出來
        $formFields = $requestResult["data"];

        // 修正資料
        $formFields['DegreeId']['completeField'] = $this->getDegreeIdList($formFields['UserType']['value'],$formFields['DegreeId']['value']);
        // dd($data->first()->DegreeId,$data->first()->UserType);
        // if( $data->first()->DegreeId == 5 &&  $data->first()->UserType == 20){
        //     $fromUser = DimUser::where('Id','=', $data->first()->FromUserID)
        //             ->where('IfDelete', '=', 0)
        //             ->get();
        //     // 修正資料
        //     $formFields['FromUser']['completeField'] = $this->getFromUserIDList($formFields['FromUserID']['value'],$formFields['FromUser']['value']);
        // }
        

        $lastLogin = '';
        if($data->first()->tokens->count() > 0){
            $lastLogin = $data->first()->tokens->first()->RequestDate;
            $formFields['LastLogin']['value'] = $lastLogin;
            $formFields['LastLogin']['completeField'] = '<div class="parsley-row"><label for="LastLogin">最後登入時間</label><input disabled="disabled" id="LastLogin" class="md-input label-fixed" name="LastLogin" type="text" value="'.$lastLogin.'"></div><div class="parsley-errors-list filled"><span class="parsley-required"></span></div>';
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

        // 取得需要的變數
        $FromUserID = null;
        $UserType = $formFields['UserType']['value'];
        $DegreeId = $formFields['DegreeId']['value'];

        // 檢查回傳資料
        if($requestResult['isError'] == false) {
            //check Password       
            if(strlen($formFields['UserPassword']['value']) != 0 && strlen($formFields['UserPassword']['value'])< 6) {
                $requestResult['isError'] = true;
                $formFields['UserPassword']['isCorrect'] = false;
                $formFields['UserPassword']['error'] = "密碼過短，請輸入六個字元以上";
                $formFields['UserPassword']['completeField'] = GenerateData::generateCustomErrorMessage('登入密碼','UserPassword', $formFields['UserPassword']['value'], $formFields['UserPassword']['error'], 'Password');
            }

            // //check FromUserID
            // if($formFields['FromUserID']['value'] == '' && $UserType == 20 && $DegreeId == 10) {
            //     $requestResult['isError'] = true;
            //     $formFields['FromUserID']['isCorrect'] = false;
            //     $formFields['FromUserID']['error'] = "請選擇經紀公司";
            //     $formFields['FromUserID']['completeField'] = $this->getFromUserList($formFields['FromUserID']['value']);
            // }

            // //check FromUser
            // if($formFields['FromUser']['value'] == '' && $UserType == 20 && $DegreeId == 5 && $formFields['FromUserID']['value'] != '') {
            //     $requestResult['isError'] = true;
            //     $formFields['FromUser']['isCorrect'] = false;
            //     $formFields['FromUser']['error'] = "請選擇經紀人";
            //     $formFields['FromUser']['completeField'] = $this->getFromUserIDList($formFields['FromUserID']['value'],$formFields['FromUser']['value']);
            // }

            // //check Member
            // if($formFields['Member']['value'] != '' && $formFields['Member']['value'] != null) {
            //     // 先檢查有沒有這個會員
            //     $member = DimMember::select('BindUserID')
            //                         ->where('MemberNo',$formFields['Member']['value'])
            //                         ->where('IfDelete',0);
            //     if($member->count() == 0){
            //         $requestResult['isError'] = true;
            //         $formFields['Member']['isCorrect'] = false;
            //         $formFields['Member']['error'] = "前台會員不存在";
            //         $formFields['Member']['completeField'] = GenerateData::generateCustomErrorMessage('綁定會員','Member', $formFields['Member']['value'], $formFields['Member']['error'], 'text');
            //     }elseif($member->count() > 1){
            //         $requestResult['isError'] = true;
            //         $formFields['Member']['isCorrect'] = false;
            //         $formFields['Member']['error'] = "前台會員帳號異常";
            //         $formFields['Member']['completeField'] = GenerateData::generateCustomErrorMessage('綁定會員','Member', $formFields['Member']['value'], $formFields['Member']['error'], 'text');
            //     }elseif($member->count() == 1){
            //         if($member->first()->BindUserID != '' && $member->first()->BindUserID != null){
            //             if($member->first()->BindUserID != $id){
            //                 $requestResult['isError'] = true;
            //                 $formFields['Member']['isCorrect'] = false;
            //                 $formFields['Member']['error'] = "前台會員已被綁定";
            //                 $formFields['Member']['completeField'] = GenerateData::generateCustomErrorMessage('綁定會員','Member', $formFields['Member']['value'], $formFields['Member']['error'], 'text');
            //             }
            //         }
            //     }
            // }
        }

        if($requestResult['isError'] == false) {
            // if($UserType == 20 && $DegreeId == 10){
            //     $FromUserID = $formFields['FromUserID']['value'];
            // }elseif($UserType == 20 && $DegreeId == 5){
            //     $FromUserID = $formFields['FromUser']['value'];
            // }elseif($UserType == 20 && $DegreeId == 19){
            //     $FromUserID = $formFields['FromUserID']['value'];
            // }

            // dd($formFields);  
            $updateValue = [
                'RealName' => $formFields['RealName']['value'],
                'UserType' => $formFields['UserType']['value'],
                'DegreeId' => $formFields['DegreeId']['value'],
                // 'FromUserID' => $FromUserID,
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

            // update Member
            // if($formFields['Member']['value'] != '' && $formFields['Member']['value'] != null) {
            //     // 檢查有沒有會員BindUserID = UID
            //     $update1 = [
            //         'BindUserID' =>  ''
            //     ];
            //     DimMember::on('mysql2')->where('BindUserID',$id)
            //                         ->where('MemberNo','!=',$formFields['Member']['value'])
            //                         ->where('IfDelete',0)
            //                         ->update($update1);
            //     $update2 = [
            //         'BindUserID' =>  $id
            //     ];
            //     DimMember::on('mysql2')->where('MemberNo',$formFields['Member']['value'])
            //                         ->where('IfDelete',0)
            //                         ->update($update2 );
            // }else{
            //     $update1 = [
            //         'BindUserID' =>  ''
            //     ];
            //     DimMember::on('mysql2')->where('BindUserID',$id)
            //                         ->where('IfDelete',0)
            //                         ->update($update1);
            // }

            return redirect()->route(self::$routePath.'.index')
                        ->with('success','更新完成');
        }else{
            //重新產生原本的資料            
            $IfValid= ($formFields['IfValid']['value'] == '') ? $data->IfValid : $formFields['IfValid']['value'];
            $formFields['IfValid']['completeField'] = GenerateData::getIfValidHtml($IfValid);

            $CreateBy = GenerateData::getCreater($data->first()->CreateBy);
            $formFields['CreateBy']['value'] = $CreateBy;
            $formFields['CreateBy']['completeField'] = GenerateData::generateData('資料建立者','CreateBy', $CreateBy, '', 'text');

            // dd($formFields['CreateDate']);
            $formFields['CreateDate']['value'] = ($formFields['CreateDate']['value'] == '') ? $data->first()->CreateDate : $formFields['CreateDate']['value'];
            $formFields['CreateDate']['completeField'] = GenerateData::generateData('資料建立日期','CreateDate', $formFields['CreateDate']['value'], '', 'text');

            $formFields['DegreeId']['completeField'] = $this->getDegreeIdList($formFields['UserType']['value'],$formFields['DegreeId']['value']);
            $lastLogin = '';

            if($data->first()->tokens->count() > 0){
                $lastLogin = $data->first()->tokens->first()->RequestDate;
                $formFields['LastLogin']['value'] = $lastLogin;
                $formFields['LastLogin']['completeField'] = GenerateData::generateData('最後登入時間','LastLogin', $lastLogin, '', 'text');
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
    public function destroy(Request $request)
    {
        $authToken = \Cookie::get('authToken');
        $responseBody = array(
            'status' => 0,
            'errorCode' => '9999',
            'message' => 'Unknown error.',
        );
        $id = $request->input('ID','');
        if($id != ''){
            // 檢查後台有沒有上層=要刪除會員的會員
            $user = DimUser::where('FromUserID',$id);
            // 檢查前台有沒有上層=要刪除會員的主播
            $member = DimMember::where('BelongUserID',$id);
            if($user->count() > 0){
                $responseBody['errorCode'] = '0001';
                $responseBody['message'] = '下層還有後台人員，不可刪除';
                return Response::json($responseBody, 200);
            }else{
                if($member->count() > 0){
                    $responseBody['errorCode'] = '0002';
                    $responseBody['message'] = '下層還有主播，不可刪除';
                    return Response::json($responseBody, 200);
                }else{
                     $responseBody['status'] = 1;
                    $responseBody['message'] = '刪除成功';
                    $DeleteValue = [
                        'IfDelete' => 1, 
                        'IfDeleteBy' => WebLib::getCurrentUserID(),
                        'IfDeleteDate' => Carbon::now('Asia/Taipei')->toDateTimeString() //date("Y-m-d H:i:s")
                    ];
                    DimUser::on('mysql2')->where('Id','=', $id)->update($DeleteValue);
                    return Response::json($responseBody, 200);
                }
            }
        }else{
            $responseBody['errorCode'] = '0003';
            $responseBody['message'] = '未輸入ID';
            return Response::json($responseBody, 200);
        }
    }

    // 回傳 UserType array
    private function getUserTypeList($mode){
        $list = array('' => '請選擇');
        $types = DimUserType::select('UserTypeId', 'UserTypeName')
                            ->where('IfValid', 1)
                            ->where('IfDelete', 0)
                            ->orderBy('UserTypeId','ASC');

        if($types->count() > 0){
            foreach($types->get() as $type){
                $list[$type->UserTypeId] = $type->UserTypeName.'('.$type->UserTypeId.')';
            }
        }
        return $list;
    }   

    //回傳 UserDegree array
    private function getUserDegreeList($userType){
        $list = array('' => '請選擇');
        if($userType == ''){
            //未選擇UserType
        }else{
            $degree = DimUserDegreeToUserType::select(
                                'DegreeId', 'DegreeName'
                                )
                                ->where('IfValid', 1)
                                ->where('IfDelete', 0)
                                ->orderBy('DegreeId','ASC');
            if($degree->count() > 0){
                foreach($degree->get() as $d){
                    $list[$d->DegreeId] = $d->DegreeName.'('.$d->DegreeId.')';
                }
            }
        }

        return $list;
    }

    //GetUserDegreeList API
    public function GetUserDegreeListAPI(Request $request){
        $responseBody = array(
            'status' => 0,
            'message' => 'Unknown Error'
          );

        $userType = $request->input('UserType', '');
        if($userType == ''){
            $responseBody['message'] = '必填資料不足';
        }else{
            //find Cosmetologist
            $returnArr = array();
            $degree = DimUserDegreeToUserType::select(
                                'DegreeId', 'DegreeName'
                                )
                                ->where('UserType', $userType)
                                ->where('IfValid', 1)
                                ->where('IfDelete', 0)
                                ->orderBy('DegreeId','ASC');
            if($degree->count() > 0){
                foreach($degree->get() as $d){
                    $returnArr[$d->DegreeId] = $d->DegreeName.'('.$d->DegreeId.')';
                }
            }
            $responseBody['status'] = 1;
            $responseBody['message'] = '';
            $responseBody['data'] = $returnArr;
        }
        return Response::json($responseBody, 200);
    }

    public function getFromUser(Request $request){
        $responseBody = array(
            'status' => 0,
            'message' => 'Unknown Error'
          );

        $UID = $request->input('UID', '');
        if($UID == ''){
            $responseBody['message'] = '必填資料不足';
        }else{
            //find Cosmetologist
            $returnArr = array();
            $degree = DimUser::select('Id','RealName')
                    ->where('IfValid', 1)
                    ->where('IfDelete', 0)
                    ->where('FromUserID', $UID)
                    ->where('UserType', 20)
                    ->where('DegreeID', 10)
                    ->orderBy('RealName', 'desc');
            if($degree->count() > 0){
                foreach($degree->get() as $d){
                    $returnArr[$d->Id] = $d->RealName;
                }
            }
            $responseBody['status'] = 1;
            $responseBody['message'] = '';
            $responseBody['data'] = $returnArr;
        }
        return Response::json($responseBody, 200);
    }

    //產生userType  select list 的 html
    private function getUserTypeIdList($userType){
        $html = '<div class="parsley-row">';
        $html .= '<label for="UserType">使用者群組<span class="req">*</span></label>';
        $html .= '<br>';
        $html .= '<select id="UserType" class="md-input label-fixed" name="UserType" required>';
        $html .= '<option value="">請選擇</option>';

        $types = DimUserType::select(
                                'UserTypeId', 'UserTypeName'
                            )
                            ->where('UserTypeId', '!=', 50)
                            ->where('IfValid', 1)
                            ->where('IfDelete', 0)
                            ->orderBy('UserTypeId','ASC');
        if($types->count() > 0){
            foreach($types->get() as $t){
                if($t->UserTypeId == $userType){
                    $html .= '<option value="'.$t->UserTypeId.'" selected="selected">'.$t->UserTypeName.'('.$t->UserTypeId.')</option>';
                }else $html .= '<option value="'.$t->UserTypeId.'">'.$d->UserTypeName.'('.$t->UserTypeId.')</option>';
            }
        }
        
        
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<div class="parsley-errors-list filled"><span class="parsley-required"></span></div>';
        return $html;
    }

    //產生degree id select list 的 html
    private function getDegreeIdList($userType, $degreeId){
        $html = '<div class="parsley-row">';
        $html .= '<label for="DegreeId">所屬身份<span class="req">*</span></label>';
        $html .= '<br>';
        $html .= '<select id="DegreeId" class="md-input label-fixed" name="DegreeId" required>';
        $html .= '<option value="">請選擇</option>';

        $degree = DimUserDegreeToUserType::select(
                                'DegreeId', 'DegreeName'
                                )
                                ->where('UserType', $userType)
                                ->where('IfValid', 1)
                                ->where('IfDelete', 0)
                                ->orderBy('DegreeId','ASC');

        if($degree->count() > 0){
                foreach($degree->get() as $d){
                    if($d->DegreeId == $degreeId){
                        $html .= '<option value="'.$d->DegreeId.'" selected="selected">'.$d->DegreeName.'('.$d->DegreeId.')</option>';
                    }else $html .= '<option value="'.$d->DegreeId.'">'.$d->DegreeName.'('.$d->DegreeId.')</option>';
                }
            }
        
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<div class="parsley-errors-list filled"><span class="parsley-required"></span></div>';
        return $html;
    }

    //產生FromUser select list 的 html
    private function getFromUserIDList($UID, $FID){
        $html = '<div class="parsley-row">';
        $html .= '<label for="FromUser">經紀人<span class="req"></span></label>';
        $html .= '<br>';
        $html .= '<select id="FromUser" class="md-input label-fixed" name="FromUser" >';
        $html .= '<option value="">請選擇</option>';

        $degree = DimUser::select('Id','RealName')
                    ->where('IfValid', 1)
                    ->where('IfDelete', 0)
                    ->where('FromUserID', $UID)
                    ->where('UserType', 20)
                    ->where('DegreeID', 10)
                    ->orderBy('RealName', 'desc');

        if($degree->count() > 0){
                foreach($degree->get() as $d){
                    if($d->Id == $FID){
                        $html .= '<option value="'.$d->Id.'" selected="selected">'.$d->RealName.'</option>';
                    }else $html .= '<option value="'.$d->Id.'">'.$d->RealName.'</option>';
                }
            }
        
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<div class="parsley-errors-list filled"><span class="parsley-required"></span></div>';
        return $html;
    }

    //產生FromUser  select list 的 html
    private function getFromUserList($FromUserID){
        $html = '<div class="parsley-row">';
        $html .= '<label for="FromUserID">經紀公司<span class="req"></span></label>';
        $html .= '<br>';
        $html .= '<select id="FromUserID" class="md-input label-fixed" name="FromUserID">';
        $html .= '<option value="">請選擇</option>';

        $data = DimUser::select('Id','RealName')
                    ->where('IfValid', 1)
                    ->where('IfDelete', 0)
                    ->where('UserType', 20)
                    ->where('DegreeID', 20)
                    ->orderBy('RealName', 'desc');
        if($data->count() > 0){
            foreach($data->get() as $t){
                if($t->Id == $FromUserID){
                    $html .= '<option value="'.$t->Id.'" selected="selected">'.$t->RealName.'</option>';
                }else $html .= '<option value="'.$t->Id.'">'.$t->RealName.'</option>';
            }
        }

        $html .= '</select>';
        $html .= '</div>';
        $html .= '<div class="parsley-errors-list filled"><span class="parsley-required">請選擇經紀公司</span></div>';
        return $html;
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