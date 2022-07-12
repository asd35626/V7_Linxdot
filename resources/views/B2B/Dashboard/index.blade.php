{{-- 載入主要的版型 --}}
@extends('layouts.master')

{{-- 額外增加所需要的css檔案 --}}
@section('extraCssArea')
<style type="text/css">
    .userMOUSE{ cursor: pointer; }
    .container-1{
        /*width: 300px;*/
        vertical-align: middle;
        white-space: nowrap;
        position: relative;
    }
    .container-1 input#search{
        width: 90%;
        height: 30px;
        border: none;
        font-size: 10pt;
        float: right;
        color: #63717f;
        padding-left: 35px;
        border-radius: 15px;
    }
    .container-1 .icon{
        position: absolute;
        z-index: 1;
        color: #4f5b66;
        position: absolute;
        margin-left: 45px;
        margin-top: 7px;
    }
    .container-1 input#search::-webkit-input-placeholder {
       color: #65737e;
    }
     
    .container-1 input#search:-moz-placeholder { /* Firefox 18- */
       color: #65737e;  
    }
     
    .container-1 input#search::-moz-placeholder {  /* Firefox 19+ */
       color: #65737e;  
    }
     
    .container-1 input#search:-ms-input-placeholder {  
       color: #65737e;  
    }

    .material-icons {font-size: 20px;}

    .bd-example-modal-lg .modal-dialog{
        display: table;
        position: relative;
        margin: 0 auto;
        top: calc(50% - 24px);
      }
      
      .bd-example-modal-lg .modal-dialog .modal-content{
        background-color: transparent;
        border: none;
      }

    /*map標記設定*/
    .mapboxgl-ctrl{
        display: none;
    }

    /*mapicon設定*/
    .marker {
        background-image: url('/favicon.ico');
        background-size: cover;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
    }

    /*map設定*/
    #map {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 100%;
    }

