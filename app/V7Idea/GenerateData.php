<?php

namespace App\V7Idea;
use Illuminate\Support\Facades\Input;
use Collective\Html\FormFacade;
use App\Model\DimUser;
use App\Model\DimMember;

class GenerateData {

    // 產生錯誤訊息的html
    public static function generateCustomErrorMessage($labelName ,$fieldName , $value, $message, $type){
        $html = '<div class="parsley-row">';
        $html .= '<label for="'.$fieldName.'"><span class="req">*</span>'.$labelName.'</label>';
        $html .= '<input id="'.$fieldName.'" class="md-input label-fixed" name="'.$fieldName.'" type="'.$type.'" value="'.$value.'" required>';
        $html .= '</div>';
        $html .= '<div class="parsley-errors-list filled">';
        $html .= '<span class="parsley-required">'.$message.'</span>';
        $html .= '</div>';

        return $html;
    }

    //產生錯誤訊息的html(非必填)
    public static function generateErrorMessage($labelName ,$fieldName , $value, $message, $type){
        $html = '<div class="parsley-row">';
        $html .= '<label for="'.$fieldName.'">'.$labelName.'</label>';
        $html .= '<input id="'.$fieldName.'" class="md-input label-fixed" name="'.$fieldName.'" type="'.$type.'" value="'.$value.'">';
        $html .= '</div>';
        $html .= '<div class="parsley-errors-list filled">';
        $html .= '<span class="parsley-required">'.$message.'</span>';
        $html .= '</div>';

        return $html;
    }

    // 產生錯誤訊息的html
    public static function generateTextareaErrorMessage($labelName ,$fieldName , $value, $message, $type){
        $html = '<div class="parsley-row">';
        $html .= '<label for="'.$fieldName.'"><span class="req">*</span>'.$labelName.'</label>';
        $html .= '<textarea id="'.$fieldName.'" class="tinymce abel-fixed" name="'.$fieldName.'" value="'.$value.'cols="50" rows="10"></textarea>';
        $html .= '</div>';
        $html .= '<div class="parsley-errors-list filled">';
        $html .= '<span class="parsley-required">'.$message.'</span>';
        $html .= '</div>';

        return $html;
    }

    // 產生錯誤訊息的html(上傳檔案使用)
    public static function FileErrorMessage($labelName ,$fieldName , $value, $message, $type){
        $html = '<div class="parsley-row">';
        $html .= '<label for="'.$fieldName.'class="stream_title" "><span class="req">*</span>'.$labelName.'</label>';
        $html .= '<input id="'.$fieldName.'" class="label-fixed dropify" name="'.$fieldName.'" type="'.$type.'" value="'.$value.'" required>';
        $html .= '</div>';
        $html .= '<div class="parsley-errors-list filled">';
        $html .= '<span class="parsley-required">'.$message.'</span>';
        $html .= '</div>';
        return $html;
    }

    // 重新產生 IfValid html
    public static function getIfValidHtml($IfValid) {
        $Html = '';
        if($IfValid == 1){
            $Html = '<div class="parsley-row">
                        <div class="md-input-wrapper  md-input-filled">
                            <label for="IfValid">Active/ Inactive<span class="req">*</span></label><br>
                            <span class="icheck-inline">
                                <input data-md-icheck="" id="IfValid_1" checked="checked" name="IfValid" type="radio" value="1">
                                <label for="IfValid_1" class="inline-label">Active</label>
                            </span>
                            <span class="icheck-inline">
                                <input data-md-icheck="" id="IfValid_0" name="IfValid" type="radio" value="0">
                                <label for="IfValid_0" class="inline-label">Inactive</label>
                            </span>
                            <div class="parsley-errors-list filled">
                                <span class="parsley-required"></span>
                            </div>
                        </div>
                    </div>';
        }else{
            $Html = '<div class="parsley-row">
                        <div class="md-input-wrapper  md-input-filled">
                            <label for="IfValid">Active/ Inactive<span class="req">*</span></label><br>
                            <span class="icheck-inline">
                                <input data-md-icheck="" id="IfValid_1" name="IfValid" type="radio" value="1">
                                <label for="IfValid_1" class="inline-label">Active</label>
                            </span>
                            <span class="icheck-inline">
                                <input data-md-icheck="" id="IfValid_0" checked="checked" name="IfValid" type="radio" value="0">
                                <label for="IfValid_0" class="inline-label">Inactive</label>
                            </span>
                            <div class="parsley-errors-list filled">
                                <span class="parsley-required"></span>
                            </div>
                        </div>
                    </div>';
        }

        $result = $Html;
        // dd($result);
        return $result;
    }

