<?php

namespace App\V7Idea;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Collective\Html\FormFacade;
use App\Model\DimUser;
use App\UserProcessTicket;
use Validator;
use Storage;
/**
 * 這是用在V7 Idea所製作的網站系統常用的Functions Call集合。未來會不定期進行補充;
 * 作者： Louis Chuang
 * 版權所有： 嵐奕科技有限公司; V7 Idea Technology Ltd.
 * 開發註記：
 * 2017/12/2: 在Admin_Function中增加FunctionCode的欄位，型別是Varchar(50), 用來取得這個欄位的唯一數據
 */
class WebLib {

    protected $versioncode = "V0.05";
    
    /**
     * 版本顯示;
     */
    public static function version() {

        $versioncode = "V0.05";
        return $versioncode;

    }

    /**
     * 取得目前存在用戶端的AuthToken;
     */
    public static function getAuth() {

        return $_COOKIE['authToken'];

    }

    // 取得目前這位用戶的相關資訊;
    public static function getCurrentAdmin() {

        $isSuccess = false;
        $statusMessage = "";
        $thisUser = null;

        // session_start();

        if(isset($_SESSION['user'])) {  // 檢查session是否有存入用戶的物件

            // 如果取得存在session的用戶, 就直接取出使用

            $thisUser = $_SESSION['user'];
            $statusMessage = "成功取得目前的登入會員";
            $isSuccess = true;

        } else {
            if(isset($_COOKIE['authToken'])){
                $authCode = $_COOKIE['authToken'];
            }else{
                $authCode = null;
            }

            if($authCode != null) {
            
                // 取得這張Ticket;
                $thisTicket =  UserProcessTicket::on('mysql2')->where('ProcessTicketId', $authCode)->where('IfSuccess', '1')
                                        ->where('IfLogout', '0')->get();
            
                // 表示目前這張Ticket是正確的;
                if($thisTicket->count() != 0) {
                            
                    $thisUID = $thisTicket->first()->UID;
            
                    $thisUser = DimUser::on('mysql2')->where('Id', $thisUID)
                                ->where('IfValid', 1)->
                                where('IfDelete', 0)->get()->first();
            
                    if($thisUser != null) {
            
                        $isSuccess = true;
                        $statusMessage = "成功取得目前的登入會員";
                        $_SESSION['user'] = $thisUser;

                    }
                } 
            } 
        }

        $result= [
            'status' => $isSuccess,
            'message' => $statusMessage,
            'data' => $thisUser,
        ];

        return $result;

    }

    /**
     * 檢查用戶是否有權限可以使用這個功能;
     * 1. 請帶入AdminFunction Code;
     * 2. 如果原本ADMIN_Function沒有這個功能; 請加入
     */
    public static function checkUserPermission($functionCode) {

        $result= [
            'status' => false,
            'message' => "無法取得用戶的權限",
            'IfAccess' => false,
        ];

        $getUser = self::getCurrentAdmin();

        if($getUser['status'] == true) {

            // 表示目前的瀏覽者已經登入系統網站中;

            $thisUser = $getUser['data'];

            if($thisUser != null) {

                // 取得用戶的UserType與DegreeID
                $userType = $thisUser->UserType;    // 用戶型別;
                $degreeId = $thisUser->DegreeId;    // 用戶等級表;
                $functionID = null;

                // 使用Function Code取得Function的ID;
                $adminFunctions = DB::table('ADM_Function')
                                        ->where('FunctionCode', $functionCode)
                                        ->where('IfValid', '1')
                                        ->where('IfDelete', '0')
                                        ->get()->first();

                if($adminFunctions != null) {  
                    $functionID = $adminFunctions->FunctionId;
                }

                if($functionID != null) {   // 正確取得FunctionID;

                    // 找到function後，開始去取得這個用戶的Permission;
                    $permissions = DB::table('ADM_DefaultPermission')
                                        ->where('FunctionId', $functionID)
                                        ->where('UserTypeId', $userType)
                                        ->where('UserDegreeId', $degreeId)
                                        ->get()->first();

                    if($permissions != null) {
                        if($permissions->IfAccess == 1) {
                            $result['IfAccess'] = true;
                        }
                    }
                }

                $result['status'] = true;
                $result['message'] = "成功取得用戶的權限";
  
            }

        }

        /*
            [▼
              "status" => true
              "message" => "成功取得用戶的權限"
              "IfAccess" => true
            ]
        */
        if($result['status']){
            if($result['IfAccess']){
                //pass
            }else{
                // dd('沒有權限');
                header('Location: /Default');
                exit();
            }
        }else{
            echo "<script>alert('登入失效，無法取得權限');</script>";
            echo "<script>location.replace('/Admin/Login');</script>";
            exit();
        }

    }

    /**
     * 取得目前登入的用戶ID;
     */
    public static function getCurrentUserID() {

        $userInfo = self::getCurrentAdmin();

        if($userInfo != null && $userInfo['status'] == true) {

            $thisUser = $userInfo['data'];
            
            return $thisUser->Id;

        }

        return null;

    }


