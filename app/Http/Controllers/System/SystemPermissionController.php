<?php

namespace App\Http\Controllers\System;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Model\DimUserType;
use App\Model\DimUserDegreeToUserType;
use Hash;
use Uuid;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\Neweb;
/**
 *  這是用在管理Dim_UserType資料Controller, 將對應的幾個不用的View
 */
class SystemPermissionController extends Controller
{
    /// <summary>
    /// 檢查權限
    /// </summary>
    function __construct(){
        WebLib::checkUserPermission('Permission');
    }
    // 設定blade目錄的位置
    public static $viewPath = "System.Permission";
    
    // 設定route目錄的位置
    public static $routePath = "Permission";
    
    // 這個資料表的主要鍵值
    public static $primaryKey = "UTID";

    public function getUserTypes() {
        return DimUserType::where('IfDelete', 0)
                        ->orderBy('UserTypeId', 'asc');
    }    

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
            'UserType' =>  [
                'name' => 'UserType',
                'id' => 'UserType',
                'label' => '用戶類型',
                'type' => 'select',
                'selectLists' => $this->getUserTypes()->get()->pluck('UserTypeName', 'UserTypeId')->toArray(),
                'value' => '',
                'extras' => [
                    'placeholder' => '',
                    'data-parsley-trigger' => 'change'
                ],
                'class' => 'md-input label-fixed',
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

        // 產生搜尋的欄位;
        $searchFields = WebLib::generateInputs(self::defineSearchFields(), true)["data"];

        // 當按下搜尋的時候，會傳回IfNewSearch = 1; 如果不是，表示空值或是其他數值;
        // 當是其他數值的時候，會依照原來的頁碼去產生回應的頁面;
        if($IsNewSearch != '1') {
            Paginator::currentPageResolver(function () use ($pageNo) {
                return $pageNo;
            });
        }else{
            $pageNo = 1;
        }

        $data = DimUserDegreeToUserType::where('IfDelete', '=', '0');
        if ($IfSearch == '1') {
            // 表示會需要參考搜尋的變數  
            $searchArray = array(
                'selectKeyword' => $searchFields['SelectKeyword']['value'],
                'UserType' => $searchFields['UserType']['value'],
            );

            

            $data= $data->where(function($query) use ($searchArray) {
                // dd($searchArray['userTypeID']);
                if($searchArray['selectKeyword'] != '') {
                    $query->where('DegreeName', 'like', '%'.$searchArray['selectKeyword'].'%' );
                }

                if($searchArray['UserType'] != '') {
                    $query->where('UserType', '=', $searchArray['UserType'] );
                }
            });
        }

        $data = $data->OrderBy('UserType', 'Desc')->OrderBy('DegreeId', 'DESC')->paginate($pageNumEachPage);

        return view(self::$viewPath.'.index', compact('data'))
                    ->with('i', ($pageNo - 1) * $pageNumEachPage)
                    ->with('IfSearch', $IfSearch)
                    ->with('pageNo', $pageNo)
                    ->with('searchFields',  $searchFields)
                    ->with('routePath', self::$routePath)
                    ->with('viewPath', self::$viewPath)
                    ->with('primaryKey', self::$primaryKey)
                    ->with('pageNumEachPage', $pageNumEachPage);
    }
}