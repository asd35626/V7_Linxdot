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
            $('#keywords').keypress(function(event) {
                if (event.which == 13) {
                    var status = $('#status').val();
                    if(status == 1){
                        $('#status').val(1);
                    }else{
                        $('#status').val(0);
                    }
                    $('#excel').val(0);
                    $('#IfSearch').val('1');
                    $('#IfNewSearch').val('1');
                    $('#Page').val('1');
                    $('#searchForm').submit();
                    $('#searchForm').submit();
                }
            });
        })

        function rebootHotspot(MAC){
            var modal =  UIkit.modal.blockUI('<div class=\'uk-text-center\'>Loading...<br/><img class=\'uk-margin-top\' src=\'/assets/img/spinners/spinner.gif\' alt=\'\'>');
            $.ajax({
                type: "POST",
                url:"{{env('API_URL_49880', '')}}rebootHotspot",
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
            var yes = confirm('Do you confirm to upgrade?');
            if(yes){
                var modal =  UIkit.modal.blockUI('<div class=\'uk-text-center\'>Loading...<br/><img class=\'uk-margin-top\' src=\'/assets/img/spinners/spinner.gif\' alt=\'\'>');
                $.ajax({
                    type: "POST",
                    url:"{{env('API_URL_49880', '')}}ota",
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
                            case 504:
                                if(check()){
                                    alert("Upgarde in progress. Please check the device later.");
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

        function ReverseSSH(MAC){
            var modal =  UIkit.modal.blockUI('<div class=\'uk-text-center\'>Loading...<br/><img class=\'uk-margin-top\' src=\'/assets/img/spinners/spinner.gif\' alt=\'\'>');
            $.ajax({
                type: "POST",
                url:"{{env('API_URL_49880', '')}}reverseSSH",
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
                url:"{{env('API_URL_49880', '')}}resetMAC",
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

        function block(MAC){
            var yes = confirm('Do you confirm to block it?');
            if (yes) {
                $.ajax({
                    url: '/api/v1/Block',
                    type: 'POST',
                    async: false,
                    headers: {
                        'Authorization': Cookies.get('authToken')
                    },
                    data : { 
                        'MAC' : MAC
                    },
                    success: function(response) {
                        blockapi(MAC);
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log('error');
                    },
                    cache: false
                });
            }  
        }

        function blockapi(MAC){
            var modal =  UIkit.modal.blockUI('<div class=\'uk-text-center\'>Loading...<br/><img class=\'uk-margin-top\' src=\'/assets/img/spinners/spinner.gif\' alt=\'\'>');
            $.ajax({
                type: "POST",
                url:"{{env('API_URL_49880', '')}}blockMiner",
                data:{
                    'mac' : MAC
                },
                timeout: 0,
                success: function(response){
                    // alert(response);
                    modal.hide();
                    if(response.status == 0){
                        alert('Successfully!');
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

        function registDewi(MAC){
            var modal =  UIkit.modal.blockUI('<div class=\'uk-text-center\'>Loading...<br/><img class=\'uk-margin-top\' src=\'/assets/img/spinners/spinner.gif\' alt=\'\'>');
            $.ajax({
                type: "POST",
                url:"https://heipeng-0715rc.linxdot.wtf/registDewi",
                data:{
                    'mac' : MAC
                },
                timeout: 0,
                success: function(response){
                    // alert(response);
                    modal.hide();
                    if(response.status == 0){
                        alert('Successfully!');
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

        function GetHotspotdata(){
            let page = $('#Page').val();
            let IfNewSearch = $('#IfNewSearch').val();
            let IfSearch = $('#IfSearch').val();
            let orderBy = $('#orderBy').val();
            let isAsc = $('#isAsc').val();
            let status = $('#status').val();
            var keywords = $('#keywords').val();
            var IsVerify = $('#IsVerify').val();
            var IfRegister = $('#IfRegister').val();
            var IssueDateFrom = $('#IssueDateFrom').val();
            var IssueDateTo = $('#IssueDateTo').val();
            var VerifyDateFrom = $('#VerifyDateFrom').val();
            var VerifyDateTo = $('#VerifyDateTo').val();
            var ModelID = $('#ModelID').val();

            $.ajax({
                url: '/api/v1/GetHotspot',
                type: 'POST',
                async: false,
                headers: {
                    'Authorization': Cookies.get('authToken')
                },
                data : { 
                    'page' : page,
                    'IfNewSearch' : IfNewSearch,
                    'IfSearch' : IfSearch,
                    'orderBy' : orderBy,
                    'isAsc' : isAsc,
                    'status' : status,
                    'keywords' : keywords,
                    'IsVerify' : IsVerify,
                    'IfRegister' : IfRegister,
                    'IssueDateFrom' : IssueDateFrom,
                    'IssueDateTo' : IssueDateTo,
                    'VerifyDateFrom' : VerifyDateFrom,
                    'VerifyDateTo' : VerifyDateTo,
                    'ModelID' : ModelID,
                },
                success: function(response) {
                    if(response.status == 0){
                        var data = [];
                        var Version = '';
                        var delivery = '';
                        var dewionboarded = '';
                        var IssueDate = '';
                        response.data.forEach(element => {
                            if(element.Version){
                                Version = element.Version.VersionNo
                            }else{
                                if(element.Firmware != "" && element.Firmware != null){
                                    Version = element.Firmware
                                }
                            }
                            if(element.Warehouse){
                                if(element.Warehouse.IfShipped == 1){
                                    delivery = element.Warehouse.CustomInfo+element.Warehouse.ShippedDate
                                }else{
                                    delivery = "in stock"
                                }
                            }else{
                                    delivery = "in stock"
                            }

                            if(element.IsRegisteredDewi){
                                if(element.IsRegisteredDewi == 1){
                                    dewionboarded = "Y"
                                }else{
                                    dewionboarded = "N"
                                }
                            }else{
                                    dewionboarded = "N"
                            }
                            IssueDate = element.IssueDate;
                            IssueDate = new Date(IssueDate.replace(/-/g,"/"));
                            getTime = IssueDate.getTime();
                            let formatted_date = IssueDate.getFullYear() + "-" + (IssueDate.getMonth() + 1) + "-" + IssueDate.getDate();

                            data.push({
                                "s/n": element.DeviceSN,
                                "model": element.ModelName,
                                "lan mac": element.MacAddress,
                                "animalname": element.AnimalName,
                                "nickname": element.OfficalNickName,
                                "verify": Version,
                                "provision date": formatted_date,
                                "delivery": delivery,
                                "dewi onboarded": dewionboarded
                            })
                        });
                        exportcsv(data);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log('error');
                },
            });
        }

        function IfNeedRegisteredDewi(){
            $.ajax({
                url: 'https://hotspot-auth.linxdot.wtf/IfNeedRegisteredDewi',
                type: 'GET',
                async: false,
                headers: {
                    'Authorization': Cookies.get('authToken')
                },
                data : {
                },
                success: function(response) {
                    if(response.status == 0){
                        alert('success!');
                    }else{
                        alert(response.errorMessage);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log('error');
                },
            });
        }

        function exportcsv(data){
            // alert(123);
            const buildData = data => {
                return new Promise((resolve, reject) => {
                    // 最後所有的資料會存在這
                    let arrayData = [];
                    // 取 data 的第一個 Object 的 key 當表頭
                    let arrayTitle = Object.keys(data[0]);
                    arrayData.push(arrayTitle);
                    // 取出每一個 Object 裡的 value，push 進新的 Array 裡
                    Array.prototype.forEach.call(data, d => {
                        let items = [];
                        Array.prototype.forEach.call(arrayTitle, title => {
                            let item = d[title] || '';
                            items.push(item);
                        });
                        arrayData.push(items)
                    })
                    resolve(arrayData);
                })
            }

            const downloadCSV = data => {
                let csvContent = '';
                Array.prototype.forEach.call(data, d => {
                    let dataString = d.join(',') + '\n';
                    csvContent += dataString;
                })
                // 下載的檔案名稱
                let fileName = '下載資料_' + (new Date()).getTime() + '.csv';

                // 建立一個 a，並點擊它
                let link = document.createElement('a');
                link.setAttribute('href', 'data:text/csv;charset=utf-8,%EF%BB%BF' + encodeURI(csvContent));
                link.setAttribute('download', fileName);
                link.click();
            }
            buildData(data).then(data => downloadCSV(data));
        }
        
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
                                    </div>
                                    <div class="uk-grid">
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['IsVerify']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['IfRegister']['completeField'] !!}
                                        </div>
                                        <div class="uk-width-1-3">
                                            {!! $searchFields['ModelID']['completeField'] !!}
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
                <!-- <div class="uk-width-1-10" style="float:right">
                    <button type="submit" onclick="exportExcel()" class="md-btn md-btn-primary">Export</button>
                </div> -->
                <div class="uk-width-1-10" style="float:right">
                    <button type="submit" onclick="GetHotspotdata()" class="md-btn md-btn-primary">Export</button>
                </div>
                <div class="uk-width-1-10" style="float:right">
                    <button type="submit" onclick="window.location.href='{{ route('Excel.create') }}';" class="md-btn md-btn-primary">Import</button>
                </div>
                <div class="uk-width-1-10" style="float:right">
                    <button type="submit" onclick="window.location.href='{{ route( $routePath.'.create') }}';" class="md-btn md-btn-primary">Add</button>
                </div>                
                <div class="uk-width-1-10" style="float:right">
                    <button type="submit" onclick="IfNeedRegisteredDewi()" class="md-btn md-btn-primary">Dewi</button>
                </div>
                <!-- <div class="uk-width-1-10" style="float:right">
                    <button type="submit" onclick="updateonline()" class="md-btn md-btn-primary">Refresh</button>
                </div> -->
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
                            {!! generateHTML('ModelName','model',$isAsc, $orderBy) !!}
                            {!! generateHTML('MacAddress','lan mac',$isAsc, $orderBy) !!}
                            {!! generateHTML('AnimalName','animal name',$isAsc, $orderBy) !!}
                            {!! generateHTML('OfficalNickName','nickname',$isAsc, $orderBy) !!}
                            <th class="uk-width-1-10 uk-text-small"></th>
                            {!! generateHTML('Firmware','Version',$isAsc, $orderBy) !!}
                            {!! generateHTML('IssueDate','provision date',$isAsc, $orderBy) !!}
                            {!! generateHTML('ShippedDate','delivery',$isAsc, $orderBy) !!}
                            {!! generateHTML('DewiStatus','dewi onboarded',$isAsc, $orderBy) !!}
                        </tr>
                    </thead>
                    @if($data->count() > 0)
                        @foreach ($data as $object)
                            @if($object->IsBlocked == 1)
                                <tr style="color:#CC9999;background: #333366">
                            @elseif($object->IsBlack == 1)
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
                                                print('<span class="material-icons" style="color:#59BBBC;font-size:14px;" id="online_'.$object->MacAddress.'"> circle </span>');
                                            }else{
                                                $online = 0;
                                                print('<span class="material-icons" style="color:#FF5959;font-size:14px;" id="online_'.$object->MacAddress.'"> circle </span>');
                                            }
                                        ?>
                                    @else
                                        <?php
                                            $online = 0;
                                        ?>
                                        <span class="material-icons" style="color:#FF5959;font-size:14px;" id="online_{{$object->MacAddress}}" > circle </span>
                                    @endif
                                    @if($object->IsBlocked == 1)
                                        @if($object->IsBlack == 1)
                                            <a href="{{ route($routePath.'.edit',$object->$primaryKey) }}" style="color:#CC9999;">{{ $object->DeviceSN }}</a>
                                            <br>{{ $object->IsBackMemo }}
                                            <a onclick="showBlack('{{ $object->IsBlack }}','{{ $object->IsBackMemo }}','{{ $object->MacAddress }}')">
                                                <span class="material-icons" style="color:#AA3333;font-size:14px;"> build </span>
                                            </a>
                                        @else
                                            <a href="{{ route($routePath.'.edit',$object->$primaryKey) }}" style="color:#CC9999;">{{ $object->DeviceSN }}</a>
                                        @endif
                                    @elseif($object->IsBlack == 1)
                                        <a href="{{ route($routePath.'.edit',$object->$primaryKey) }}" style="color:#FF5959;">{{ $object->DeviceSN }}</a>
                                        <br>{{ $object->IsBackMemo }}
                                        <a onclick="showBlack('{{ $object->IsBlack }}','{{ $object->IsBackMemo }}','{{ $object->MacAddress }}')">
                                            <span class="material-icons" style="color:#AA3333;font-size:14px;"> build </span>
                                        </a>
                                    @else
                                        <a href="{{ route($routePath.'.edit',$object->$primaryKey) }}" style="color:#444444;">{{ $object->DeviceSN }}</a>
                                    @endif
                                    
                                </td>
                                <td class="uk-text-small" id="MAC_{{ $object->MacAddress }}">
                                    {{ $object->ModelName }}
                                </td>
                                <td class="uk-text-small" id="MAC_{{ $object->MacAddress }}">
                                    @if(isset($object->CurrentMacAddress))
                                        @if($object->CurrentMacAddress != $object->MacAddress)
                                            {{ $object->MacAddress }}<br>
                                            <font color="#AA3333">({{ $object->CurrentMacAddress }})    </font>
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
                                    <span class="material-icons userMOUSE" onclick="showNickName('{{ $object->id }}','{{ $object->OfficalNickName }}')">create</span>
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
                                                N <span onclick="registDewi('{{ $object->MacAddress }}')" class="material-icons userMOUSE">info</span>
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
                                                        {{-- 地圖 --}}
                                                        @if($object->map_lat != null || $object->map_lat != '' && $object->map_lng != null || $object->map_lng != '')
                                                            <li><a data-uk-modal="{target:'#modal_full'}" onclick="map('{{ $object->map_lng }}','{{ $object->map_lat }}','{{ $online }}')">Show on map</a></li>
                                                        @else
                                                            <li style="pointer-events: none;"><a style="color:#FAFAFA;">Show on map</a></li>
                                                        @endif

                                                        {{-- 導向linxdot網站 --}}
                                                        <li><a href="https://explorer.helium.com/hotspots/{{ $object->OnBoardingKey }}" target="_blank">Helium Explorer</a></li>

                                                        {{-- 所屬會員 --}}
                                                        <li><a onclick="showUserList('{{ $object->$primaryKey }}')">User</a></li>

                                                        {{-- 重開機 --}}
                                                        <li><a onclick="rebootHotspot('{{ $object->MacAddress }}')">Reboot</a></li>

                                                        {{-- 更新分位 --}}
                                                        <li><a onclick="Upgradefirmware('{{ $object->MacAddress }}')">Upgrade firmware</a></li>
                                                        {{-- <li><a href="#">Restart miner</a></li>
                                                        <li><a href="#">Trigger fast sync</a></li> --}}

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

                                                        {{-- 黑名單 --}}
                                                        <li><a onclick="showBlack('{{ $object->IsBlack }}','{{ $object->IsBackMemo }}','{{ $object->MacAddress }}')">Black</a></li>

                                                        {{-- block --}}
                                                        @if($object->IsBlocked == 0)
                                                            <li><a onclick="block('{{ $object->MacAddress }}')">Block</a></li>
                                                        @else
                                                            <li style="pointer-events: none;"><a style="color:#FAFAFA;">Block</a></li>
                                                        @endif

                                                        {{-- 回報問題 --}}
                                                        <!-- <li><a data-uk-modal="{target:'#modal_header_footer'}">Report issue</a></li> -->
                                                        <li><a onclick="showIssue('{{ $object->AnimalName }}','{{ $object->DeviceSN }}','{{ $object->MacAddress }}')">Report Issue</a></li>
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
                    <button onclick="javascript:Unassign()" type="button" class="md-btn md-btn-flat uk-modal-close">Unassign</button>
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

    {{-- 回報問題 --}}
    <div class="uk-modal" id="issue">
        <div class="uk-modal-dialog">
            <div class="uk-modal-header" style="background:#45B7C4;margin-top:-25px;height:50px;display:flex;align-items:center;">
                <h3 align="center" valign="center" style="color:#E8F6F8">Report Issue</h3>
            </div>
            <div>
                <p><input type="hidden" id="MAC"></p>
                <p><input type="hidden" id="SN"></p>
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
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-1">
                            {!! $formFields['AnimalName']['completeField']  !!}
                        </div>
                    </div>
                    <h3 class="heading_a" style="padding-left: 0px;">Issue</h3>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-1">
                            {!! $formFields['LogType']['completeField']  !!}
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-1">
                            {!! $formFields['Subject']['completeField']  !!}
                        </div>
                    </div>
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-1" style="width: 100%">
                            {!! $formFields['Description']['completeField']  !!}
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
    {{-- 回報問題 --}}

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
        function showBlack(black,memo,MacAddress) {
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
            $('#black #HID').val(MacAddress);
            UIkit.modal("#black").show();
        }

        // 顯示回報問題
        function showIssue(name,sn,mac) {
            $('#issue #AnimalName').val(name);
            $('#issue #DeviceSN').val(sn);
            $('#issue #MacAddress').val(mac);
            $('#issue #MAC').val(mac);
            $('#issue #SN').val(sn);
            UIkit.modal("#issue").show();
        }

        // 回報問題
        function updateIssue() {
            var mac = $('#issue #MAC').val();
            var sn = $('#issue #SN').val();
            var Subject = $('#issue #Subject').val();
            var Description = tinyMCE.get('Description').getContent();

            var LogType = 0;
            var radios = document.getElementsByName('LogType');
            for (var i = 0, length = radios.length; i < length; i++) {
                if (radios[i].checked) {
                    LogType = radios[i].value;
                    break;
                }
            }

            $.ajax({
                url: '/api/v1/UpdateIssue',
                type: 'POST',
                async: false,
                headers: {
                    'Authorization': Cookies.get('authToken')
                },
                data : { 
                    'MAC' : mac,
                    'SN' : sn,
                    'Subject' : Subject,
                    'Description' : Description,
                    'LogType' : LogType,
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
                        UIkit.modal.alert('Updated!').on('hide.uk.modal', function() {
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

        // 清除所屬會員
        function Unassign() {
            //機器ID
            let HID = $('#update_hotspotOwner #HID').val();
            //會員ID
            let newUID = null;
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
                        UIkit.modal.alert('Updated!').on('hide.uk.modal', function() {
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
            let MacAddress = $('#black #HID').val();

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
                    'MacAddress' : MacAddress
                },
                success: function(response) {
                    if(response.status == 0){
                        // hideen the button
                        UIkit.modal.alert('Updated!');
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
                        UIkit.modal.alert('Updated!')
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

        setInterval(function(){
            let page = $('#Page').val();
            let IfNewSearch = $('#IfNewSearch').val();
            let IfSearch = $('#IfSearch').val();
            let orderBy = $('#orderBy').val();
            let isAsc = $('#isAsc').val();
            let status = $('#status').val();
            var keywords = $('#keywords').val();
            var IsVerify = $('#IsVerify').val();
            var IfRegister = $('#IfRegister').val();
            var IssueDateFrom = $('#IssueDateFrom').val();
            var IssueDateTo = $('#IssueDateTo').val();
            var VerifyDateFrom = $('#VerifyDateFrom').val();
            var VerifyDateTo = $('#VerifyDateTo').val();
            var ModelID = $('#ModelID').val();

            $.ajax({
                url: '/api/v1/GetOnlineTime',
                type: 'POST',
                async: false,
                headers: {
                    'Authorization': Cookies.get('authToken')
                },
                data : { 
                    'page' : page,
                    'IfNewSearch' : IfNewSearch,
                    'IfSearch' : IfSearch,
                    'orderBy' : orderBy,
                    'isAsc' : isAsc,
                    'status' : status,
                    'keywords' : keywords,
                    'IsVerify' : IsVerify,
                    'IfRegister' : IfRegister,
                    'IssueDateFrom' : IssueDateFrom,
                    'IssueDateTo' : IssueDateTo,
                    'VerifyDateFrom' : VerifyDateFrom,
                    'VerifyDateTo' : VerifyDateTo,
                    'ModelID' : ModelID,
                },
                success: function(response) {
                    if(response.status == 0){
                        var onlincolor = '';
                        var mac = '';
                        let lastOnLineTime = "";
                        let currentDate = "";
                        let limit = 30 * 60 * 1000;
                        let getTime = "";
                        let offset = "";
                        response.data.data.forEach(element => {
                            // alert(element.MacAddress);
                            mac = element.MacAddress;
                            onlincolor = document.getElementById('online_'+mac);
                            if(onlincolor != null){
                                // onlincolor.style.color = "#59BBBC";
                                // console.log(mac);
                                if(element.LastUpdateOnLineTime != null){
                                    // 現在時間
                                    currentDate = new Date();
                                    // 處理取出的lastOnLineTime
                                    lastOnLineTime = element.LastUpdateOnLineTime;
                                    lastOnLineTime = new Date(lastOnLineTime.replace(/-/g,"/"));
                                    getTime = lastOnLineTime.getTime();
                                    getTime = getTime+(8*60*60*1000);
                                    // console.log(getTime);
                                    // 計算時間差
                                    offset = currentDate.getTime() - getTime
                                    if(offset <= limit){
                                        onlincolor.style.color = "#59BBBC"
                                    }else{
                                        onlincolor.style.color = "#FF5959"
                                    }
                                }else{
                                    onlincolor.style.color = "#FF5959";
                                }
                            }
                                
                        });
                        // UIkit.modal.alert('Updated!')
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log('error');
                },
                cache: false
            });
        },300000)
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