    /**
     *  //' 'V1.10 New Grid Input..
     *   // '(x,0) 代表field name             
     *   // '(x,1) 代表長度
     *   // '(x,2) 代表狀態 
     *   // '  -1  -> 僅顯示文字但是沒有任何的輸入欄位; 
     *   // '  0 -> hidden; 
     *   // '  1-> Text;  
     *   // '  2 -> TextArea;  增加可以修改寬度設定，這樣可以符合比較多的需求
     *   // '  10 -> TextArea; 
     *   // '  3 -> Scoll Bar; 
     *   // '  4 -> Password; 
     *   // '  5 -> Time; 
     *   // '  6 -> Birthday; 
     *   // '  7 -> Telephone; 
     *   // '  8->Date; 
     *   // '  9-> Radio Box; 
     *   // '  11-> File上傳; (x,2) 代表最大的Size; (X,3) 代表儲存的路徑, (X,4) 代表已經上傳的位置,  (x,8) 表示型別: 0: 不區分, 1: 圖形, 2:Html; 3:Word; 4:Excel: 5:PowerPoint; 6:程式原始檔; 
     *   // '  12->Multi File上傳; (x,2) 代表最大的Size; (X,3) 代表儲存的路徑, (X,4) 代表已經上傳的位置,  (x,8) 表示型別: 0: 不區分, 1: 圖形, 2:Html; 3:Word; 4:Excel: 5:PowerPoint; 6:程式原始檔; 
     *   // '  99 -> Other 
     *   // '(x,3) 代表其他 If (x,2) = 3 時將會用到(下拉式選單與Radio Box時會參考到)。例如DataSheet(6, 3) = "Select TDMCPID, PositionTitle From ATV_TDMCPosition "
     *   // '(x,4) 代表實際內容
     *   // '(x,5) Idenifity tag 1-> Yes; Other -> No 當值為一時，該欄位會出現在List Table
     *   // '(x,6) Check datatype 1-> Need have value; 2-> Time; 3-> EMail; 4-> Number 
     *   // '(x,7) 是否在List列出 1 -> List; Other -> No
     *   // '(x,8) 如果有值時，會先用Sql command 先選出需要的值。在顯示值會用到(List) 用例：'Select status_desc from status where status_id'
     *   // '(X,9) 是否鎖死不可輸入 (0 or NULL -> 否;  1-> 是)
     *   // '(X,10) 是否ㄧ定要有值 (0 or NULL -> 不需要;  1-> 需要
     *   // '(x,11) 輸入值 型別: 1-> Text; 2->Integer  3->Time
     *   // '(x,12) 最小值 當輸入值型別為1(TEXT) 時, 則為字串最小允許長度。  當沒有值 或 NULL 時，則表示不限制
     *   // '(x,13) 最大值  當輸入值型別為1(TEXT) 時, 則為字串最大允許長度。 當沒有值 或 NULL 時，則表示不限制
     *   // '(x,14) 固定格式: 例: AN-NNNN A代表字母, N代表數字
     *   // '(x,15) 允許使用的值 例如: "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-,_."
     *   // '(x,16) 可以用來撰寫Javascript的相關功能"
     * 
     *   $setting = [
     *      'name'          => '欄位名稱',
     *      'laber'         => '使用標籤',
     *      'id'            =>  '這個設定的id',
     *      'value'         => '這個欄位的預設值或是取得的資料',
     *      'type'          => '型別:text, textarea, radio, checkbox, password, select, multiselect, file, multifile, hidden, note, info, date',
     *      'selectLists'   => '當型別是radio/checkbox box或是select時, 需要有一個Array物件, 產生選單',
     *      'validation'    => '有關於這個的條件設定;'
     *      'extras'        => '增加在input內的額外條件, 請使用array加入,'
     *      'error'        => '錯誤的資訊',
     *      'class'           => '如果有需要附加的class'    
     *   ];
     * 
     *  $output = [
     *      'name'          => '欄位名稱'
     *      'value'         => '這個欄位取得的數值'
     *      'isCorrect'     => '當$ifRequest是true, 表示需要驗證input的數值, 當驗證成功, 此欄位會是true, 驗證失敗, 就會顯示false;'
     *      'error'         => '如果這個欄位取得資料有問題，將會顯示在這邊'
     *      'inputField'    => '新增或是修改表格的輸入html字串'
     *      'completeField' => '提供完整的html字串，包含了標籤與文字;' 
     *      'setting'       => '原來的設定陣列;'
     *  ]
     * 
     */
    public static function generateInputField($setting, $ifRequest) {
        // if(!isset($setting['name'])){
        //     dd($setting, $ifRequest);
        // }
        $name = $setting['name'];
        $value = Input::get($name, $setting['value']);
        // $value = $request->input($name, $setting['value']);
        $isCorrect = null;
        $error = "";
        $inputField = "";
        $completeField = "";
        //dd($setting);
    
        if($ifRequest == true) {    // 驗證$value是否符合資料;

            if( strlen($setting['validation']) > 0) {   // 表示validation有設定;
                
                $validator = Validator::make(['value' => $value], [
                    'value' => $setting['validation'],
                ], self::errorMessage());

                if($validator->fails()) {

                    // 表示有錯誤;
                    $isCorrect = false;
                    $error = $validator->errors()->first('value'); // 取得錯誤的資訊;

                } else {

                    $isCorrect = true;
                }
            }
        }

        // 產生input的資料;
        if($setting['type'] != null) {

            $requiredTag = "";
            $label = "";
            $error = "";
            
            if(isset($setting['validation'])) {

                //dd(strpos($setting['validation'], "required"));

                if(strpos($setting['validation'], "required") !== false) {

                    // 增加顯示字樣註解星星符號
                    $requiredTag = "<span class=\"req\">*</span>";
                
                    // 在extras的陣列中增加requires
                    if(array_key_exists('extras', $setting)) {

                        if( is_array($setting['extras']) && !array_key_exists('required', $setting['extras'])) {
                            // 當在extras陣列中沒有required時，就加進去;
                            $setting['extras']['required'] = "";
                        
                        }
                    }
                    
                }
            }

           if(isset($setting['label'])) {
                $label = $setting['label'].$requiredTag;
            }

            if(isset($setting['error'])) {
                $error = $setting['error'];
            }

            if(isset($setting['extras'])) {
                // 表示有額外需要填寫的;
                if(!is_array($setting['extras'])) {  // 表示符合正確的格式;

                    //$keys = array_keys($setting['extras']);
                    // 表示傳入的不是陣列;
                    $setting['extras'] = [];    // 變成空陣列;

                }
            } else {

                $setting['extras'] = [];    // 變成空陣列;

            }

            switch($setting['type']) {

                case "text":

                    if(!isset($setting['extras']) || !is_array($setting['extras'])) {
                        $setting['extras'] = [];
                    }

                    if(!array_key_exists('id', $setting['extras']) && isset($setting['id'])) {
                        $setting['extras']['id'] = $setting['id'];
                    }

                    if(!array_key_exists('class', $setting['extras']) && isset($setting['class'])) {
                        $setting['extras']['class'] = $setting['class'];
                    }
                        
                    //dd($value);
                    $inputField = FormFacade::text($setting['name'], $value, $setting['extras'] );
                    $completeField = "<div class=\"parsley-row\">".
                                     "<label for=\"".$setting['name']."\">".$label."</label>".
                                     $inputField.
                                     "</div>".
                                     "<div class=\"parsley-errors-list filled\">".
                                     "<span class=\"parsley-required\">".$error."</span></div>";

                    break;
                
                case "textarea":

                    if(!isset($setting['extras']) || !is_array($setting['extras'])) {
                        $setting['extras'] = [];
                    }

                    if(!array_key_exists('id', $setting['extras']) && isset($setting['id'])) {
                        $setting['extras']['id'] = $setting['id'];
                    }

                    if(!array_key_exists('class', $setting['extras']) && isset($setting['class'])) {
                        $setting['extras']['class'] = $setting['class'];
                    }

                    $inputField = FormFacade::textarea($setting['name'], $value, $setting['extras'] );
                    $completeField = "<div class=\"parsley-row\">".
                                    "<label for=\"".$setting['name']."\">".$label."</label>".
                                    $inputField.
                                    "</div>".
                                    "<div class=\"parsley-errors-list filled\">".
                                    "<span class=\"parsley-required\">".$error."</span></div>";
                   
                    break;

                case "simple_textarea":

                    if(!isset($setting['extras']) || !is_array($setting['extras'])) {
                        $setting['extras'] = [];
                    }

                    if(!array_key_exists('id', $setting['extras']) && isset($setting['id'])) {
                        $setting['extras']['id'] = $setting['id'];
                    }

                    if(!array_key_exists('class', $setting['extras']) && isset($setting['class'])) {
                        $setting['extras']['class'] = $setting['class'];
                    }

                    $inputField = FormFacade::textarea($setting['name'], $value, $setting['extras'] );
                    $completeField = "<div class=\"parsley-row\"><div class=\"md-input-wrapper md-input-filled\">".
                                    "<label for=\"".$setting['name']."\">".$label."</label><br>".
                                    $inputField.
                                    "</div>".
                                    "<div class=\"parsley-errors-list filled\">".
                                    "<span class=\"parsley-required\">".$error."</span></div></div>";                   
                    break;
                
                case "radio":

                    if(count($setting['selectLists']) > 0) {

                        // 開始製作選項;

                        $keys = array_keys($setting['selectLists']);
                        
                        $radio_serial = 0;

                        foreach($keys as $item) {

                            if(isset($setting['extras']) && is_array($setting['extras'])) {
                                $extra_array = $setting['extras'];
                            } else {
                                $extra_array = [];
                            }
                       
                            $extra_array['data-md-icheck'] = "";
                            $extra_array['id'] = $setting['id']."_".$radio_serial;

                            $ifcheck = false;

                            if($item == $setting['value']) {
                                $ifcheck = true;
                            }
                            $inputField = $inputField."<span class=\"icheck-inline\">";

                            $inputField = $inputField.FormFacade::radio($setting['name'], $item, $ifcheck,  $extra_array ).
                                            "<label for=\"".$setting['id']."_".$radio_serial."\" class=\"inline-label\">".$setting['selectLists'][$item]."</label>";

                            $inputField = $inputField."</span>";

                            $radio_serial = $radio_serial + 1;
                        }

                        
                        $completeField = "<div class=\"parsley-row\"><div class=\"md-input-wrapper  md-input-filled\">".
                                        "<label for=\"".$setting['name']."\">".$label."</label><br>".
                                        $inputField.
                                        "".
                                        "<div class=\"parsley-errors-list filled\">".
                                        "<span class=\"parsley-required\">".$error."</span></div></div></div>";

                    } else {

                        // 表示沒有設定Radio box的SelectList;
                        // 就會看不到選單;

                        $inputField = "<span class=\"icheck-inline\"></span>";
                        $completeField = "<div class=\"parsley-row\">".
                                            "<label for=\"".$setting['name']."\">".$label."</label><br>".
                                            $inputField.
                                            "</div>".
                                            "<div class=\"parsley-errors-list filled\">".
                                            "<span class=\"parsley-required\">".$error."</span></div>";

                    }

                    break;

                case "checkbox":

                    if(count($setting['selectLists']) > 0) {

                        // 開始製作選項;

                        // dd($setting['selectLists']);

                        $keys = array_keys((array)$setting['selectLists']);
                        $inputField = "<span class=\"icheck-inline\">";
                        $radio_serial = 0;
                        $setting['extras']['class'] = $setting['class'];

                        foreach($keys as $item) {

                            if(isset($setting['extras']) && is_array($setting['extras'])) {
                                $extra_array = $setting['extras'];
                            } else {
                                $extra_array = [];
                            }

                            $extra_array['data-md-icheck'] = "";
                            $extra_array['id'] = $setting['id']."_".$radio_serial;

                            $ifcheck = false;

                            if(is_array($setting['value'])) {   
                                
                                // 如果checkbox是陣列，就需要進行比對;

                                foreach($setting['value'] as $subValue) {

                                    // dd($subValue);

                                    if($item == $subValue) {

                                        
                                        $ifcheck = true;
                                    }

                                }

                            } else {

                                // 如果是一般的數值，則就比對數值就好;
                                if($item == $setting['value']) {
                                    
                                    $ifcheck = true;
                                }

                            }

                            // dd($setting['selectLists']);
                            $inputField = $inputField."<span class=\"icheck-inline\">";
                            
                            $inputField = $inputField.FormFacade::checkbox($setting['name'], $item, $ifcheck,  $extra_array ).
                                             "<label for=\"".$setting['id']."_".$radio_serial."\" class=\"inline-label\">".$setting['selectLists'][$item]."</label>";

                            $inputField = $inputField."</span>";

                            $radio_serial = $radio_serial + 1;
                        }

                        

                        $completeField = "<div class=\"parsley-row\">".
                                        "<label for=\"".$setting['name']."\">".$label."</label><br>".
                                        $inputField.
                                        "</div>".
                                        "<div class=\"parsley-errors-list filled\">".
                                        "<span class=\"parsley-required\">".$error."</span></div>";

                    } else {

                        // 表示沒有設定Radio box的SelectList;
                        // 就會看不到選單;

                        $inputField = "<span class=\"icheck-inline\"></span>";
                        $completeField = "<div class=\"parsley-row\">".
                                            "<label for=\"".$setting['name']."\">".$label."</label><br>".
                                            $inputField.
                                            "</div>".
                                            "<div class=\"parsley-errors-list filled\">".
                                            "<span class=\"parsley-required\">".$error."</span></div>";

                    }

                    break;

                case "password":
                    
                    if(!isset($setting['extras']) || !is_array($setting['extras'])) {
                        $setting['extras'] = [];
                    }

                    if(!array_key_exists('id', $setting['extras']) && isset($setting['id'])) {
                        $setting['extras']['id'] = $setting['id'];
                    }

                    if(!array_key_exists('class', $setting['extras']) && isset($setting['class'])) {
                        $setting['extras']['class'] = $setting['class'];
                    }

                    $inputField = FormFacade::password($setting['name'],  $setting['extras'] );
                    $completeField = "<div class=\"parsley-row\">".
                                    "<label for=\"".$setting['name']."\">".$label."</label>".
                                    $inputField.
                                    "</div>".
                                    "<div class=\"parsley-errors-list filled\">".
                                    "<span class=\"parsley-required\">".$error."</span></div>";

                    
                    break;
                
                case "select":

                    if(!isset($setting['extras']) || !is_array($setting['extras'])) {
                        $setting['extras'] = [];
                    }

                    if(!array_key_exists('id', $setting['extras']) && isset($setting['id'])) {
                        $setting['extras']['id'] = $setting['id'];
                    }

                    if(!array_key_exists('class', $setting['extras']) && isset($setting['class'])) {
                        $setting['extras']['class'] = $setting['class'];
                    }
                    
                    $inputField = FormFacade::select($setting['name'], $setting['selectLists'], $value,  $setting['extras']);

                    $completeField = "<div class=\"parsley-row\">".
                                        "<label for=\"".$setting['name']."\">".$label."</label><br>".
                                        $inputField.
                                        "</div>".
                                        "<div class=\"parsley-errors-list filled\">".
                                        "<span class=\"parsley-required\">".$error."</span></div>";

                    break;

                case "multiselect":

                    // dd($value);

                    if(!isset($setting['extras']) || !is_array($setting['extras'])) {
                        $setting['extras'] = [];
                    }

                    if(!array_key_exists('id', $setting['extras']) && isset($setting['id'])) {
                        $setting['extras']['id'] = $setting['id'];
                    }

                    if(!array_key_exists('class', $setting['extras']) && isset($setting['class'])) {
                        $setting['extras']['class'] = $setting['class'];
                    }

                    if(!array_key_exists('multiple', $setting['extras'])) {
                        $setting['extras']['multiple'] = 'multiple';
                    }
                    
                    $inputField = FormFacade::select($setting['name'], $setting['selectLists'], $value,  $setting['extras']);

                    $completeField = "<div class=\"parsley-row\">".
                                        "<label for=\"".$setting['name']."\">".$label."</label><br>".
                                        $inputField.
                                        "</div>".
                                        "<div class=\"parsley-errors-list filled\">".
                                        "<span class=\"parsley-required\">".$error."</span></div>";


                
                    break;
                
                case "file":

                    if(!isset($setting['extras']) || !is_array($setting['extras'])) {
                        $setting['extras'] = [];
                    }

                    if(!array_key_exists('id', $setting['extras']) && isset($setting['id'])) {
                        $setting['extras']['id'] = $setting['id'];
                    }

                    if(!array_key_exists('class', $setting['extras']) && isset($setting['class'])) {
                        $setting['extras']['class'] = $setting['class'];
                    }

                    $inputField = FormFacade::file($setting['name'], $setting['extras']);
                    $value = Input::file($name, $setting['value']);

                    $completeField = "<div class=\"parsley-row\">".
                                        "<label for=\"".$setting['name']."\">".$label."</label>".
                                        $inputField.
                                    "</div>".
                                    "<div class=\"parsley-errors-list filled\">".
                                    "<span class=\"parsley-required\">".$error."</span></div>";
                    break;
                
                case "multifile":

                case "note":
                case "info":

                    if(!isset($setting['extras']) || !is_array($setting['extras'])) {
                        $setting['extras'] = [];
                    }

                    if(!array_key_exists('id', $setting['extras']) && isset($setting['id'])) {
                        $setting['extras']['id'] = $setting['id'];
                    }

                    if(!array_key_exists('class', $setting['extras']) && isset($setting['class'])) {
                        $setting['extras']['class'] = $setting['class'];
                    }

                    $inputField = FormFacade::text($setting['name'], $value, $setting['extras'] );
                    
                    $completeField = "<div class=\"parsley-row\">".
                                    "<label for=\"".$setting['name']."\">".$label."</label>".
                                    "<div class=\"".$setting['class']."\">".$setting['value']."</div>".
                                    "</div>".
                                    "<div class=\"parsley-errors-list filled\">".
                                    "<span class=\"parsley-required\">".$error."</span></div>";
                    
                    break;

                case "hidden":

                    if(!isset($setting['extras']) || !is_array($setting['extras'])) {
                        $setting['extras'] = [];
                    }

                    if(!array_key_exists('id', $setting['extras']) && isset($setting['id'])) {
                        $setting['extras']['id'] = $setting['id'];
                    }
 
                    $inputField = FormFacade::hidden($setting['name'], $value, $setting['extras'] );
                    $completeField = $inputField;
              
                    break;

                case "date":

                    if(!isset($setting['extras']) || !is_array($setting['extras'])) {
                        $setting['extras'] = [];
                    }

                    if(!array_key_exists('id', $setting['extras']) && isset($setting['id'])) {
                        $setting['extras']['id'] = $setting['id'];
                    }

                    if(!array_key_exists('class', $setting['extras']) && isset($setting['class'])) {
                        $setting['extras']['class'] = $setting['class'];
                    }
                    if (!isset($setting['extras']['data-uk-datepicker'])) {
                        $setting['extras']['data-uk-datepicker'] = "{format:'YYYY-MM-DD'}";
                    }

                    $inputField = FormFacade::text($setting['name'], $value, $setting['extras'] );
                    $completeField = "<div class=\"parsley-row\">".
                                    "<label for=\"".$setting['name']."\">".$label."</label>".
                                    $inputField.
                                    "</div>".
                                    "<div class=\"parsley-errors-list filled\">".
                                    "<span class=\"parsley-required\">".$error."</span></div>";


                    break;
                
                case "number":
                    if(!isset($setting['extras']) || !is_array($setting['extras'])) {
                        $setting['extras'] = [];
                    }

                    if(!array_key_exists('id', $setting['extras']) && isset($setting['id'])) {
                        $setting['extras']['id'] = $setting['id'];
                    }

                    if(!array_key_exists('class', $setting['extras']) && isset($setting['class'])) {
                        $setting['extras']['class'] = $setting['class'];
                    }
                        

                    $inputField = FormFacade::number($setting['name'], $value, $setting['extras'] );
                    $completeField = "<div class=\"parsley-row\">".
                                     "<label for=\"".$setting['name']."\">".$label."</label>".
                                     $inputField.
                                     "</div>".
                                     "<div class=\"parsley-errors-list filled\">".
                                     "<span class=\"parsley-required\">".$error."</span></div>";

                    break;
            }

        }

        $output = [
            'name' => $setting['name'],
            'value' => $value,
            'isCorrect' => $isCorrect,
            'error' => $error,
            'inputField' => $inputField,
            'completeField' => $completeField,
            'setting' => $setting,
        ];

        return $output;

    }

