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
    <style type="text/css">
        .uk-modal #hotspotOwner .selectize-dropdown {
            top: 85px !important;
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
    <script>
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
                        UIkit.modal("#reverseSSH").show();
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
                }
            });
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
                                            {!! $searchFields['S/N']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['Mac']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['AnimalName']['completeField'] !!}
                                        </div>
                                    </div>
                                    <div class="uk-grid">
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['IsVerify']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['IfRegister']['completeField'] !!}
                                        </div>
                                    </div>
                                    <div class="uk-grid">
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['IssueDateFrom']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['IssueDateTo']['completeField'] !!}
                                        </div>
                                    </div>
                                    <div class="uk-grid">
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['VerifyDateFrom']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['VerifyDateTo']['completeField'] !!}
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
    <h4 class="heading_a uk-margin-bottom">List</h4>
    <div class="md-card uk-margin-medium-bottom">
        <div class="md-card-content">
            <div class="uk-overflow-container">
                <div class="uk-width-1-10" style="float:right">
                    <button type="submit" onclick="exportExcel()" class="md-btn md-btn-primary">Export</button>
                </div>
                <div class="uk-width-1-10" style="float:right">
                    <button type="submit" onclick="window.location.href='{{ route('Excel.create') }}';" class="md-btn md-btn-primary">Import</button>
                </div>
                <div class="uk-width-1-10" style="float:right">
                    <button type="submit" onclick="window.location.href='{{ route( $routePath.'.create') }}';" class="md-btn md-btn-primary">Add</button>
                </div>
            </div>
            <div class="uk-overflow-container" style="overflow:visible;">
                <table id="grid-basic" class="uk-table uk-table-nowrap table_check">
                    <thead>
                        <tr>
                            {!! generateHTML('DeviceSN','s/n',$isAsc, $orderBy) !!}
                            {!! generateHTML('MacAddress','lan mac',$isAsc, $orderBy) !!}
                            {!! generateHTML('AnimalName','animal name',$isAsc, $orderBy) !!}
                            {!! generateHTML('IssueDate','issue date',$isAsc, $orderBy) !!}
                            <th class="uk-width-1-10 uk-text-small">register status</th>
                            {!! generateHTML('ShippedDate','customerInfo',$isAsc, $orderBy) !!}
                            <th class="uk-width-1-10 uk-text-small">height</th>
                            {!! generateHTML('LastUpdateOnLineTime','status',$isAsc, $orderBy) !!}
                            {!! generateHTML('DewiStatus','dewi status',$isAsc, $orderBy) !!}
                            {{-- {!! generateHTML('Region','regions',$isAsc, $orderBy) !!} --}}
                            <th class="uk-width-1-10 uk-text-small uk-text-center ">more</th>
                        </tr>
                    </thead>
                    @if($data->count() > 0)
                        @foreach ($data as $object)
                            <tr>
                                <td class="uk-text-small">{{ $object->DeviceSN }}</td>
                                <td class="uk-text-small">{{ $object->MacAddress }}</td>
                                <td class="uk-text-small">{{ $object->AnimalName }}</td>
                                <td class="uk-text-small">
                                    {{Carbon\Carbon::parse($object->IssueDate)->format('Y-m-d')}}
                                </td>
                                <td class="uk-text-small">
                                    @if($object->IfRegister == 1) 
                                        Y
                                    @else
                                        @if($object->IfKey == 0) 
                                            @if($object->IfAnimal == 1) 
                                                No Key
                                            @else
                                                N/A
                                            @endif
                                        @else
                                            No Animal
                                        @endif
                                    @endif
                                </td>
                                <td class="uk-text-small">
                                    @if($object->IsShipped == 1)
                                        {{ $object->TrackNo }}
                                        <br>
                                        {{ $object->Manufacturer->RealName }}
                                        <br>
                                        {{Carbon\Carbon::parse($object->ShippedDate)->format('Y-m-d')}}
                                    @else
                                        in stock
                                    @endif
                                </td>

                                <td class="uk-text-small">{{ $object->BlockHeight }}</td>
                                <td class="uk-text-small">
                                    @if($object->LastUpdateOnLineTime)
                                        <?php 
                                            $now = date_create( date('Y-m-d H:i:s',time() - (8 * 3600)));
                                            $LastUpdateOnLineTime = date_create( $object->LastUpdateOnLineTime);
                                            $time = date_diff($now, $LastUpdateOnLineTime);

                                            $minutes = $time->days * 24 * 60 * 60;
                                            $minutes += $time->h * 60 * 60;
                                            $minutes += $time->i * 60;
                                            $minutes += $time->s ;
                                            if($minutes <= 30 && $object->P2P_Connected == 1){
                                                print('<span class="material-icons" style="color:#59BBBC"> circle </span> online');
                                            }else{
                                                print('<span class="material-icons" style="color:#FF5959"> circle </span> offline');
                                            }
                                        ?>
                                    @else
                                        <span class="material-icons" style="color:#FF5959"> circle </span> offline
                                    @endif
                                </td>
                                <td class="uk-text-small">{{ $object->DewiStatus }}</td>
                                {{-- <td class="uk-text-small">{{ $object->Region }}</td> --}}

                                <td class="uk-text-center uk-text-small">
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
                                                        @else
                                                            <li style="pointer-events: none;"><a style="color:#FAFAFA;">Show on map</a></li>
                                                        @endif
                                                        {{-- 重開機 --}}
                                                        <li><a onclick="rebootHotspot('{{ $object->MacAddress }}')">Reboot</a></li>
                                                        {{-- <li><a href="#">Upgrade firmware</a></li>
                                                        <li><a href="#">Restart miner</a></li>
                                                        <li><a href="#">Trigger fast sync</a></li>
                                                        回報問題 --}}
                                                        {{-- <li><a data-uk-modal="{target:'#modal_header_footer'}">Report issue</a></li> --}}
                                                        {{-- <li><a href="#">Device heartbeat</a></li> --}}
                                                        {{-- 編輯 --}}
                                                        <li><a href="{{ route($routePath.'.edit',$object->$primaryKey) }}">Edit</a></li>
                                                        {{-- 會員 --}}
                                                        <li><a onclick="showUserList('{{ $object->$primaryKey }}')">User</a></li>
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
            </div>
            @include('Pagination')
        </div>
    </div>
    <!-- table end -->

    {{-- 變更會員 --}}
    <div class="uk-modal" id="update_hotspotOwner" aria-hidden="true" style="display: none; overflow-y: auto;">
            <div class="uk-modal-dialog" style="top: 199px;">
                <div class="uk-modal-header">
                    <h3 class="uk-modal-title">User</h3>
                </div>
                <p><input type="hidden" id="HID"></p>
                <div id="hotspotOwner"></div>
                <div class="uk-modal-footer uk-text-right">
                    <button type="button" class="md-btn md-btn-flat uk-modal-close">BACK</button>
                    <button onclick="javascript:updateUID()" type="button" class="md-btn md-btn-flat md-btn-flat-primary">OK</button>
                </div>
            </div>
        </div>
    {{-- 變更會員 --}}

    {{-- MAP --}}
    <div class="uk-modal uk-modal-card-fullscreen" id="modal_full" aria-hidden="true" style="display: none; overflow-y: auto;">
        <div class="uk-modal-dialog uk-modal-dialog-blank">
            <div class="md-card uk-height-viewport">
                <div class="md-card-toolbar">
                    <div class="md-card-toolbar-actions">
                        <div class="md-card-dropdown" data-uk-dropdown="{pos:'bottom-right'}">
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
    {{-- MAP --}}

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

        // 顯示會員選單
        function showUserList(ID) {
            $.ajax({
                url: '/api/v1/showUserList',
                type: 'POST',
                async: false,
                headers: {
                    'Authorization': Cookies.get('authToken')
                },
                data : { 
                    'ID' : ID,
                },
                success: function(response) {
                    if(response.status == 0){
                        // hideen the button
                        let hotspotOwner = '';

                        hotspotOwner += `<option value="" selected hidden>Select...</option>`;
                        
                        response.data.users.forEach(element => {
                            let selected = '';
                            if(response.data.select == element.Id) selected = 'selected';
                            hotspotOwner += `<option value="${element.Id}" ${selected}>${element.RealName}(${element.MemberNo})</option>`;
                        });
                        let userSelect = `<select name="UID" id="UID">${hotspotOwner}</select>`;
                        $('#hotspotOwner').html(userSelect);
                    }else{
                        // console.log(response.message);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log('error');
                    UIkit.modal.alert('讀取失敗！(error)').on('hide.uk.modal', function() {
                        // custome js code
                        console.log('close');
                        // window.location.reload();
                    });
                },
                complete: function () {
                    $('#UID').selectize({
                        plugins: {
                            'remove_button': {
                                label: ''
                            }
                        },
                    });

                    $('#update_hotspotOwner #HID').val(ID);
                    console.log(`update_hotspotOwner HID: ${$('#update_hotspotOwner #HID').val()}`);
                    UIkit.modal("#update_hotspotOwner").show();

                },
                cache: false
            });

        }

        // 更新所屬會員
        function updateUID() {
            //機器ID
            let HID = $('#update_hotspotOwner #HID').val();
            //會員ID
            let newUID = $('#update_hotspotOwner #UID').val();
            console.log(`HID: ${HID}`, `newUID: ${newUID}`);

            // if(newUID == '') {
            //     UIkit.modal.alert('請選擇商品').on('hide.uk.modal', function() {
            //         // custome js code
            //         console.log('close');
            //     });
            // } else {
                $.ajax({
                    url: '/api/v1/updateUID',
                    type: 'POST',
                    async: false,
                    headers: {
                        'Authorization': Cookies.get('authToken')
                    },
                    data : { 
                        'ID' : HID,
                        'newUID' : newUID,
                    },
                    success: function(response) {
                        if(response.status == 0){
                            // hideen the button
                            UIkit.modal.alert('更新成功！').on('hide.uk.modal', function() {
                                // custome js code
                                console.log('close');
                                let topic = `{{env('mqtt_prefix', '')}}/LiveShow/${HID}`;
                                // console.log(`topic: ${topic}`);
                                let sendData = {
                                                type          : "PrimaryProductUpdate",
                                            };
                                client.publish(topic, JSON.stringify(sendData), 1);
                            });
                        }else{
                            UIkit.modal.alert('更新失敗！').on('hide.uk.modal', function() {
                                // custome js code
                                console.log('close');
                            });
                            // console.log(response.message);
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
                        UIkit.modal("#update_hotspotOwner").hide();
                    },
                    cache: false
                });
            // }

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