{{-- 載入主要的版型 --}}
@extends('layouts.master')

{{-- 額外增加所需要的css檔案 --}}
@section('extraCssArea')
    <style>
        .userMOUSE{ cursor: pointer; }
    </style>
@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')
    <!-- common functions -->
    <script src="/assets/js/common.min.js"></script>
    <!-- uikit functions -->
    <script src="/assets/js/uikit_custom.min.js"></script>
    <script src="/bower_components/tinymce/tinymce.min.js"></script>
    <!-- ionrangeslider -->
    <script src="/bower_components/ion.rangeslider/js/ion.rangeSlider.min.js"></script>
    <!-- htmleditor (codeMirror) -->
    <script src="assets/js/uikit_htmleditor_custom.min.js"></script>

    <style type="text/css">
        .uk-modal #hotspotOwner .selectize-dropdown {
            top: 85px !important;
        }
    </style>
    <script>
        tinymce.init({
            selector: 'textarea.tinymce',
            skin_url: '/assets/skins/tinymce/material_design',
            height: 300,
            menubar: '',
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table contextmenu paste code textcolor'
            ],
            toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fontsizeselect | code | forecolor backcolor',
            color_map: [
                '#BFEDD2', 'Light Green',
                '#FBEEB8', 'Light Yellow',
                '#F8CAC6', 'Light Red',
                '#ECCAFA', 'Light Purple',
                '#C2E0F4', 'Light Blue',

                '#2DC26B', 'Green',
                '#F1C40F', 'Yellow',
                '#E03E2D', 'Red',
                '#B96AD9', 'Purple',
                '#3598DB', 'Blue',

                '#169179', 'Dark Turquoise',
                '#E67E23', 'Orange',
                '#BA372A', 'Dark Red',
                '#843FA1', 'Dark Purple',
                '#236FA1', 'Dark Blue',

                '#ECF0F1', 'Light Gray',
                '#CED4D9', 'Medium Gray',
                '#95A5A6', 'Gray',
                '#7E8C8D', 'Dark Gray',
                '#34495E', 'Navy Blue',

                '#000000', 'Black',
                '#ffffff', 'White'
            ],
            file_picker_types: 'image media',
            paste_data_images: true,
            forced_root_block:"",//防止自動加上<p></p>
            fontsize_formats: '11px 12px 14px 16px 18px 24px 36px 48px',
            images_upload_handler: function (blobInfo, success, failure) {
                var xhr, formData;

                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', '/api/v1/UploadImage');

                xhr.onload = function() {
                    var json;

                    if (xhr.status != 200) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }

                    json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location != 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }

                    success('{{ env("IMGPath") }}'+json.location);
                  };

                  formData = new FormData();
                  formData.append('file', blobInfo.blob(), blobInfo.filename());
                  formData.append('path', 'files/upload/');

                  xhr.send(formData);
                },
              // importcss_file_filter: "style-rabbit.css",
              // content_css: '/css/style-rabbit.css',
              importcss_append: true,
              relative_urls: false, // 關閉會將圖片路徑顯示完整URL
              remove_script_host: false
        });
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
        <li><span>{!! $TOPname !!}</span></li>
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
        {{ Form::hidden('excel', '', array('id' => 'excel', 'value' => 0)) }}
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
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['DeviceSN']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['MacAddress']['completeField'] !!}
                                        </div>
                                    </div>
                                    <div class="uk-grid">
                                        <div class="uk-width-1-1">
                                            {!! $searchFields['LogType']['completeField'] !!}
                                        </div>
                                    </div>
                                    <div class="uk-grid">
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['DateFrom']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['DateTo']['completeField'] !!}
                                        </div>
                                    </div>
                                    <!-- uk-grid end -->
                                </div>
                                <div class="uk-width-10-10" style="margin-top:20px;" >
                                    <div class="uk-width-1-1">
                                        <div onclick="search();" class="md-btn md-btn-primary">Search</div>
                                        @if($IfSearch == '1') 
                                            <div onclick="resetForm();" class="md-btn md-btn-warning">Clear</div>
                                        @endif
                                    </div>
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
    {!! Form::close() !!}
    <!-- search area end-->

    <!-- table start -->
    <h4 class="heading_a uk-margin-bottom">List</h4>
    <div class="md-card uk-margin-medium-bottom">
        <div class="md-card-content">
            <div class="uk-overflow-container">
            </div>
            <div class="uk-overflow-container" style="overflow:visible;">
                <table id="grid-basic" class="uk-table uk-table-nowrap table_check">
                    <thead>
                        <tr>
                            {!! generateHTML('DeviceSN','s/n',$isAsc, $orderBy) !!}
                            {!! generateHTML('MacAddress','lan mac',$isAsc, $orderBy) !!}
                            {!! generateHTML('LogType','type',$isAsc, $orderBy) !!}
                            {!! generateHTML('LogDescription','description',$isAsc, $orderBy) !!}
                            {!! generateHTML('LogDate','date',$isAsc, $orderBy) !!}
                            {!! generateHTML('IsCompleted','completed',$isAsc, $orderBy) !!}
                        </tr>
                    </thead>
                    @if($data->count() > 0)
                        @foreach ($data as $object)
                            <tr>
                                <td class="uk-text-small">{{ $object->DeviceSN }}</td>
                                <td class="uk-text-small">{{ $object->MacAddress }}</td>
                                <td class="uk-text-small">
                                    <?php
                                        switch($object->LogType){
                                            case null:
                                                echo "";
                                                break;
                                            case '':
                                                echo "";
                                                break;
                                            case 0:
                                                echo "H/W issue.";
                                                break;
                                            case 1:
                                                echo "Helium related.";
                                                break;
                                            case 2:
                                                echo "Setting error.";
                                                break;
                                            case 99:
                                                echo "others.";
                                                break;
                                            default:
                                                echo $object->LogType;
                                        }
                                    ?>
                                </td>
                                <td class="uk-text-small">{{ $object->LogDescription }}</td>
                                <td class="uk-text-small">{{Carbon\Carbon::parse($object->LogDate)->format('Y-m-d H:i:s')}}</td>
                                <td class="uk-text-small">
                                    @if($object->IsCompleted == 1)
                                        Y
                                        @if($object->IsCompletedDate)
                                            <br>
                                            {{Carbon\Carbon::parse($object->IsCompletedDate)->format('Y-m-d H:i:s')}}
                                            <br>
                                            @if(isset($object->CompletedBy))
                                                {{ $object->CompletedBy->RealName }}
                                            @endif
                                            <br>
                                            {{ $object->CompletedReport }}
                                        @endif
                                    @else
                                        N
                                        <a onclick="showIssue('{{ $object->AnimalName }}','{{ $object->DeviceSN }}','{{ $object->MacAddress }}','{{ $object->LogId }}')">
                                            <span class="material-icons" style="font-size:14px;" > build </span>
                                        </a>
                                    @endif
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

    {{-- 處理 --}}
    <div class="uk-modal" id="issue">
        <div class="uk-modal-dialog">
            <div class="uk-modal-header" style="background:#45B7C4;margin-top:-25px;height:50px;display:flex;align-items:center;">
                <h3 align="center" valign="center" style="color:#E8F6F8">Report Issue</h3>
            </div>
            <div>
                <p><input type="hidden" id="LogId"></p>
                <div class="large-padding">
                    <h3 class="heading_a" style="padding-left: 0px;">Device Information</h3>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-2">
                            {!! $formFields['DeviceSN']['completeField']  !!}
                        </div>
                        <div class="uk-width-medium-1-2">
                            {!! $formFields['MacAddress']['completeField']  !!}
                        </div>
                    </div>
                    <h3 class="heading_a" style="padding-left: 0px;">Issue</h3>
                   <!--  <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-1">
                            {!! $formFields['LogType']['completeField']  !!}
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-1">
                            {!! $formFields['Subject']['completeField']  !!}
                        </div>
                    </div> -->
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-1" style="width: 100%">
                            {!! $formFields['CompletedReport']['completeField']  !!}
                        </div>
                    </div>
                    <div>
                        <div class="uk-width-1-1 uk-modal-footer">
                            <button type="button" class="md-btn md-btn-primary" onclick="updateIssue()">Submit</button>
                            <button type="button" class="md-btn md-btn-warning uk-modal-close" >BACK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- 處理 --}}

    <script>
        function search() {
            $('#excel').val(0);
            $('#IfSearch').val('1');
            $('#IfNewSearch').val('1');
            $('#Page').val('1');
            $('#searchForm').submit();
        }

        function resetForm() {
            $('#excel').val(0);
            $('#searchForm input').val('');
            $('#searchForm select').val('');
            $('#IfSearch').val('0');
            $('#IfNewSearch').val('0');
            $('#Page').val('1');
            $('#searchForm').submit();
        }

        function exportExcel() {
            $('#excel').val(1);
            $('#IfSearch').val('1');
            $('#IfNewSearch').val('1');
            $('#Page').val('1');
            $('#searchForm').submit();
        }

        function gotoPage(pageNo) {
            $('#Page').val(pageNo);
            $('#IfNewSearch').val('0');
            $('#searchForm').submit();
        }

        function orderBy(type){
            $('#Page').val('1');
            $('#excel').val(0);
            $('#orderBy').val(type);
            var a = $('#'+type);
            if(a.length > 0){
                // a[0].textContent == "keyboard_arrow_up"
                if($('#isAsc').val() == '1'){
                    $('#isAsc').val('0');
                }else if($('#isAsc').val() == '0'){
                    $('#isAsc').val('1');
                }
            }else{
                $('#isAsc').val('1');
            }
              
            $('#searchForm').submit();
        }

        // 顯示回報問題
        function showIssue(name,sn,mac,lid) {
            $('#issue #AnimalName').val(name);
            $('#issue #DeviceSN').val(sn);
            $('#issue #MacAddress').val(mac);
            $('#issue #LogId').val(lid);
            UIkit.modal("#issue").show();
        }

        // 回報問題
        function updateIssue() {
            var LogId = $('#issue #LogId').val();
            var CompletedReport = tinyMCE.get('CompletedReport').getContent();

            $.ajax({
                url: '/api/v1/SolvingIssue',
                type: 'POST',
                async: false,
                headers: {
                    'Authorization': Cookies.get('authToken')
                },
                data : { 
                    'LogId' : LogId,
                    'CompletedReport' : CompletedReport
                },
                success: function(response) {
                    if(response.status == 0){
                        // hideen the button
                        UIkit.modal.alert('success!');
                        window.location.reload();
                    }else{
                        UIkit.modal.alert(response.message)
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log('error');
                    UIkit.modal.alert('更新失敗！(error)').on('hide.uk.modal', function() {
                        // custome js code
                        console.log('close');
                    });
                },
                complete: function () {
                    UIkit.modal("#black").hide();
                },
                cache: false
            });
        }
    </script>

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
@endsection