    /**
     * 產生與檢查輸入值;
     * 回傳的Data包含：
     *  'isError' => '是否有輸入錯誤, 如果沒有檢查Request, 則永遠都是true;'
     *  'error' => '錯誤的列表，來自於validator;'
     *  'input' => '產生出的輸入物件; 從self::generateInputField()產生;
     */ 
    public static function generateInputs($inputList, $ifCheckRequest) {

        $status = false;
        $message = "";
        $data = null;
        $isError = false;
        $errors = null;
        $input = [];

        $keys = array_keys($inputList);
        //dd($inputList);
        if($ifCheckRequest) {   // 表示要檢查輸入;

            $values = [];
            $validConditions = [];

            $i = 0;

            for($i = 0; $i < count($inputList); $i ++) {

                $requestName = $inputList[$keys[$i]]['name'];
                $requestName = str_replace('[', '', $requestName);
                $requestName = str_replace(']', '', $requestName);

                switch ($inputList[$keys[$i]]['type']) {

                    case "file":
                        $inputList[$keys[$i]]['value'] = Input::file($requestName, '');
                        break;
                    
                    default:
                        $inputList[$keys[$i]]['value'] = Input::get($requestName, '');
                        //dd($inputList);
                        break;
                
                    }

                // dd($inputList[$keys[$i]]['name'].'->'.Input::get($inputList[$keys[$i]]['name']));

                if(isset($inputList[$keys[$i]]['validation'])) {
                    
                    $values[$inputList[$keys[$i]]['name']] = $inputList[$keys[$i]]['value'];
                    $validConditions[$inputList[$keys[$i]]['name']] = $inputList[$keys[$i]]['validation'];
                    
                }

            }

            if(count($validConditions) > 0) {

                $validator = Validator::make($values, $validConditions, self::errorMessage());

                if($validator->fails()) {

                    // 表示有錯誤;
                    $isError = true;
                    $errors = $validator->errors(); // 取得錯誤的資訊;

                    //將錯誤資訊回填到所有的欄位中;
                    for($i = 0; $i < count($inputList); $i ++) {

                        if($errors->first($inputList[$keys[$i]]['name']) != null) {
                            
                            $inputList[$keys[$i]]['error'] = $errors->first($inputList[$keys[$i]]['name']);

                        }
                    }

                } else {

                    $isError = false;
                    
                }

            }

        }

        for($i = 0; $i < count($inputList); $i ++) {
         
            $input[$keys[$i]] = self::generateInputField($inputList[$keys[$i]], false);
        }
    
        $output = [

            'isError' => $isError,
            'errors' => $errors,
            'data' => $input

        ];

        return $output;

    }

