<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\V7Idea\WebLib;
use App\V7Idea\Neweb;
use App\Model\DimUser;
use App\UserProcessTicket;
use Response;
use Hash;

class ProfilePasswordSettingController extends Controller
{
    // 設定blade目錄的位置
    public static $viewPath = "Profile.PasswordSetting";

    // 設定route目錄的位置
    public static $routePath = "PasswordSetting";

    public function defineSearchFields($IfSearch) {

        $fields = [
	                'OldPassword' =>  [
	                            'name' => 'OldPassword',
	                            'id' => 'OldPassword',
	                            'label' => '請輸入舊密碼',
	                            'type' => 'password',
	                            'value' => '',
	                            // 'validation' => 'required',
	                            'class' => 'md-input label-fixed',
	                            // 'extras' => ['placeholder'=>'請輸入舊密碼'],
	                            ],
	                'NewPassowrd' =>  [
	                            'name' => 'NewPassowrd',
	                            'id' => 'NewPassowrd',
	                            'label' => '請輸入新密碼',
	                            'type' => 'password',
	                            'value' => '',
	                            // 'validation' => 'required',
	                            'class' => 'md-input label-fixed',
	                            // 'extras' => ['placeholder'=>'請輸入新密碼'],
	                            ],
	                'NewPassowrdCheck' =>  [
	                            'name' => 'NewPassowrdCheck',
	                            'id' => 'NewPassowrdCheck',
	                            'label' => '請再輸入一次新密碼',
	                            'type' => 'password',
	                            'value' => '',
	                            // 'validation' => 'required',
	                            'class' => 'md-input label-fixed',
	                            // 'extras' => ['placeholder'=>'請再輸入一次新密碼'],
	                            ],
                ];

        if ($IfSearch == '1'){
            $fields['OldPassword']['validation'] = 'required';
            $fields['NewPassowrd']['validation'] = 'required';
            $fields['NewPassowrdCheck']['validation'] = 'required';
        }
        return $fields;

    }
  	

