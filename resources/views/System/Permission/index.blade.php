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
<script type="text/javascript">
    $(document).ready(function(){
        $("label[for='UserType']").addClass( "stream_title" );
    });
</script>
@endsection


{{-- 設定視窗的標題 --}}
@section('title', '用戶等級權限表')

{{-- 設定內容的主標題區 --}}
@section('pageTitle', '用戶等級權限表')

{{-- 設定內容的主標題區 --}}
@section('breadcrumbArea')
    <ul id="breadcrumbs">
        <li><a href="/Default">Home</a></li>
        <li><span>用戶等級權限表</span></li>
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
        <div class="uk-grid uk-margin-medium-bottom" >
            <div class="uk-width-medium-5-5 uk-row-first">
                <div class="md-card">
                    <div class="md-card-content">
                        <div data-dynamic-fields="field_template_a" dynamic-fields-counter="0">
                            <div class="uk-grid uk-grid-medium form_section" data-uk-grid-match="">
                                <div class="uk-width-10-10">
                                    <!-- uk-grid start -->
                                    <div class="uk-grid">
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['SelectKeyword']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['UserType']['completeField'] !!}
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
                <table id="grid-basic" class="uk-table uk-table-nowrap table_check">
                    <thead>
                        <tr>
                            <th class="uk-width-1-10 uk-text-small">No.</th>
                            <th class="uk-width-1-10 uk-text-small">用戶所屬型別</th>
                            <th class="uk-width-1-10 uk-text-small">型別/層級代碼</th>
                            <th class="uk-width-2-10 uk-text-small">用戶所屬層級</th>
                            <th class="uk-width-1-10 uk-text-small">啟用/停用</th>
                            <th class="uk-width-1-10 uk-text-center uk-text-small">瀏覽</th>
                        </tr>
                    </thead>
                    @if($data->count() > 0)
                        @foreach ($data as $object)
                            <tr>
                                <td class="uk-text-small">{{ $i = $i + 1 }}</td>
                                <td class="uk-text-small">{{ $object->userType()->UserTypeName}}</td>
                                <td class="uk-text-small">{{ $object->UserType }}/{{ $object->DegreeId }}</td>
                                <td class="uk-text-small">{{ $object->DegreeName }}</td>
                                <td class="uk-text-small">
                                    @if($object->IfValid == 0) 
                                        <span class="uk-badge uk-badge-danger">停用</span>
                                    @else
                                        <span class="uk-badge uk-badge-primary">啟用</span>
                                    @endif
                                </td>
                                <td class="uk-text-center uk-text-small">
                                    <a href="javascript: getPermissionList('{{ $object->UTID }}');">
                                        <i class="md-icon material-icons">&#xE8F4;</i>
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
            
    <!-- table start -->
    <div id="PermissionList" style="display: none">
        <input type="hidden" id="userType" />
        <input type="hidden" id="userDegreeId" />
        <h4 class="heading_a uk-margin-bottom" id="ListHead">權限列表</h4>
        <div id="test"></div>
        <div class="md-card uk-margin-medium-bottom">
            <div class="md-card-content">
                <div class="uk-overflow-container">
                    <table id="PermissionTable" class="uk-table uk-table-nowrap table_check">
                        <thead>
                            <tr>
                                <th class="uk-width-1-10 uk-text-small">上層選單</th>
                                <th class="uk-width-1-10 uk-text-small">子選單</th>
                                <th class="uk-width-1-10 uk-text-small">備註</th>
                                <th class="uk-width-1-10 uk-text-small">排列順序</th>
                                <th class="uk-width-1-10 uk-text-small">開啟中/已關閉</th>
                            </tr>
                        </thead>
                        <tr>
                            <td colspan="4" style="text-align: center;">查無資料</td>
                        </tr>
                    </table>
                </div>
            </div>
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

        function getPermissionList(thisId){
            // console.log(thisId);
            $.ajax({
                url: '/api/v1/System/UserSetting/Permission/List',
                type: 'POST',
                async: false,
                headers: {
                    'Authorization': Cookies.get('authToken')
                },
                data : { 'id': thisId},
                success: function(response) {
                    if(response.status == 0){
                        var content = showList(response.data, thisId);
                        $('#userType').val(response.UserTypeId);
                        $('#userDegreeId').val(response.DegreeId);
                        $('#PermissionTable').html(content);
                        $('#PermissionList').show();
                        // $('#ListHead').attr("tabindex",-1).css('outline', 'none !important').focus();
                        $('html, body').animate({ scrollTop: $('#PermissionList').offset().top }, 'slow');
                    }else{
                        $('userType').val('');
                        $('userDegreeId').val('');
                        $('#PermissionList').hide();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log('error');
                },
                cache: false
            });
        }

        function showList(data, classId){
            var content = '<thead><tr><th class="uk-width-1-10 uk-text-small">上層選單</th><th class="uk-width-1-10 uk-text-small">子選單</th><th class="uk-width-1-10 uk-text-small">備註</th><th class="uk-width-1-10 uk-text-small">排列順序</th><th class="uk-width-1-10 uk-text-small">開啟中/已關閉</th></tr></thead>';
            var FunctionDesc = '';
            if(data.length > 0){
                $.each( data, function( k, v ) {
                    // console.log('k:'+k+':'+v.FunctionName);
                    if(v.FunctionDesc == null){
                        FunctionDesc = '-';
                    }else{
                        FunctionDesc = v.FunctionDesc;
                    }
                    content +=  '<tr>';
                    content +=  '<td class="uk-text-small">'+v.ParentName+'</td>';
                    content +=  '<td class="uk-text-small">'+v.FunctionName+'</td>';
                    content +=  '<td class="uk-text-small">'+FunctionDesc+'</td>';
                    content +=  '<td class="uk-text-small">'+v.MenuOrder+'</td>';
                    content +=  '<td class="uk-text-small">';
                    // if(v.IfAccess == 1) content +=  '<span class="uk-badge uk-badge-primary">開啟中</span>';
                    // else content +=  '<span class="uk-badge uk-badge-danger">已關閉</span>';
                    if(v.IfAccess == 1) content +=  '<div class="md-btn md-btn-primary" style="float:none;" onClick=turnOff("'+v.PermissionId+'","'+classId+'");>開啟中</div>';
                    else content +=  '<div class="md-btn md-btn-warning" style="float:none;" onClick=turnOn("'+v.PermissionId+'","'+v.FunctionId+'","'+classId+'");>已關閉</div>';
                    content +=  '</td>';
                    content +=  '</tr>';
                });
            }else{
                content +=  '<tr>';
                content +=  '<td colspan="4" style="text-align: center;">查無資料</td>';
                content +=  '</tr>';
            }

            content += '</table></div></div></div></div>';
            return content;
        }

        function turnOn(permissionId, functionId, thisId) {
          // console.log('permissionId:'+permissionId+',functionId:'+functionId+',thisId:'+thisId);
          
          // if (confirm("確定要開啟功能？")) {
            $.ajax({
                url: "/api/v1/System/UserSetting/Permission/TurnOn",
                type: 'POST',
                dataType: "json",
                data: {
                  "UserTypeId": $('#userType').val(),
                  "UserDegreeId": $('#userDegreeId').val(),
                  "FunctionId": functionId,
                  "PermissionId": permissionId
                },
                beforeSend: function(request) {
                  request.setRequestHeader("Authorization", Cookies.get('authToken'));
                },
                success: function(response) {
                    //getPermissionList(thisId);
                    if(response.status == 1){
                        //fail
                        alert('無法開啟該項功能');
                    }else{
                        getPermissionList(thisId);
                        // console.log('UserTypeId:'+response.UserTypeId);
                        // console.log('UserDegreeId:'+response.UserDegreeId);
                        // console.log('FunctionId:'+response.FunctionId);
                        // console.log('PermissionId:'+response.PermissionId);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert('登入權限已失效, 即將返回登入頁面');
                    // console.log('xhr.status:'+xhr.status);
                    // console.log('ajaxOptions:'+ajaxOptions);
                    // console.log('thrownError:'+thrownError);

                    Cookies.remove('authToken');
                    window.location.replace("/Admin/Login");
                }
            });         
          // }        
        }

    function turnOff(permissionId, thisId) {
        // console.log('permissionId:'+permissionId+',thisId:'+thisId);
        // if (confirm("確定要關閉功能？")) {
            $.ajax({
              url: "/api/v1/System/UserSetting/Permission/TurnOff",
              type: 'POST',
              dataType: "json",
              data: { "PermissionId": permissionId },
              beforeSend: function(request) {
                request.setRequestHeader("Authorization", Cookies.get('authToken'));
              },
              success: function(response) {
                if(response.status == 1){
                    alert('無法關閉該項功能');
                }else getPermissionList(thisId);
              },
              error: function(xhr, ajaxOptions, thrownError) {

                alert('登入權限已失效, 即將返回登入頁面');
                // console.log('xhr.status:'+xhr.status);
                // console.log('ajaxOptions:'+ajaxOptions);
                // console.log('thrownError:'+thrownError);

                Cookies.remove('authToken');
                window.location.replace("/Admin/Login");
              }
            });
        // }      
    }
    </script>    
@endsection