    /**
     * Bind Data: 
     *  1. 確認Data如果有值，自動可以Mapping到data裡
     *  使用範例：
     *    // 找到目前要編輯的資料
     *    $data= ETickets::where('SEID', $id)->get();
     *    // 取得欄位定義
     *    $formFieldDef = $this->editFieldSetting();
     *    // 將資料對應到欄位定義的value中
     *    $requestResult =  WebLib::generateInputsWhthData($formFieldDef, $data);
     *     ......... 如果還有其他欄位值，是從其他的資料表找出來，在此處理....
     *    // 產生輸入欄位結果
     *    $requestResult = WebLib::generateInputs($requestResult, false);
     */
    public static function generateInputsWhthData($inputList, $data) {

        $isError = false;
        // 步驟1: 先做資料的mapping

        $dataArray = isset($data->toArray()[0]) ? $data->toArray()[0] : $data->toArray();

        if(is_array($inputList) && count($inputList) > 0) {  // 表示輸入的資料是array;

            // 取得Keys數值;
            $keys = array_keys($inputList);

            $i = 0;
           
            for($i = 0; $i < count($inputList); $i ++) {

                // dd($dataArray);
                //dd($keys[$i]);

                // dd(array_key_exists($keys[$i], $dataArray));

                if(array_key_exists($keys[$i], $dataArray)) {

                    $value = $dataArray[$keys[$i]];
                    $inputList[$keys[$i]]['value'] = $value;

                    // dd($value);

                }
   
            }
        }

        // 步驟2: mapping好了之後，產生欄位
        // return self::generateInputs($inputList, false);
        return $inputList;

    }



