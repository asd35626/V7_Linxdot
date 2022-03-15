{{-- 載入主要的版型 --}}
@extends('layouts.master')

{{-- 額外增加所需要的css檔案 --}}
@section('extraCssArea')
<style type="text/css">
    .stream_title{
        top: -6px;
        font-size: 12px;
        color: #727272;
        position: relative;
        left: 4px;
    }
</style>    
@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')
<script>
    function getUserDegreeList(UserType, DegreeId, selectListId){
      $.ajax({
        url: '/api/v1/GetUserDegreeList',
        type: 'POST',
        async: false,
        headers: {
          'Authorization': Cookies.get('authToken')
        },
        data : {
          'UserType' : UserType
        },
        success: function(data) {
          console.log('data.status:'+data.status);
          if(data.status == 1){
            $(selectListId).empty().append($('<option>', {
                value: '',
                text: '請選擇'
              }));

            $.each(data.data, function(k, v) {
                console.log('option:'+k+':'+v);
              $(selectListId).append($('<option>', {
                value: k,
                text: v
              }));
            });
          }
          
          if(DegreeId == '') ;
          else $(selectListId).val(DegreeId);
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert('error');
        },
        cache: false
      });
    }

    function DeleteUser(ID){
        if(confirm('確定要刪除嗎?')){
            $.ajax({
                url: '/api/v1/DeleteUser',
                type: 'POST',
                async: false,
                headers: {
                  'Authorization': Cookies.get('authToken')
                },
                data : {
                    'ID' : ID
                },
                success: function(data) {
                    console.log('data.status:'+data.status);
                    window.location.reload();
                    alert(data.message);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert('error');
                },
                cache: false
            });
        }
    }

    $(document).ready(function(){
        $("label[for='UserType']").addClass( "stream_title" );
        $("label[for='DegreeId']").addClass( "stream_title" );
        $('#UserType').on('change', function(){
            console.log('UserType:'+$('#UserType').val());
            if($('#UserType').val() == ''){
              if($('#DegreeId').val() != ''){
                //reset degree
                $('#DegreeId').empty().append($('<option>', {
                    value: '',
                    text: '請選擇'
                  }));
              }
            }else{
              getUserDegreeList($('#UserType').val(),'','#DegreeId');
            }
        });
        var UserType = '<?php echo $UserType?>';
        var DegreeId = '<?php echo $DegreeId?>';
        console.log('UserType:'+ UserType);
        console.log('DegreeId:'+ DegreeId);
        if(UserType == ''){

        }else{
            getUserDegreeList(UserType,DegreeId,'#DegreeId');
        }
    });

    function unlock(id){
        console.log('id:'+id);
        if(confirm('確定要解除鎖定嗎?')){
            $.ajax({
                headers: {
                    'authToken'   : Cookies.get('authToken')
                },
                type: "POST",
                url:"/api/v1/User/Unlock",
                data:{
                    UID: id 
                },
                success: function(response){
                    if(response.status == 0){
                        let target = 'tr#'+id+' td.Status';
                        let content = '<span class="uk-badge uk-badge-primary">啟用</span>';
                        $(target).html(content);
                    }else{
                        alert(response.message);
                    }
                },
                error : function(xhr, ajaxOptions, thrownError){
                    canSendGift = true;
                    switch (xhr.status) {
                        case 422:
                            if(check()){
                            // grecaptcha.reset();
                                alert("Error(422)");
                            }
                        break;
                        default:
                          // grecaptcha.reset();
                          alert('server error');
                    }
                }
            });
        }
    }
</script>
@endsection

{{-- 設定視窗的標題 --}}
@section('title', $functionname)

{{-- 設定內容的主標題區 --}}
@section('pageTitle', $functionname)

{{-- 設定內容的主標題區 --}}
@section('breadcrumbArea')
    <ul id="breadcrumbs">
        <li><a href="/Default">Home</a></li>
        <li><span>{{ $functionname }}</span></li>
    </ul>
@endsection