    // 重新產生 radio html
    public static function getRadioHtml($name,$id,$r0,$r1,$value) {
        $Html = '';
        if($value == 1){
            $Html = '<div class="parsley-row">
                        <div class="md-input-wrapper  md-input-filled">
                            <label for="'.$id.'">'.$name.'<span class="req">*</span></label><br>
                            <span class="icheck-inline">
                                <input data-md-icheck="" id="SpeakAuth_1" checked="checked" name="'.$id.'" type="radio" value="1">
                                <label for="SpeakAuth_1" class="inline-label">'.$r1.'</label>
                            </span>
                            <span class="icheck-inline">
                                <input data-md-icheck="" id="SpeakAuth_0" name="'.$id.'" type="radio" value="0">
                                <label for="SpeakAuth_0" class="inline-label">'.$r0.'</label>
                            </span>
                            <div class="parsley-errors-list filled">
                                <span class="parsley-required"></span>
                            </div>
                        </div>
                    </div>';
        }else{
            $Html = '<div class="parsley-row">
                        <div class="md-input-wrapper  md-input-filled">
                            <label for="'.$id.'">'.$name.'<span class="req">*</span></label><br>
                            <span class="icheck-inline">
                                <input data-md-icheck="" id="SpeakAuth_1" name="'.$id.'" type="radio" value="1">
                                <label for="SpeakAuth_1" class="inline-label">'.$r1.'</label>
                            </span>
                            <span class="icheck-inline">
                                <input data-md-icheck="" id="SpeakAuth_0" checked="checked" name="'.$id.'" type="radio" value="0">
                                <label for="SpeakAuth_0" class="inline-label">'.$r0.'</label>
                            </span>
                            <div class="parsley-errors-list filled">
                                <span class="parsley-required">
                            </span>
                            </div>
                        </div>
                    </div>';
        }
        return $Html;
    }

    //產生回填資料的html 限 type test & disable
    public static function generateData($labelName ,$fieldName , $value, $message, $type){
        $html = '<div class="parsley-row">';
        $html .= '<label for="'.$fieldName.'">'.$labelName.'<span class="req">*</span></label>';
        $html .= '<input disabled="disabled" id="'.$fieldName.'" class="md-input label-fixed" name="'.$fieldName.'" type="'.$type.'" value="'.$value.'" required>';
        $html .= '</div>';
        $html .= '<div class="parsley-errors-list filled">';
        $html .= '<span class="parsley-required">'.$message.'</span>';
        $html .= '</div>';

        return $html;
    }

    //產生回填資料的html 限 file
    public static function generateFileData($setting){
        $requiredTag = "";
            
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
        $value = Input::file($setting['name'], $setting['value']);

        $completeField = "<div class=\"parsley-row\">".
                            "<label for=\"".$setting['name']."\">".$label."</label>".
                            $inputField.
                        "</div>".
                        "<div class=\"parsley-errors-list filled\">".
                        "<span class=\"parsley-required\"></span></div>";

        return $completeField;
    }

    // 取得資料建立者
    public static function getCreater($MID) {
        $Creater = '';
        // dd($MID);

        // // 先找DimMember
        // $Creaters = DimMember::select('RealName')
        //                     ->where('IfValid', 1)
        //                     ->where('IfDelete',0)
        //                     ->where('MID',$MID);
        
        // // 如果有就取出名字，沒有就檢查DimUser
        // if($Creaters->count() != 0){
        //     $Creater = $Creaters->first()->RealName;
        //     // dd($Creater->first()->RealName);  
        // }else{
        //     $Creaters = DimUser::select('RealName')
        //                     ->where('IfValid', 1)
        //                     ->where('IfDelete',0)
        //                     ->where('Id',$MID);
        // }

        $Creaters = DimUser::select('RealName')
                            ->where('IfValid', 1)
                            ->where('IfDelete',0)
                            ->where('Id',$MID);

        // 如果DimUser有就取出RealName，沒有就預設文字
        if($Creaters->count() != 0){
            $Creater = $Creaters->first()->RealName;
        }else{
            $Creater = 'CreateBy';
        }

        $result = $Creater;
        // dd($result);
        return $result;
    }

    // 取得最後修改人員
    public static function getLastModifiedBy($MID) {
        $LastModifiedBy = '';
        // dd($MID);
        if($MID != ''){
            // 先找DimMember
            $LastModifiedBys = DimMember::select('RealName')
                                ->where('IfValid', 1)
                                ->where('IfDelete',0)
                                ->where('MID',$MID);
            
            // 如果有就取出名字，沒有就檢查DimUser
            if($LastModifiedBys->count() != 0){
                $LastModifiedBy = $LastModifiedBys->first()->RealName;
                // dd($LastModifiedBy->first()->RealName);  
            }else{
                $LastModifiedBys = DimUser::select('RealName')
                                ->where('IfValid', 1)
                                ->where('IfDelete',0)
                                ->where('Id',$MID);
            }

            // 如果DimUser有就取出RealName，沒有就預設文字
            if($LastModifiedBys->count() != 0){
                $LastModifiedBy = $LastModifiedBys->first()->RealName;
            }else{
                $LastModifiedBy = 'LastModifiedBy';
            }
        }else{
            $LastModifiedBy = 'LastModifiedBy';
        }
        $result = $LastModifiedBy;
        // dd($result);
        return $result;
    }