    // 客製化錯誤的訊息;
    public static function errorMessage() {

        return [
            
                /*
                |--------------------------------------------------------------------------
                | Validation Language Lines
                |--------------------------------------------------------------------------
                |
                | The following language lines contain the default error messages used by
                | the validator class. Some of these rules have multiple versions such
                | as the size rules. Feel free to tweak each of these messages here.
                |
                */
            
                'accepted'             => '這必須要勾選',
                'active_url'           => '這不是一個存在的URL.',
                'after'                => '這必須要選擇在:date之後的日期',
                'alpha'                => '這只能填寫英文字母',
                'alpha_dash'           => '這只能填寫英文字母、數字或是底線。',
                'alpha_num'            => '這只能填寫英文字母或是數字.',
                'array'                => '這必須是一個陣列',
                'before'               => '這必須要選擇在:date之前的日期',
                'between'              => [
                    'numeric' => 'The :attribute must be between :min and :max.',
                    'file'    => 'The :attribute must be between :min and :max kilobytes.',
                    'string'  => 'The :attribute must be between :min and :max characters.',
                    'array'   => 'The :attribute must have between :min and :max items.',
                ],
                'boolean'              => 'The :attribute field must be true or false.',
                'confirmed'            => 'The :attribute confirmation does not match.',
                'date'                 => '這不是一個合乎規格的日期格式',
                'date_format'          => 'The :attribute does not match the format :format.',
                'different'            => 'The :attribute and :other must be different.',
                'digits'               => 'The :attribute must be :digits digits.',
                'digits_between'       => 'The :attribute must be between :min and :max digits.',
                'distinct'             => 'The :attribute field has a duplicate value.',
                'email'                => 'The :attribute must be a valid email address.',
                'exists'               => 'The selected :attribute is invalid.',
                'filled'               => 'The :attribute field is required.',
                'image'                => 'The :attribute must be an image.',
                'in'                   => 'The selected :attribute is invalid.',
                'in_array'             => 'The :attribute field does not exist in :other.',
                'integer'              => 'The :attribute must be an integer.',
                'ip'                   => 'The :attribute must be a valid IP address.',
                'json'                 => 'The :attribute must be a valid JSON string.',
                'max'                  => [
                    'numeric' => 'The :attribute may not be greater than :max.',
                    'file'    => 'The :attribute may not be greater than :max kilobytes.',
                    'string'  => 'The :attribute may not be greater than :max characters.',
                    'array'   => 'The :attribute may not have more than :max items.',
                ],
                'mimes'                => 'The :attribute must be a file of type: :values.',
                'min'                  => [
                    'numeric' => 'The :attribute must be at least :min.',
                    'file'    => 'The :attribute must be at least :min kilobytes.',
                    'string'  => 'The :attribute must be at least :min characters.',
                    'array'   => 'The :attribute must have at least :min items.',
                ],
                'not_in'               => 'The selected :attribute is invalid.',
                // 'numeric'              => 'The :attribute must be a number.',
                'numeric'              => '這不是一個數字',
                'present'              => 'The :attribute field must be present.',
                'regex'                => '格式不正確',
                'required'             => '這必須要填寫',
                // 'required_if'          => 'The :attribute field is required when :other is :value.',
                'required_if'          => '這必須要填寫',
                'required_unless'      => 'The :attribute field is required unless :other is in :values.',
                'required_with'        => 'The :attribute field is required when :values is present.',
                'required_with_all'    => 'The :attribute field is required when :values is present.',
                'required_without'     => 'The :attribute field is required when :values is not present.',
                'required_without_all' => 'The :attribute field is required when none of :values are present.',
                'same'                 => 'The :attribute and :other must match.',
                'size'                 => [
                    'numeric' => 'The :attribute must be :size.',
                    'file'    => 'The :attribute must be :size kilobytes.',
                    'string'  => 'The :attribute must be :size characters.',
                    'array'   => 'The :attribute must contain :size items.',
                ],
                'string'               => '這必須是一個字串',
                'timezone'             => '這個 :attribute must be a valid zone.',
                'unique'               => '這已經存在相同的數值',
                'url'                  => '這不是一個正確的網址格式',
                'MemberNo.exists' => '該會員帳號不存在',
            
                /*
                |--------------------------------------------------------------------------
                | Custom Validation Language Lines
                |--------------------------------------------------------------------------
                |
                | Here you may specify custom validation messages for attributes using the
                | convention "attribute.rule" to name the lines. This makes it quick to
                | specify a specific custom language line for a given attribute rule.
                |
                */
            
                'custom' => [
                    'attribute-name' => [
                        'rule-name' => 'custom-message',
                    ],
                ],
            
                /*
                |--------------------------------------------------------------------------
                | Custom Validation Attributes
                |--------------------------------------------------------------------------
                |
                | The following language lines are used to swap attribute place-holders
                | with something more reader friendly such as E-Mail Address instead
                | of "email". This simply helps us make messages a little cleaner.
                |
                */
            
                'attributes' => [],
            
            ];



    }

