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
            background-image: url('/assets/img/pin-green.png');
            background-size: cover;
            width: 33px;
            height: 53.75px;
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
        $(document).ready(function() {
            $('#statuscheck').change(function(event) {
                var status = $('#status').val();
                if(status == 1){
                    $('#status').val(0);
                }else{
                    $('#status').val(1);
                }
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

                        datahtml += `<font size=5>ip:`+response.data.ip+'</font>';
                        datahtml += `<br><font size=5>port:`+response.data.port+'</font>';
                        datahtml += `<br><font size=5>`+response.data.command+'</font>';
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

        function resetMAC(MAC){
            var modal =  UIkit.modal.blockUI('<div class=\'uk-text-center\'>Loading...<br/><img class=\'uk-margin-top\' src=\'/assets/img/spinners/spinner.gif\' alt=\'\'>');
            $.ajax({
                type: "POST",
                url:"https://linxdotapi.v7idea.com/resetMAC",
                data:{
                    mac: MAC 
                },
                timeout: 0,
                success: function(response){
                    modal.hide();
                    // alert(response);
                    if(response.status == 0){
                        alert('MAC Reset Successfully');
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
        {{ Form::hidden('status', '', array('id' => 'status')) }}
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
                                            {!! $searchFields['keywords']['completeField'] !!}
                                        </div>
                                        <!-- <div class="uk-width-1-3">
                                            {!! $searchFields['S/N']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['Mac']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['AnimalName']['completeField'] !!}
                                        </div> -->
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
                <div class="" style="margin:0;padding:0;" align="right">
                    @if($status == 1)
                        <input type="checkbox" data-switchery checked id="statuscheck" name="statuscheck"/>
                    @else
                        <input type="checkbox" data-switchery id="statuscheck" name="statuscheck"/>
                    @endif
                    <label for="status" class="inline-label">online</label>
                </div>
            </div>
            <div class="uk-overflow-container" style="overflow:visible;">
                <table id="grid-basic" class="uk-table uk-table-nowrap table_check">
                    <thead>
                        <tr>
                            {!! generateHTML('DeviceSN','s/n',$isAsc, $orderBy) !!}
                            {!! generateHTML('MacAddress','lan mac',$isAsc, $orderBy) !!}
                            {!! generateHTML('AnimalName','animal name',$isAsc, $orderBy) !!}
                            {!! generateHTML('OfficalNickName','nick name',$isAsc, $orderBy) !!}
                            <th class="uk-width-1-10 uk-text-small"></th>
                            {!! generateHTML('Firmware','Version',$isAsc, $orderBy) !!}
                            {!! generateHTML('IssueDate','provision date',$isAsc, $orderBy) !!}
                            {!! generateHTML('ShippedDate','delivery',$isAsc, $orderBy) !!}
                            {!! generateHTML('DewiStatus','dewi onboarded',$isAsc, $orderBy) !!}
                        </tr>
                    </thead>
                    @if($data->count() > 0)
                        @foreach ($data as $object)
                            @if($object->IsBlack == 1)
                                <tr style="color:#FF5959;">
                            @else
                                <tr>
                            @endif
                                <td class="uk-text-small">
                                    @if($object->LastUpdateOnLineTime)
                                        <?php
                                            $now = date_create( date('Y-m-d H:i:s',time() - (8 * 3600)));
                                            $LastUpdateOnLineTime = date_create( $object->LastUpdateOnLineTime);
                                            $time = date_diff($now, $LastUpdateOnLineTime);

                                            $minutes = $time->days * 24 * 60;
                                            $minutes += $time->h * 60;
                                            $minutes += $time->i;
                                            if($minutes <= 30){
                                                $online = 1;
                                                print('<span class="material-icons" style="color:#59BBBC;font-size:14px;"> circle </span>');
                                            }else{
                                                $online = 0;
                                                print('<span class="material-icons" style="color:#FF5959;font-size:14px;"> circle </span>');
                                            }
                                        ?>
                                    @else
                                        <?php
                                            $online = 0;
                                        ?>
                                        <span class="material-icons" style="color:#FF5959;font-size:14px;"> circle </span>
                                    @endif
                                    @if($object->IsBlack == 1)
                                        <a href="{{ route($routePath.'.edit',$object->$primaryKey) }}" style="color:#FF5959;">{{ $object->DeviceSN }}</a>
                                        <br>{{ $object->IsBackMemo }}
                                        <a onclick="showBlack('{{ $object->IsBlack }}','{{ $object->IsBackMemo }}','{{ $object->id }}')">
                                            <span class="material-icons" style="color:#AA3333;font-size:14px;"> build </span>
                                        </a>
                                    @else
                                        <a href="{{ route($routePath.'.edit',$object->$primaryKey) }}" style="color:#444444;">{{ $object->DeviceSN }}</a>
                                    @endif
                                    
                                </td>
                                <td class="uk-text-small">
                                    @if(isset($object->CurrentMacAddress))
                                        @if($object->CurrentMacAddress != $object->MacAddress)
                                            {{ $object->MacAddress }}<br>
                                            <font color="#AA3333">({{ $object->MacAddress }})    </font>
                                            <a  onclick="javascript:if(confirm('Do you confirm to recover its MAC?'))resetMAC('{{ $object->CurrentMacAddress }}')">
                                                <span class="material-icons" style="color:#AA3333;font-size:14px;"> build </span>
                                            </a>
                                        @else
                                            {{ $object->MacAddress }}
                                        @endif
                                    @else
                                        {{ $object->MacAddress }}
                                    @endif
                                </td>
                                <td class="uk-text-small">{{ $object->AnimalName }}</td>
                                <td class="uk-text-small" align="center">
                                    {{ $object->OfficalNickName }}
                                    <br>
                                    @if($object->NickName != null)
                                        ( {{ $object->NickName }} )
                                    @endif
                                </td>
                                <td class="uk-text-small">
                                    <span class="material-icons userMOUSE" onclick="showNickName('{{ $object->id }}','{{ $object->NickName }}')">create</span>
                                </td>
                                <td class="uk-text-small">
                                    @if(isset($object->Version))
                                        {{ $object->Version->VersionNo }}
                                    @else
                                        @if($object->Firmware != '' && $object->Firmware != null)
                                            {{ $object->Firmware }}
                                        @endif
                                    @endif<br>
                                    {{ substr($object->MinerVersion, -15) }}
                                </td>
                                <td class="uk-text-small">
                                    {{Carbon\Carbon::parse($object->IssueDate)->format('Y-m-d')}}
                                </td>
                                <td class="uk-text-small">
                                    {{-- MAC號如果不一致 --}}
                                    @if(isset($object->Warehouse))
                                        @if($object->Warehouse->IfShipped == 1)
                                            {{ $object->Warehouse->CustomInfo }}
                                            <br>
                                            {{Carbon\Carbon::parse($object->Warehouse->ShippedDate)->format('Y-m-d')}}
                                        @else
                                            in stock
                                        @endif
                                    @else
                                        in stock
                                    @endif
                                </td>
                                <td class="uk-text-small">
                                    <div style="position:relative;float:left;top:7px;text-align: center;width: 50px;">
                                        @if(isset($object->IsRegisteredDewi))
                                            @if($object->IsRegisteredDewi == 1)
                                                Y
                                            @else
                                                N
                                            @endif
                                        @else
                                            N
                                        @endif
                                    </div>
                                        <div class="md-card-list-wrapper" style="float:right;width:10px;">
                                            <div class="md-card-list" style="margin-top:0px">
                                                <div class="md-card-list-item-menu" data-uk-dropdown="{mode:'click',pos:'bottom-right'}">
                                                    <a class="md-icon material-icons">&#xE5D4;</a>
                                                    <div class="uk-dropdown" style="background:#C4C4C4">
                                                        <ul style="text-align:left;list-style:none;display: block;
                                                        margin-block-start:0px;margin-block-end:0px;margin-inline-start:0px;
                                                        margin-inline-end:0px;padding-inline-start:0px;line-height: 25px;">
                                                            {{--地圖--}}
                                                            @if($object->map_lat != null || $object->map_lat != '' && $object->map_lng != null || $object->map_lng != '')
                                                                <li><a data-uk-modal="{target:'#modal_full'}" onclick="map('{{ $object->map_lng }}','{{ $object->map_lat }}','{{ $online }}')">Show on map</a></li>
                                                            @else
                                                                <li style="pointer-events: none;"><a style="color:#FAFAFA;">Show on map</a></li>
                                                            @endif
                                                            {{-- 重開機 --}}
                                                            <li><a onclick="rebootHotspot('{{ $object->MacAddress }}')">Reboot</a></li>
                                                            {{-- 更新分位 --}}
                                                            <li><a onclick="Upgradefirmware('{{ $object->MacAddress }}')">Upgrade firmware</a></li>
                                                            {{-- <li><a href="#">Restart miner</a></li>
                                                            <li><a href="#">Trigger fast sync</a></li>
                                                            回報問題 --}}
                                                            {{-- <li><a data-uk-modal="{target:'#modal_header_footer'}">Report issue</a></li> --}}
                                                            {{-- 黑名單 --}}
                                                            <li><a onclick="showBlack('{{ $object->IsBlack }}','{{ $object->IsBackMemo }}','{{ $object->id }}')">Black</a></li>
                                                            {{-- <li><a href="#">Device heartbeat</a></li> --}}
                                                            {{-- 會員 --}}
                                                            <li><a onclick="showUserList('{{ $object->$primaryKey }}')">User</a></li>
                                                            {{-- Reverse SSH --}}
                                                            @if(isset($object->CurrentMacAddress))
                                                                @if($object->CurrentMacAddress != null)
                                                                    <li><a onclick="ReverseSSH('{{ $object->CurrentMacAddress }}')">Reverse SSH</a></li>
                                                                @else
                                                                    <li><a onclick="ReverseSSH('{{ $object->MacAddress }}')">Reverse SSH</a></li>
                                                                @endif
                                                            @else
                                                                <li><a onclick="ReverseSSH('{{ $object->MacAddress }}')">Reverse SSH</a></li>
                                                            @endif
                                                            {{-- helium explorer --}}
                                                            <li><a href="https://explorer.helium.com/hotspots/{{ $object->OnBoardingKey }}" target="_blank">Helium Explorer</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </td>
                                {{-- <td class="uk-text-small">{{ $object->DewiStatus }}</td> --}}

                                
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

    {{-- 黑名單 --}}
    <div class="uk-modal" id="black" aria-hidden="true" style="display: none; overflow-y: auto;">
            <div class="uk-modal-dialog" style="top: 199px;">
                <p><input type="hidden" id="HID"></p>
                <div id="blackmodal"></div>
                <div class="uk-modal-footer uk-text-right">
                    <button type="button" class="md-btn md-btn-flat uk-modal-close">BACK</button>
                    <button onclick="javascript:updateBlack()" type="button" class="md-btn md-btn-flat md-btn-flat-primary">OK</button>
                </div>
            </div>
        </div>
    {{-- 黑名單 --}}

    {{-- 暱稱 --}}
    <div class="uk-modal" id="nickname" aria-hidden="true" style="display: none; overflow-y: auto;">
            <div class="uk-modal-dialog" style="top: 199px;">
                <p><input type="hidden" id="HID"></p>
                <div id="nicknamemodal"></div>
                <div class="uk-modal-footer uk-text-right">
                    <button type="button" class="md-btn md-btn-flat uk-modal-close">BACK</button>
                    <button onclick="javascript:updateNickName()" type="button" class="md-btn md-btn-flat md-btn-flat-primary">OK</button>
                </div>
            </div>
        </div>
    {{-- 暱稱 --}}

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

        // 顯示黑名單
        function showBlack(black,memo,hid) {
            let blackmodal = '';
            var blackmemo = '';
            if(memo != null && memo != ""){
                blackmemo = memo;
            }

            blackmodal += '<div class="parsley-row">';
            blackmodal += '<div class="md-input-wrapper  md-input-filled">';
            blackmodal += '<label for="Black">Black<span class="req">*</span></label><br>';
            blackmodal += '<span class="icheck-inline">';
            if(black == 1){
                blackmodal += '<input data-md-icheck="" id="Black_1" checked="checked" name="Black" type="radio" value="1">';
            }else{
                blackmodal += '<input data-md-icheck="" id="Black_1" name="Black" type="radio" value="1">';
            }
            blackmodal += '<label for="Black_1" class="inline-label">Yes</label></span>';
            blackmodal += '<span class="icheck-inline">';
            if(black == 1){
                blackmodal += '<input data-md-icheck="" id="Black_0" name="Black" type="radio" value="0">';
            }else{
                blackmodal += '<input data-md-icheck="" id="Black_0" checked="checked" name="Black" type="radio" value="0">';
            }
            blackmodal += '<label for="Black_0" class="inline-label">No</label></span>';
            blackmodal += '<span class="parsley-required"></span></div></div>';

            blackmodal += '<div class="parsley-row">';
            blackmodal += '<div class="md-input-wrapper  md-input-filled">';
            blackmodal += '<label for="blackmemo">Memo</label>';
            blackmodal += '<input id="blackmemo" class="md-input label-fixed" name="blackmemo" type="text" value="'+blackmemo+'">';
            blackmodal += '</div></div>';

            $('#blackmodal').html(blackmodal);
            $('#black #HID').val(hid);
            UIkit.modal("#black").show();
        }

        // 更新所屬會員
        function updateUID() {
            //機器ID
            let HID = $('#update_hotspotOwner #HID').val();
            //會員ID
            let newUID = $('#update_hotspotOwner #UID').val();
            console.log(`HID: ${HID}`, `newUID: ${newUID}`);
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
        }

        // 更新黑名單狀態
        function updateBlack() {
        // 狀態
            let IsBlack = 0;
            var checked = document.getElementById("Black_1").checked;
            if(checked){
                IsBlack = 1;
            }else{
                IsBlack = 0;
            }
            // memo
            let IsBackMemo = $('#black #blackmemo').val();
            // 機器ID
            let HID = $('#black #HID').val();

            $.ajax({
                url: '/api/v1/updateIsBlack',
                type: 'POST',
                async: false,
                headers: {
                    'Authorization': Cookies.get('authToken')
                },
                data : { 
                    'IsBackMemo' : IsBackMemo,
                    'IsBlack' : IsBlack,
                    'ID' : HID
                },
                success: function(response) {
                    if(response.status == 0){
                        // hideen the button
                        UIkit.modal.alert('更新成功！');
                        window.location.reload();
                    }else{
                        UIkit.modal.alert('更新失敗！')
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


        function map(lng,lat,online){
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

            var marker = document.querySelector('.marker');
            if(online == 0){
                // alert(online);
                marker.style = "background-image: url('/assets/img/pin-red.png')";
            }else{
                marker.style = "background-image: url('/assets/img/pin-green.png')";
            }
        }
        // 顯示暱稱編輯畫面
        function showNickName(id,nickname) {
            let nicknamemodal = '';
            var name = '';
            if(nickname != null && nickname != ""){
                name = nickname;
            }

            nicknamemodal += '<div class="parsley-row">';
            nicknamemodal += '<div class="md-input-wrapper  md-input-filled">';
            nicknamemodal += '<label for="name">Nick Name</label>';
            nicknamemodal += '<input id="name" class="md-input label-fixed" name="name" type="text" value="'+name+'">';
            nicknamemodal += '</div></div>';

            $('#nicknamemodal').html(nicknamemodal);
            $('#nickname #HID').val(id);
            UIkit.modal("#nickname").show();
        }

        // 更新暱稱
        function updateNickName() {
            //機器ID
            let HID = $('#nickname #HID').val();
            //會員ID
            let name = $('#nickname #name').val();
            $.ajax({
                url: '/api/v1/UpdateNickName',
                type: 'POST',
                async: false,
                headers: {
                    'Authorization': Cookies.get('authToken')
                },
                data : { 
                    'ID' : HID,
                    'name' : name,
                },
                success: function(response) {
                    if(response.status == 0){
                        // hideen the button
                        UIkit.modal.alert('更新成功！')
                        window.location.reload();
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
                    UIkit.modal("#nickname").hide();
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