    //簡易Email格式檢查
    private function emailValidation($mail){
        //format aa@aa.aa
        $result = false;
        $rule = "/^[-A-Za-z0-9_]+[-A-Za-z0-9_.]*[@]{1}[-A-Za-z0-9_]+[-A-Za-z0-9_.]*[.]{1}[A-Za-z]{2,5}$/";
        if(preg_match($rule, $mail)) $result = true;

        return $result;
    }

    //回傳 MemberType array
    // mode 1 : 包含 美容師群組, type 2 : 不含美容師
    private function getMemberTypeList($mode){
        $list = array('' => '請選擇');
        $types = DimMemberType::select(
                                'MemberTypeId', 'MemberTypeName'
                            );
        if($mode == 1){
            //nothing to do
        }else{
            $types = $types->where('MemberTypeId', '!=', 50);
        }

        $types = $types->where('IfValid', 1)
                        ->where('IfDelete', 0)
                        ->orderBy('MemberTypeId','ASC');

        if($types->count() > 0){
            foreach($types->get() as $type){
                $list[$type->MemberTypeId] = $type->MemberTypeName.'('.$type->MemberTypeId.')';
            }
        }

        return $list;
    } 
    
    //回傳 MemberDegree array
    private function getMemberDegreeList($MemberType){
        $list = array('' => '請選擇');
        if($MemberType == ''){
            //未選擇MemberType
        }else{
            $degree = DimMemberDegreeToMemberType::select(
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

    //GetMemberDegreeList API
    public function GetMemberDegreeListAPI(Request $request){
        $responseBody = array(
            'status' => 0,
            'message' => 'Unknown Error'
        );

        $MemberType = $request->input('MemberType', '');
        if($MemberType == ''){
            $responseBody['message'] = '必填資料不足';
        }else{
            //find Cosmetologist
            $returnArr = array();
            $degree = DimMemberDegreeToMemberType::select(
                                'DegreeId', 'DegreeName'
                                )
                                ->where('MemberType', $MemberType)
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

    //產生MemberType  select list 的 html
    private function getMemberTypeIdList($MemberType){
        $html = '<div class="parsley-row">';
        $html .= '<label for="MemberType">使用者群組<span class="req">*</span></label>';
        $html .= '<br>';
        $html .= '<select id="MemberType" class="md-input label-fixed" name="MemberType" required>';
        $html .= '<option value="">請選擇</option>';

        $types = DimMemberType::select(
                                'MemberTypeId', 'MemberTypeName'
                            )
                            ->where('MemberTypeId', '!=', 50)
                            ->where('IfValid', 1)
                            ->where('IfDelete', 0)
                            ->orderBy('MemberTypeId','ASC');
        if($types->count() > 0){
            foreach($types->get() as $t){
                if($t->MemberTypeId == $MemberType){
                    $html .= '<option value="'.$t->MemberTypeId.'" selected="selected">'.$t->MemberTypeName.'('.$t->MemberTypeId.')</option>';
                }else $html .= '<option value="'.$t->MemberTypeId.'">'.$d->MemberTypeName.'('.$t->MemberTypeId.')</option>';
            }
        }
        
        
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<div class="parsley-errors-list filled"><span class="parsley-required"></span></div>';
        return $html;
    }

    //產生degree id select list 的 html
    private function getDegreeIdList($MemberType, $degreeId){
        $html = '<div class="parsley-row">';
        $html .= '<label for="DegreeId">所屬身份<span class="req">*</span></label>';
        $html .= '<br>';
        $html .= '<select id="DegreeId" class="md-input label-fixed" name="DegreeId" required>';
        $html .= '<option value="">請選擇</option>';

        $degree = DimMemberDegreeToMemberType::select(
                                'DegreeId', 'DegreeName'
                                )
                                ->where('MemberType', $MemberType)
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

    /**
     * 上傳影片;
     * 1. 請帶入file 實體;
     */
    public static function processVideo($file ='', $path = '')
    {
        // dd(123);
        if ($file->isValid()) {
            $fileExtension = strtolower($file->getClientOriginalExtension());

            if($fileExtension == "mp4")
            {
                $fileName = str_random(15) .".". $fileExtension;
                $imagePath = $path. $fileName;

                $getNewFileName = true;
                while($getNewFileName){
                    // if($s3->exists($imagePath)){//檢查是否已有此檔案名稱
                    if(file_exists($imagePath)){
                        $fileName = str_random(15) .".". $fileExtension;
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
}
?>