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
    <h4 class="heading_a uk-margin-bottom">Search</h4>
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
                                    </div>
                                    <!-- uk-grid end -->
                                </div>
                                <div class="uk-width-1-1" style="margin-top:20px;" >
                                    <div class="uk-width-1-1">
                                        <div onclick="search();" class="md-btn md-btn-primary">Inquire</div>
                                        @if($IfSearch == '1') 
                                            <div onclick="resetForm();" class="md-btn md-btn-warning">Clear inquiry</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script id="field_template_a" type="text/x-handlebars-template"></script>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
    <!-- search area end-->
    
    <!-- table start -->
    <h4 class="heading_a uk-margin-bottom">List</h4>
    <div class="md-card uk-margin-medium-bottom">
        <div class="md-card-content">
            <div class="uk-overflow-container">
                <div class="uk-width-1-10" style="float:right">
                    <button type="submit" onclick="window.location.href='{{ route( $routePath.'.create') }}';" class="md-btn md-btn-primary">Add</button>
                </div>
            </div>
            
            <div class="uk-overflow-container">
                <table id="grid-basic" class="uk-table uk-table-nowrap table_check">
                    <thead>
                        <tr>
                            <th class="uk-width-1-10 uk-text-small">No.</th>
                            {!! generateHTML('MemberNo','Login Name',$isAsc, $orderBy) !!}
                            {!! generateHTML('RealName','Name',$isAsc, $orderBy) !!}
                            {!! generateHTML('UserEmail','Email',$isAsc, $orderBy) !!}
                            {!! generateHTML('ContactPhone','Phone',$isAsc, $orderBy) !!}

                            {!! generateHTML('ContactAddress','Address',$isAsc, $orderBy) !!}
                            {!! generateHTML('CompanyName','Contact',$isAsc, $orderBy) !!}
                            {!! generateHTML('CompanyPhone','Contact Phone',$isAsc, $orderBy) !!}
                            {!! generateHTML('CompanyEmail','Contact Email',$isAsc, $orderBy) !!}
                            {!! generateHTML('IfValid','Active/ Inactive',$isAsc, $orderBy) !!}
                            <th class="uk-width-1-10 uk-text-center uk-text-small">Function</th>
                        </tr>
                    </thead>
                    @if($data->count() > 0)
                        @foreach ($data as $object)
                            <tr id="{{$object->Id}}">
                                <td class="uk-text-small">{{ $i = $i + 1 }}</td>
                                <td class="uk-text-small">{{ $object->MemberNo }}</i></td>
                                <td class="uk-text-small">{{ isset($object->RealName) ? $object->RealName : '' }}</td>
                                <td class="uk-text-small">{{ isset($object->UserEmail) ? $object->UserEmail : '' }}</td>
                                <td class="uk-text-small">{{ isset($object->ContactPhone) ? $object->ContactPhone : '' }}</td>

                                <td class="uk-text-small">{{ isset($object->ContactAddress) ? $object->ContactAddress : '' }}</td>
                                <td class="uk-text-small">{{ isset($object->CompanyName) ? $object->CompanyName : '' }}</td>
                                <td class="uk-text-small">{{ isset($object->CompanyPhone) ? $object->CompanyPhone : '' }}</td>
                                <td class="uk-text-small">{{ isset($object->CompanyEmail) ? $object->CompanyEmail : '' }}</td>
                                <td class="uk-text-small Status">
                                    @if($object->IfValid == 0) 
                                        <span class="uk-badge uk-badge-danger">Inactive</span>
                                    @elseif($object->LoginFailTimes > 2)
                                        <span class="uk-badge uk-badge-danger" onclick="unlock('{{$object->Id}}')">Locked</span>
                                    @else
                                        <span class="uk-badge uk-badge-primary">Active</span>
                                    @endif
                                </td>
                                <td class="uk-text-center uk-text-small">
                                    <a href="{{ route($routePath.'.edit',$object->$primaryKey) }}">
                                        <i class="md-icon material-icons">&#xE254;</i>
                                    </a>
                                    {!! Form::open(['id' => 'formDeleteAction'.$i , 'method' => 'DELETE','route' => [ $routePath.'.destroy', $object->$primaryKey],'style'=>'display:inline']  ) !!}
                                        <a href="javascript:if(confirm('Are you sure to delete this datum?'))$('{{ '#formDeleteAction'.$i }}').submit();"><i class="md-icon material-icons">&#xE872;</i></a>
                                    {!! Form::close() !!}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <td colspan="500" style="text-align: center;">No data matched</td>
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