
{{-- 載入主要的版型 --}}
@extends('layouts.master')

{{-- 額外增加所需要的css檔案 --}}
@section('extraCssArea')

@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')

@endsection


{{-- 設定視窗的標題 --}}
@section('title', '修改密碼')

{{-- 設定內容的主標題區 --}}
@section('pageTitle', '修改密碼')

{{-- 設定內容的主標題區 --}}
@section('breadcrumbArea')
    <ul id="breadcrumbs">
        <li><a href="/Default">Home</a></li>
        <li><span>修改密碼</span></li>
    </ul>
@endsection

{{-- 設定內容 --}}
@section('content')
    <!-- search area start-->
    {!! Form::open(array('route' => $routePath.'.index','method'=>'GET', 'id'=> 'searchForm')) !!}
    {{ Form::hidden('IfSearch', '', array('id' => 'IfSearch','value' => '1')) }}
    <div class="uk-grid uk-margin-medium-bottom" >
        <div class="uk-width-medium-5-5 uk-row-first">
            <div class="md-card">
                <div class="md-card-content">
                    <div data-dynamic-fields="field_template_a" dynamic-fields-counter="0">
                        <div class="uk-grid uk-grid-medium form_section" data-uk-grid-match="">
                            <div class="uk-width-10-10">
                                <!-- uk-grid start -->
                                <div class="uk-grid">
                                    <div class="uk-width-1-2">
                                        {!! $searchFields['OldPassword']['completeField'] !!}
                                    </div>
                                </div>
                                <!-- uk-grid end -->
                                <!-- uk-grid start -->
                                <div class="uk-grid">
                                    <div class="uk-width-1-2">
                                        {!! $searchFields['NewPassowrd']['completeField'] !!}
                                    </div>
                                </div>
                                <!-- uk-grid end -->
                                <!-- uk-grid start -->
                                <div class="uk-grid">
                                    <div class="uk-width-1-2">
                                        {!! $searchFields['NewPassowrdCheck']['completeField'] !!}
                                    </div>
                                </div>
                                <!-- uk-grid end -->
                            </div>
                            <div class="uk-width-5-10" style="margin-top:20px;" >
                                <div class="uk-width-1-1">
                                    <!--<button type="submit" href="#" class="md-btn md-btn-primary">查詢</button>-->
                                    <div onclick="search();" class="md-btn md-btn-primary">送出修改</div>
                                    @if($IfSearch == '1')
                                        <div onclick="resetForm();" class="md-btn md-btn-warning">重新輸入</div>
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

    <script>
        function search() {
            $('#IfSearch').val('1');
            $('#searchForm').submit();
        }

        function resetForm() {
            $('#searchForm input').val('')
        }
    </script>
@endsection