    /**
     * 上傳圖片;
     * 1. 請帶入file 實體;
     */
    // public static function processImage($file ='', $path = '')
    // {
    //     if ($file->isValid()) {
    //         $fileExtension = strtolower($file->getClientOriginalExtension());

    //         if(($fileExtension == "jpg")
    //             or($fileExtension == "png")
    //             or($fileExtension == "gif"))
    //         {
    //             $fileName = str_random(15) .".". $file->getClientOriginalExtension();
    //             $image = $path. $fileName;

    //             $getNewFileName = true;
    //             while($getNewFileName){
    //                 if(is_file($path.$fileName)){//檢查是否已有此檔案名稱
    //                     $fileName = str_random(15) .".". $file->getClientOriginalExtension();
    //                     $image = $path. $fileName;
    //                 }else{
    //                     $getNewFileName = false;
    //                 }
    //             }

    //             $file->move(storage_path($path), $image);
    //             return $image;
    //         }else{
    //             return false;
    //         }
    //     } else {
    //         return false;
    //     }
    // }

    /**
     * 上傳圖片至 S3;
     * 1. 請帶入file 實體;
     */
    public static function processImage($file ='', $path = '')
    {
        // $s3 = Storage::disk('s3');
        if ($file->isValid()) {
            $fileExtension = strtolower($file->getClientOriginalExtension());

            if(($fileExtension == "jpg")
                or($fileExtension == "jpeg")
                or($fileExtension == "png")
                or($fileExtension == "gif"))
            {
                $fileName = str_random(15) .".". $file->getClientOriginalExtension();
                $imagePath = $path. $fileName;

                $getNewFileName = true;
                while($getNewFileName){
                    // if($s3->exists($imagePath)){//檢查是否已有此檔案名稱
                    if(file_exists($imagePath)){
                        $fileName = str_random(15) .".". $file->getClientOriginalExtension();
                        $imagePath = $path. $fileName;
                    }else{
                        $getNewFileName = false;
                    }
                }

                // $t = $s3->put($imagePath, file_get_contents($file), 'public');
                $file->move(storage_path($path), $imagePath);
                // dd($s3);
                // $url = $s3->url($imagePath);
                return $imagePath;
            }else{
                return false;
            }
        } else {
            return false;
        }
    }