</style>
@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')
    <script>
        $(document).ready(function() {
            $('#search').keypress(function(event) {
                if (event.which == 13) {
                    $('#excel').val(0);
                    $('#IfSearch').val('1');
                    $('#IfNewSearch').val('1');
                    $('#Page').val('1');
                    $('#searchForm').submit();
                }
            });
            $('#search').change(function(event) {
                $('#excel').val(0);
                $('#IfSearch').val('1');
                $('#IfNewSearch').val('1');
                $('#Page').val('1');
                $('#searchForm').submit();
            });
        })

        function rebootHotspot(MAC){
            var modal =  UIkit.modal.blockUI('<div class=\'uk-text-center\'>Loading...<br/><img class=\'uk-margin-top\' src=\'/assets/img/spinners/spinner.gif\' alt=\'\'>');
            $.ajax({
                type: "POST",
                url:"https://linxdotapi.v7idea.com/rebootHotspot",
                data:{
                    mac: MAC 
                },
                success: function(response){
                    modal.hide();
                    // alert(response);
                    if(response.status == 0){
                        alert('Reboot Successfully');
                    }else{
                        alert(response.errorMessage);
                    }
                },
                error : function(xhr, ajaxOptions, thrownError){
                    modal.hide();
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
        function Upgradefirmware(MAC){
            var modal =  UIkit.modal.blockUI('<div class=\'uk-text-center\'>Loading...<br/><img class=\'uk-margin-top\' src=\'/assets/img/spinners/spinner.gif\' alt=\'\'>');
            $.ajax({
                type: "POST",
                url:"https://linxdotapi.v7idea.com/ota",
                data:{
                    mac: MAC 
                },
                timeout: 0,
                success: function(response){
                    modal.hide();
                    // alert(response);
                    if(response.status == 0){
                        alert('Upgrade Firmware Successfully');
                    }else{
                        alert(response.errorMessage);
                    }
                },
                error : function(xhr, ajaxOptions, thrownError){
                    modal.hide();
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
        function ReverseSSH(MAC){
            var modal =  UIkit.modal.blockUI('<div class=\'uk-text-center\'>Loading...<br/><img class=\'uk-margin-top\' src=\'/assets/img/spinners/spinner.gif\' alt=\'\'>');
            $.ajax({
                type: "POST",
                url:"https://linxdotapi.v7idea.com/reverseSSH",
                data:{
                    mac: MAC 
                },
                success: function(response){
                    modal.hide();
                    // alert(response.status);
                    if(response.status == 0){
                        // var modal2 =  UIkit.modal.blockUI('<div class=\'uk-text-center\'>ip:'+response.data.ip+'<br>port:'+response.data.port+'<br>command:'+response.data.command+'<br/>');
                        let datahtml = '';

                        datahtml += `ip:`+response.data.ip;
                        datahtml += `<br>port:`+response.data.port;
                        datahtml += `<br>command:`+response.data.command;
                        $('#ReverseSSH').html(datahtml);
                        // alert('Reboot Successfully');
                    }else{
                        alert(response.errorMessage);
                    }
                },
                error : function(xhr, ajaxOptions, thrownError){
                    modal.hide();
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
                },
                complete: function () {
                    UIkit.modal("#reverseSSH").show();
                },
                cache: false
            });
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
        <li><span>Dashboard</span></li>
    </ul>
@endsection

{{-- 設定內容 --}}
@section('content')
    <!-- search area start-->
    {!! Form::open(array('route' => $routePath.'.index','method'=>'GET', 'id'=> 'searchForm')) !!}
        {{ Form::hidden('IfNewSearch', '', array('id' => 'IfNewSearch')) }}
        {{ Form::hidden('IfSearch', '', array('id' => 'IfSearch')) }}
        {{ Form::hidden('Page', '', array('id' => 'Page')) }}
        {{ Form::hidden('orderBy', '', array('id' => 'orderBy')) }}
        {{ Form::hidden('isAsc', '', array('id' => 'isAsc')) }}
        <div class="uk-grid uk-margin-medium-bottom"  style="display:none">
            <div class="uk-width-medium-1-5 uk-row-first">
                <div class="md-card">
                    <div class="md-card-right">
                        <script id="field_template_a" type="text/x-handlebars-template">
                            {{--<hr class="form_hr">--}}
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="uk-grid  uk-margin-medium-bottom">
            <div class="uk-width-7-10"></div>
            <div class="container-1 uk-width-3-10">
                <span class="material-icons icon"> search </span>
                <input type="search" id="search" name="search" placeholder="Search..." value="{!! $search !!}">
            </div>
        </div>
    {!! Form::close() !!}
    <!-- search area end-->
    <!-- table start -->
    <table id="grid-basic" class="uk-table uk-table-nowrap table_check">
        <thead>
            <tr>
                {!! generateHTML('DeviceSN','S/N',$isAsc, $orderBy) !!}
                {!! generateHTML('MacAddress','MAC Address',$isAsc, $orderBy) !!}
                {!! generateHTML('AnimalName','Animal name',$isAsc, $orderBy) !!}
                {!! generateHTML('LastUpdateOnLineTime','Status',$isAsc, $orderBy) !!}
                {!! generateHTML('BlockHeight','Miner height',$isAsc, $orderBy) !!}
                {!! generateHTML('LastUpdateOnLineTime','Latest online time',$isAsc, $orderBy) !!}
                {!! generateHTML('Firmware','ROM version',$isAsc, $orderBy) !!}
                {!! generateHTML('MinerVersion','Miner version',$isAsc, $orderBy) !!}
                <th class="uk-width-1-10 uk-text-small" style="cursor: pointer;color:black;font-weight:bold;">More</th>
            </tr>
        </thead>
        @if($data->count() > 0)
            @foreach ($data as $object)
                <tr bgcolor="#C8E0E5" style="color:black;">
                    <td class="uk-text-small">{{ $object->DeviceSN }}</td>
                    <td class="uk-text-small">{{ $object->MacAddress }}</td>
                    <td class="uk-text-small">{{ $object->AnimalName }}</td>
                    <td class="uk-text-small">
                        @if($object->DewiStatus == 'Onboarded')
                            @if($object->LastUpdateOnLineTime)
                                <?php 
                                    $now = date_create( date('Y-m-d H:i:s',time() - (8 * 3600)));
                                    $LastUpdateOnLineTime = date_create( $object->LastUpdateOnLineTime);
                                    $time = date_diff($now, $LastUpdateOnLineTime);

                                    $minutes = $time->days * 24 * 60;
                                    $minutes += $time->h * 60;
                                    $minutes += $time->i;
                                    if($minutes <= 10 && $object->P2P_Connected == 1){
                                        print('<span class="material-icons" style="color:#59BBBC"> circle </span> online');
                                    }else{
                                        print('<span class="material-icons" style="color:#FF5959"> circle </span> offline');
                                    }
                                ?>
                            @else
                                <span class="material-icons" style="color:#FF5959"> circle </span> offline
                            @endif
                        @else
                            <span class="material-icons" style="color:#ABABAB"> circle </span>notonboarded
                        @endif
                    </td>
                    <td class="uk-text-small">{{ $object->BlockHeight }}</td>
                    <td class="uk-text-small">
                        @if($object->LastUpdateOnLineTime)
                            {{ Carbon\Carbon::parse($object->LastUpdateOnLineTime)->format('Y/m/d H:i:s') }}
                        @endif
                    </td>
                    <td class="uk-text-small">
                        @if(isset($object->Version->VersionNo))
                            {{ $object->Version->VersionNo }}
                        @else
                            {{ $object->Firmware }}
                        @endif
                    </td>
                    <td class="uk-text-small">{{ substr($object->MinerVersion, -15) }}</td>
                    <td class="uk-text-small">
                        <div class="md-card-list-wrapper">
                            <div class="md-card-list" style="margin-top:0px">
                                <div class="md-card-list-item-menu" data-uk-dropdown="{mode:'click',pos:'bottom-right'}">
                                    <a class="md-icon material-icons">&#xE5D4;</a>
                                    <div class="uk-dropdown" style="background:#C4C4C4">
                                        <ul style="text-align:left;list-style:none;display: block;
                                        margin-block-start:0px;margin-block-end:0px;margin-inline-start:0px;
                                        margin-inline-end:0px;padding-inline-start:0px;line-height: 25px;">
                                            {{--地圖--}}
                                            @if($object->map_lat != null || $object->map_lat != '' && $object->map_lng != null || $object->map_lng != '')
                                                <li><a data-uk-modal="{target:'#modal_full'}" onclick="map('{{ $object->map_lng }}','{{ $object->map_lat }}')">Show on map</a></li>
                                            @endif
                                            {{-- 重開機 --}}
                                            <li><a onclick="rebootHotspot('{{ $object->MacAddress }}')">Reboot</a></li>
                                            {{-- 更新分位 --}}
                                            <li><a onclick="Upgradefirmware('{{ $object->MacAddress }}')">Upgrade firmware</a></li>
                                            {{-- <li><a href="#">Restart miner</a></li>
                                            <li><a href="#">Trigger fast sync</a></li>
                                            回報問題 --}}
                                            {{-- <li><a data-uk-modal="{target:'#modal_header_footer'}">Report issue</a></li> --}}
                                            {{-- <li><a href="#">Device heartbeat</a></li> --}}
                                            {{-- Reverse SSH --}}
                                            <li><a onclick="ReverseSSH('{{ $object->MacAddress }}')">Reverse SSH</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <td colspan="500" style="text-align: center;">No data found</td>
        @endif
    </table>
    @include('Pagination')
    <!-- table end -->


    <div class="uk-width-medium-1-3">
        <div class="uk-modal" id="modal_header_footer">
            <div class="uk-modal-dialog">
                <div class="uk-modal-header" style="background:#45B7C4;margin-top:-25px;height:50px;display:flex;align-items:center;">
                    <h3 align="center" valign="center" style="color:#E8F6F8">Report issue</h3>
                </div>
                <table align="center">
                    <tr><td>Animal name：</td><td></td></tr>
                    <tr><td>Subject：</td><td><input type="" name=""></td></tr>
                    <tr><td>Description：</td><td><input type="" name=""></td></tr>
                </table>
                <div class="uk-modal-footer uk-text-center">
                    <button type="button" class="md-btn md-btn-flat md-btn-flat-primary" style="background:#45B7C4;color:#E8F6F8 ">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="uk-modal uk-modal-card-fullscreen" id="modal_full" aria-hidden="true" style="display: none; overflow-y: auto;">
        <div class="uk-modal-dialog uk-modal-dialog-blank">
            <div class="md-card uk-height-viewport">
                <div class="md-card-toolbar">
                    <div class="md-card-toolbar-actions">
                        <div class="md-card-dropdown" data-uk-dropdown="{pos:'bottom-right'}">
                            {{-- <i class="md-icon material-icons"></i>
                            <div class="uk-dropdown">
                                <ul class="uk-nav">
                                    <li><a href="#">Action 1</a></li>
                                    <li><a href="#">Action 2</a></li>
                                </ul>
                            </div> --}}
                        </div>
                    </div>
                     <span class="md-icon material-icons uk-modal-close"></span>
                    <h3 class="md-card-toolbar-heading-text">
                        Map
                    </h3>
                </div>
                <div class="md-card-content">
                    <div id='map' style='width: 95%; height: 95%;'></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ReverseSSH --}}
    <div class="uk-modal" id="reverseSSH" aria-hidden="true" style="display: none; overflow-y: auto;">
            <div class="uk-modal-dialog" style="top: 199px;">
                <p><input type="hidden" id="HID"></p>
                <div id="ReverseSSH"></div>
                <div class="uk-modal-footer uk-text-right">
                    <button type="button" class="md-btn md-btn-primary uk-modal-close">OK</button>
                </div>
            </div>
        </div>
    {{-- ReverseSSH --}}

    <script>
        function search() {
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
        function map(lng,lat){
            mapboxgl.accessToken = 'pk.eyJ1IjoiYXNkMzU2MjYiLCJhIjoiY2w0cDdlNDk2MDd2ZTNlbWpycnNrdW0wcCJ9._Q--d12cdqSM5jAdabU08w';
            const map = new mapboxgl.Map({
                container: 'map', // container ID
                style: 'mapbox://styles/mapbox/streets-v11', // style URL
                // center: [-74.5, 40], // starting position [lng, lat]
                center: [lng,lat],
                zoom: 15, // starting zoom
            });
            map.on('idle',function(){
                // alert(123);
                map.resize()
            });

            const geojson = {
                type: 'FeatureCollection',
                features: [
                    {
                        type: 'Feature',
                        geometry: {
                            type: 'Point',
                            coordinates: [lng,lat]
                        },
                        properties: {
                            title: 'Mapbox',
                            description: 'Washington, D.C.'
                        }
                    }
                ]
            };

            // add markers to map
            for (const feature of geojson.features) {
                // create a HTML element for each feature
                const el = document.createElement('div');
                el.className = 'marker';

                // make a marker for each feature and add to the map
                new mapboxgl.Marker(el).setLngLat(feature.geometry.coordinates).addTo(map);
            }
        }
            
    </script>

    <?php 
        //產生列表的 title(id, 名稱, 排列方式, 當前排序欄位)
        function generateHTML($rawId,$rawName,$isAsc,$orderBy){
            $html = '<th class="uk-width-1-10 uk-text-small">
                        <span onclick="orderBy(\''.$rawId.'\');" style="cursor: pointer;color:black;font-weight:bold;">';
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