{{-- 設定內容 --}}
@section('content')
    <!-- search area start-->
    <h4 class="heading_a uk-margin-bottom">搜尋</h4>
    {!! Form::open(array('route' => $routePath.'.index','method'=>'GET', 'id'=> 'searchForm')) !!}
        {{ Form::hidden('IfNewSearch', '', array('id' => 'IfNewSearch')) }}
        {{ Form::hidden('IfSearch', '', array('id' => 'IfSearch')) }}
        {{ Form::hidden('Page', '', array('id' => 'Page')) }}
        {{ Form::hidden('orderBy', '', array('id' => 'orderBy')) }}
        {{ Form::hidden('isAsc', '', array('id' => 'isAsc')) }}
        <div class="uk-grid uk-margin-medium-bottom" >
            <div class="uk-width-medium-5-5 uk-row-first">
                <div class="md-card">
                    <div class="md-card-content">
                        <div data-dynamic-fields="field_template_a" dynamic-fields-counter="0">
                            <div class="uk-grid uk-grid-medium form_section" data-uk-grid-match="">
                                <div class="uk-width-10-10">
                                    <!-- uk-grid start -->
                                    <div class="uk-grid">
                                        <div class="uk-width-1-4">
                                            {!! $searchFields['SelectKeyword']['completeField'] !!}
                                        </div>
                                        {{-- <div class="uk-width-1-4">
                                            {!! $searchFields['company']['completeField'] !!}
                                        </div> --}}
                                        <div class="uk-width-1-4">
                                            {!! $searchFields['UserType']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-4">
                                            {!! $searchFields['DegreeId']['completeField'] !!}
                                        </div>
                                    </div>
                                    <!-- uk-grid end -->
                                </div>
                                <div class="uk-width-1-1" style="margin-top:20px;" >
                                    <div class="uk-width-1-1">
                                        <div onclick="search();" class="md-btn md-btn-primary">查詢</div>
                                        @if($IfSearch == '1') 
                                            <div onclick="resetForm();" class="md-btn md-btn-warning">清除搜尋</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script id="field_template_a" type="text/x-handlebars-template">
                            {{--<hr class="form_hr">--}}
                        </script>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
    <!-- search area end-->
    
    <!-- table start -->
    <h4 class="heading_a uk-margin-bottom">列表</h4>
    <div class="md-card uk-margin-medium-bottom">
        <div class="md-card-content">
            <div class="uk-overflow-container">
                <div class="uk-width-1-10" style="float:right">
                    <button type="submit" onclick="window.location.href='{{ route( $routePath.'.create') }}';" class="md-btn md-btn-primary">新增</button>
                </div>
            </div>
            
            <div class="uk-overflow-container">
                <table id="grid-basic" class="uk-table uk-table-nowrap table_check">
                    <thead>
                        <tr>
                            <th class="uk-width-1-10 uk-text-small">No.</th>
                            {!! generateHTML('userType','會員群組',$isAsc, $orderBy) !!}
                            {!! generateHTML('DegreeId','會員身份',$isAsc, $orderBy) !!}
                            {!! generateHTML('MemberNo','帳號',$isAsc, $orderBy) !!}
                            {!! generateHTML('RealName','名稱',$isAsc, $orderBy) !!}
                            {{-- {!! generateHTML('company','上層人員',$isAsc, $orderBy) !!} --}}
                            {!! generateHTML('IfValid','啟用/停用',$isAsc, $orderBy) !!}
                            <th class="uk-width-1-10 uk-text-center uk-text-small">最後登入時間</th>
                            <th class="uk-width-1-10 uk-text-center uk-text-small">功能</th>
                        </tr>
                    </thead>
                    @if($data->count() > 0)
                        @foreach ($data as $object)
                            <tr id="{{$object->Id}}">
                                <td class="uk-text-small">{{ $i = $i + 1 }}</td>
                                <td class="uk-text-small">{{ $object->userType->UserTypeName}}({{ $object->UserType }})</td>
                                <td class="uk-text-small">
                                    <?php
                                        $degree = App\Model\DimUserDegreeToUserType::where('UserType',$object->UserType )
                                                                        ->where('DegreeId',$object->DegreeId)
                                                                        ->where('IfValid', 1)
                                                                        ->where('IfDelete', 0)
                                                                        ->get()
                                                                        ->first()
                                    ?>
                                    {{ $degreeName = isset($degree) ? $degree->DegreeName : '' }}
                                    ({{ $object->DegreeId}})
                                </td>
                                <td class="uk-text-small">{{ $object->MemberNo }}</i></td>
                                <td class="uk-text-small">{{ isset($object->RealName) ? $object->RealName : '' }}</td>
                                {{-- <td class="uk-text-small">{{ isset($object->company) ? $object->company : '-' }}</td> --}}
                                <td class="uk-text-small Status">
                                    @if($object->IfValid == 0) 
                                        <span class="uk-badge uk-badge-danger">停用</span>
                                    @elseif($object->LoginFailTimes > 2)
                                        <span class="uk-badge uk-badge-danger" onclick="unlock('{{$object->Id}}')">鎖定</span>
                                    @else
                                        <span class="uk-badge uk-badge-primary">啟用</span>
                                    @endif
                                </td>
                                <td class="uk-text-small">
                                    @if($object->tokens->count() > 0)
                                        {{Carbon\Carbon::parse($object->tokens->first()->RequestDate)}}
                                    @endif
                                </td>
                                <td class="uk-text-center uk-text-small">
                                    <a href="{{ route($routePath.'.edit',$object->$primaryKey) }}">
                                        <i class="md-icon material-icons">&#xE254;</i>
                                    </a>
                                    <a onclick="DeleteUser('{{ $object->$primaryKey }}')">
                                        <i class="md-icon material-icons">&#xE872;</i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <td colspan="9" style="text-align: center;">查無資料</td>
                    @endif
                </table>
            </div>
            @include('Pagination')
        </div>
    </div>
    <!-- table end -->

    <script>
        function search() {
            $('#IfSearch').val('1');
            $('#IfNewSearch').val('1');
            $('#Page').val('1');
            $('#searchForm').submit();
        }

        function resetForm() {
            $('#SelectKeyword').val('');
            $('#company').val('');
            $('#IfSearch').val('0');
            $('#IfNewSearch').val('0');
            $('#Page').val('1');
            $('#UserType').val('');
            $('#searchForm').submit();
        }

        function gotoPage(pageNo) {
            $('#Page').val(pageNo);
            $('#IfNewSearch').val('0');
            $('#searchForm').submit();
        } 

        function orderBy(type){
            $('#excel').val(0);
            $('#Page').val('1');
            $('#orderBy').val(type);
            var a = $('#'+type);
            if(a.length > 0){
                // a[0].textContent == "keyboard_arrow_up"
                if($('#isAsc').val() == '1'){
                    $('#isAsc').val('0');
                }else{
                    $('#isAsc').val('1');
                }
            }else{
                $('#isAsc').val('1');
            }
            // console.log('orderBy', $('#orderBy').val());
            // console.log('isAsc', $('#isAsc').val());
            $('#searchForm').submit();
        }
    </script>    
@endsection
<?php 
    //產生列表的 title(id, 名稱, 排列方式, 當前排序欄位)
    function generateHTML($rawId,$rawName,$isAsc,$orderBy){
        $html = '<th class="uk-width-1-10 uk-text-small">
                    <span onclick="orderBy(\''.$rawId.'\');" style="cursor: pointer;">';
        $html .= $rawName;
        $html .= '</span>';
        //是否為當前排序
        if($orderBy == $rawId){
            $html .= '<i class="material-icons" id="'.$rawId.'">';
            //顯示箭頭
            if($isAsc){
                $html .= 'keyboard_arrow_down';
            }else{
                $html .= 'keyboard_arrow_up';
            }
            $html .= '</i>';
        }
        $html .= '</th>';
        return $html;
    }
?>