    public static function processFile($file ='', $path = '')
    {
        // $userId = WebLib::getCurrentUserID();
        if ($file->isValid()) {
            $fileExtension = strtolower($file->getClientOriginalExtension());
            $fileName = $file->getClientOriginalExtension().'_'.date( "YmdHis", strtotime("now")).'.'.$file->getClientOriginalExtension();
            $imagePath = $path. $fileName;
            $file->move(storage_path($path), $imagePath);
            // $destinationPath = base_path() . $path;
            // $file->move($destinationPath, $imagePath);
            return $imagePath;
        } else {
            return false;
        }
    }

    // for BannerController
    public static function generateInputsWhthDataArray($inputList, $data) {

        $isError = false;
        // 步驟1: 先做資料的mapping
        $dataArray = $data;

        if(is_array($inputList) && count($inputList) > 0) {  // 表示輸入的資料是array;

            // 取得Keys數值;
            $keys = array_keys($inputList);

            $i = 0;
           
            for($i = 0; $i < count($inputList); $i ++) {
                // dd($inputList);
                // dd($dataArray->BID);
                // dd($keys[$i]);

                // dd(array_key_exists($keys[$i], $dataArray));

                if(array_key_exists($keys[$i], $dataArray)) {

                    $value = $dataArray[$keys[$i]];
                    $inputList[$keys[$i]]['value'] = $value;

                    // dd($value);

                }
   
            }
        }

        // 步驟2: mapping好了之後，產生欄位
        // return self::generateInputs($inputList, false);
        return $inputList;

    }
} 