
{{-- 載入主要的版型 --}}
@extends('layouts.master')

{{-- 額外增加所需要的css檔案 --}}
@section('extraCssArea')
    
@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')

@endsection


{{-- 設定視窗的標題 --}}
@section('title', $functionname)

{{-- 設定內容的主標題區 --}}
@section('pageTitle', $functionname)

{{-- 設定內容的主標題區 --}}
@section('breadcrumbArea')
    <ul id="breadcrumbs">
        <li><a href="/Default">Home</a></li>
        <li><span>{!! $functionname !!}</span></li>
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
                                    </div>
                                    <!-- uk-grid end -->
                                </div>
                                <div class="uk-width-1-1" style="margin-top:20px;" >
                                    <div class="uk-width-1-1">
                                        <!--<button type="submit" href="#" class="md-btn md-btn-primary">查詢</button>-->
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
                            <th class="uk-width-1-10 uk-text-small">順序</th>
                            <th class="uk-width-1-10 uk-text-small">功能代碼</th>
                            <th class="uk-width-2-10 uk-text-small">名稱</th>
                            <th class="uk-width-2-10 uk-text-small">功能連結</th>
                            <th class="uk-width-1-10 uk-text-small">啟用/停用</th>
                            <th class="uk-width-1-10 uk-text-center uk-text-small">功能</th>
                        </tr>
                    </thead>
                    @if($data->count() > 0)
                        @foreach ($data as $object)
                            <tr>
                                <td class="uk-text-small">{{ $i = $i + 1 }}</td>
                                <td class="uk-text-small">{{ $object->MenuOrder}}</td>
                                <td class="uk-text-small">
                                    <i class="material-icons">{{ $object->FunctionCode }}</i>
                                </td>
                                <td class="uk-text-small">{{ $object->FunctionName }}</td>
                                <td class="uk-text-small">{{ $object->FunctionURL }}</td>
                                <td class="uk-text-small">
                                    @if($object->IfValid == 0) 
                                        <span class="uk-badge uk-badge-danger">停用</span>
                                    @else
                                        <span class="uk-badge uk-badge-primary">啟用</span>
                                    @endif
                                </td>
                                <td class="uk-text-center uk-text-small">
                                    <a href="{{ route($routePath.'.edit',$object->toArray()[$primaryKey]) }}"><i class="md-icon material-icons">&#xE254;</i></a>
                                     {!! Form::open(['id' => 'formDeleteAction'.$i , 'method' => 'DELETE','route' => [ $routePath.'.destroy', $object->toArray()[$primaryKey]],'style'=>'display:inline']  ) !!}
                                    <a href="javascript:if(confirm('確定要刪除這筆資料嗎？'))$('{{ "#formDeleteAction".$i }}').submit();"><i class="md-icon material-icons">&#xE872;</i></a>
                                    {!! Form::close() !!}
                                    {{--<a href="#"><i class="md-icon material-icons">&#xE88F;</i></a>--}}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <td colspan="500" style="text-align: center;">No data found</td>
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
    </script>    
@endsection