    public function index(Request $request)
    {
        // $pageNumEachPage = 50;                              // 每頁的基本資料量
        // $pageNo = (int) $request->input('Page', '1');       // 目前的頁碼
        // $IsNewSearch = $request->input('IfNewSearch', '');  // 是否為新開始搜尋
        $IfSearch = $request->input('IfSearch', '');        // 是否為搜尋

        // 產生搜尋的欄位;
        $searchFields = WebLib::generateInputs(self::defineSearchFields($IfSearch), true)["data"];
        // dd($searchFields);

        // 當按下搜尋的時候，會傳回IfNewSearch = 1; 如果不是，表示空值或是其他數值;
        // 當是其他數值的時候，會依照原來的頁碼去產生回應的頁面;
        if ($IfSearch == '1') {
            // 表示會需要參考搜尋的變數
        	// dd($searchFields);
        	$checkStatus = true;

            $OldPassword = $searchFields['OldPassword']['value'];
            $NewPassowrd = $searchFields['NewPassowrd']['value'];
            $NewPassowrdCheck = $searchFields['NewPassowrdCheck']['value'];

            // dd($searchArray);
            //check input not empty

            if($OldPassword == ''){
            	$checkStatus = false;
            }else if($NewPassowrd == ''){
            	$checkStatus = false;
            }else if($NewPassowrdCheck == ''){
            	$checkStatus = false;
            }else ;

            $user = DimUser::find(WebLib::getCurrentUserID());
            //檢查舊密碼
            if($checkStatus){
            	// if (Hash::check($OldPassword, $user->UserPassword)) {
                if( md5($OldPassword) == $user->UserPassword){
            		// The passwords match...保留輸入
            		$searchFields['OldPassword']['completeField']='<div class="parsley-row"><label for="OldPassword">請輸入舊密碼<span class="req">*</span></label><input id="OldPassword" class="md-input label-fixed" name="OldPassword" type="password" value="'.$OldPassword.'"></div><div class="parsley-errors-list filled"><span class="parsley-required"></span></div>';
            	}else{
            		$checkStatus = false;
            		$searchFields['OldPassword']['error']='舊密碼不符合';
            		$searchFields['OldPassword']['completeField']='<div class="parsley-row"><label for="OldPassword">請輸入舊密碼<span class="req">*</span></label><input id="OldPassword" class="md-input label-fixed" name="OldPassword" type="password" value=""></div><div class="parsley-errors-list filled"><span class="parsley-required">舊密碼不符合</span></div>';
            	}
            }

            //新密碼與舊密碼不同
            if($checkStatus){
            	if ($NewPassowrd == $OldPassword) {
            		$checkStatus = false;
            		$searchFields['NewPassowrd']['error']='新密碼與舊密碼相同';
            		$searchFields['NewPassowrd']['completeField']='<div class="parsley-row"><label for="NewPassowrd">請再輸入一次新密碼<span class="req">*</span></label><input id="NewPassowrd" class="md-input label-fixed" name="NewPassowrd" type="password" value=""></div><div class="parsley-errors-list filled"><span class="parsley-required">新密碼與舊密碼相同</span></div>';
            	}else{
            		// match...保留輸入
            		$searchFields['NewPassowrd']['completeField']='<div class="parsley-row"><label for="NewPassowrd">請輸入新密碼<span class="req">*</span></label><input id="NewPassowrd" class="md-input label-fixed" name="NewPassowrd" type="password" value="'.$NewPassowrd.'"></div><div class="parsley-errors-list filled"><span class="parsley-required"></span></div>';
            		$searchFields['NewPassowrdCheck']['completeField']='<div class="parsley-row"><label for="NewPassowrdCheck">請再輸入一次新密碼<span class="req">*</span></label><input id="NewPassowrdCheck" class="md-input label-fixed" name="NewPassowrdCheck" type="password" value="'.$NewPassowrdCheck.'"></div><div class="parsley-errors-list filled"><span class="parsley-required"></span></div>';
            	}
            }

            //檢查新密碼 與 確認密碼
            if($checkStatus){
            	if ($NewPassowrd == $NewPassowrdCheck) {
            		// match...
            	}else{
            		$checkStatus = false;
            		$searchFields['NewPassowrdCheck']['error']='新密碼確認不符合';
            		$searchFields['NewPassowrdCheck']['completeField']='<div class="parsley-row"><label for="NewPassowrdCheck">請再輸入一次新密碼<span class="req">*</span></label><input id="NewPassowrdCheck" class="md-input label-fixed" name="NewPassowrdCheck" type="password" value=""></div><div class="parsley-errors-list filled"><span class="parsley-required">新密碼兩次輸入不相同</span></div>';
            	}
            }

            //通過檢查
            if($checkStatus){
            	$updateArr = array(
            		'UserPassword' => md5($NewPassowrd),
            		'UpdateDate' => Carbon::now('Asia/Taipei')->toDateTimeString(),
            		'UpdateBy' => WebLib::getCurrentUserID()
            	);
            	//update user
                DimUser::on('mysql2')->find(WebLib::getCurrentUserID())->update($updateArr);

            	//reset searchFields and add sucess message
            	$searchFields['OldPassword']['completeField']='<div class="parsley-row"><label for="OldPassword">請輸入舊密碼<span class="req">*</span></label><input id="OldPassword" class="md-input label-fixed" name="OldPassword" type="password" value=""></div><div class="parsley-errors-list filled"><span class="parsley-required"></span></div>';
            	$searchFields['NewPassowrd']['completeField']='<div class="parsley-row"><label for="NewPassowrd">請再輸入一次新密碼<span class="req">*</span></label><input id="NewPassowrd" class="md-input label-fixed" name="NewPassowrd" type="password" value=""></div><div class="parsley-errors-list filled"><span class="parsley-required"></span></div>';
            	$searchFields['NewPassowrdCheck']['completeField']='<div class="parsley-row"><label for="NewPassowrdCheck">請再輸入一次新密碼<span class="req">*</span></label><input id="NewPassowrdCheck" class="md-input label-fixed" name="NewPassowrdCheck" type="password" value=""></div><div class="parsley-errors-list filled"><span class="parsley-required">修改完成</span></div>';
            	return view(self::$viewPath.'.index')
            			->with('IfSearch', '')
            			->with('searchFields',  $searchFields)
            			->with('routePath', self::$routePath)
                    	->with('viewPath', self::$viewPath);
            }
        }

        return view(self::$viewPath.'.index' )
                    // ->with('i', ($pageNo - 1) * $pageNumEachPage)
                    ->with('IfSearch', $IfSearch)
                    ->with('searchFields',  $searchFields)
                    ->with('routePath', self::$routePath)
                    ->with('viewPath', self::$viewPath);
                    // ->with('result', $result)
                    // ->with('pageNumEachPage', $pageNumEachPage);

    }
}
