{{-- 載入主要的版型 --}}
@extends('layouts.master')

{{-- 額外增加所需要的css檔案 --}}
@section('extraCssArea')
    
@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')
    <script>
        function RegisteredDewi(ID){
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
                        List(ID);
                    }else{
                        alert(response.errorMessage);
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log('error');
                },
            });
        }
    </script>
@endsection


{{-- 設定視窗的標題 --}}
@section('title', $functionname)

{{-- 設定內容的主標題區 --}}
@section('pageTitle', 'IMPORT')

{{-- 設定內容的主標題區 --}}
@section('breadcrumbArea')
    <ul id="breadcrumbs">
        <li><a href="/Default">Home</a></li>
        <li><a href="{!! $functionURL !!}">{!! $TOPname !!}</a></li>
        <li><a href="{!! $functionURL !!}">{!! $functionname !!}</a></li>
    </ul>
@endsection

{{-- 設定內容 --}}
@section('content')
    <h4 class="heading_a uk-margin-bottom">Import</h4>
    @if (count($errors) > 0)
        <div class="alert alert-danger parsley-errors-list filled">
            <strong>您好</strong>，您輸入的資料有問題，請在確認後重新輸入！<br><br>
        </div>
    @endif
    {!! Form::open(array('route' => $routePath.'.store','method'=>'POST','autocomplete' => 'off' , 'role' => 'presentation' , 'class'=> 'class="uk-form-stacked"', 'id'=> 'form_validation','enctype'=>'multipart/form-data')) !!}
        @include($viewPath.'.form')  
    {!! Form::close() !!}
    <!-- table start -->

    <h4 class="heading_a uk-margin-bottom">List</h4>
    <div class="md-card uk-margin-medium-bottom">
        <div class="md-card-content">
            <div class="uk-overflow-container">
                <table id="grid-basic" class="uk-table uk-table-nowrap table_check">
                    <thead>
                        <tr>
                            <th class="uk-width-1-6 uk-text-small">ImportDate</th>
                            <th class="uk-width-1-6 uk-text-small">FileName</th>
                            <th class="uk-width-1-6 uk-text-small">IfCompleteImport</th>
                            <th class="uk-width-1-6 uk-text-small">TotalRecords</th>
                            {{-- <th class="uk-width-1-6 uk-text-small">CreateBy</th> --}}
                            <th class="uk-width-1-6 uk-text-small">More</th>
                        </tr>
                    </thead>
                    @if($data->count() > 0)
                        @foreach ($data as $object)
                            <tr>
                                <td class="uk-text-small">
                                    @if($object->ImportDate)
                                        {{ Carbon\Carbon::parse($object->ImportDate)->format('Y/m/d H:s:i') }}
                                    @endif
                                </td>
                                <td class="uk-text-small">{{ $object->FileName }}</td>
                                <td class="uk-text-small">{{ $object->IfCompleteImport }}</td>
                                <td class="uk-text-small">{{ $object->TotalRecords }}</td>
                                {{-- <td class="uk-text-small">
                                    @if($object->Creater)
                                        {{ $object->Creater->RealName }}
                                    @endif
                                </td> --}}
                                <td class="uk-text-small"><span class="material-icons" data-uk-modal="{target:'#modal_full'}" onclick="List('{{ $object->id }}')">description</span></td>
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

    {{-- 清單 --}}
    <div class="uk-modal uk-modal-card-fullscreen" id="modal_full" aria-hidden="true" style="display: none; overflow-y: auto;">
        <div class="uk-modal-dialog uk-modal-dialog-blank">
            <div class="md-card uk-height-viewport">
                <div class="md-card-toolbar">
                    <div class="md-card-toolbar-actions">
                        <div class="md-card-dropdown" data-uk-dropdown="{pos:'bottom-right'}">
                        </div>
                    </div>
                    <span class="md-icon material-icons uk-modal-close"></span>
                    <h3 class="md-card-toolbar-heading-text"></h3>
                </div>
                <div class="md-card-content" id='list'>
                    
                    
                </div>
            </div>
        </div>
    </div>
    {{-- 清單 --}}


    <script>
        function resetForm() {
            location.href = '{{ route('Hotspots'.'.index') }}';
        }
        function gotoPage(pageNo) {
            $('#Page').val(pageNo);
            $('#IfNewSearch').val('0');
            $('#searchForm').submit();
        }
        function List(ID) {
            // alert(ID);
            $.ajax({
                url: '/api/v1/WarehouseInventoryDetail2',
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
                        let HTML = '<button type="button" onclick="RegisteredDewi(';
                        HTML += ID;
                        HTML += ')" class="md-btn md-btn-primary">Dewi</button>';
                        HTML += '<table class="uk-table uk-table-nowrap table_check">';
                        HTML += '<thead><tr>';
                        HTML += '<th class="uk-width-1-10">SkuID</th>';
                        HTML += '<th class="uk-width-1-10">PalletId</th>';
                        HTML += '<th class="uk-width-1-10">CartonId</th>';
                        HTML += '<th class="uk-width-1-10">DeviceSN</th>';
                        HTML += '<th class="uk-width-1-10">IfShipped</th>';
                        HTML += '<th class="uk-width-1-10">ShippedDate</th>';
                        HTML += '<th class="uk-width-1-10">TrackingNo</th>';
                        HTML += '<th class="uk-width-1-10">ImportStatus</th>';
                        HTML += '<th class="uk-width-1-10">Registered</th>';
                        HTML += '<th class="uk-width-1-10">RegisteredDate</th>';
                        HTML += '</tr></thead>';
                        
                        response.data.forEach(element => {
                            if(element.IfCompletedImport == 1 && element.ImportStatus == 1 && element.IsRegisteredDewi == 1){
                                HTML += '<tr>';
                            }else{
                                HTML += "<tr style='color: red;'>";
                            }
                            HTML += '<td class="uk-text-small">';
                            HTML += element.SkuID;
                            HTML += '</td>';
                            HTML += '<td class="uk-text-small">';
                            HTML += element.PalletId;
                            HTML += '</td>';
                            HTML += '<td class="uk-text-small">';
                            HTML += element.CartonId;
                            HTML += '</td>';
                            HTML += '<td class="uk-text-small">';
                            HTML += element.DeviceSN;
                            HTML += '</td>';
                            HTML += '<td class="uk-text-small">';
                            HTML += element.IfShipped;
                            HTML += '</td>';
                            HTML += '<td class="uk-text-small">';
                            HTML += element.ShippedDate;
                            HTML += '</td>';
                            HTML += '<td class="uk-text-small">';
                            HTML += element.TrackingNo;
                            HTML += '</td>';
                            HTML += '<td class="uk-text-small">';
                            if(element.ImportStatus == 1){
                                HTML += "Y";
                            }else{
                                HTML += "N";
                                if(element.ImportMemo){
                                    HTML += "(";
                                    HTML += element.ImportMemo;
                                    HTML += ")";
                                }
                            }
                            HTML += '</td>';
                            HTML += '<td class="uk-text-small">';
                            if(element.IsRegisteredDewi == 1){
                                HTML += "Y";
                            }else if(element.IsRegisteredDewi == 0){
                                HTML += "N";
                            }else{
                                HTML += "Error";
                                if(element.LastRegisterDewiMemo){
                                    HTML += "(";
                                    HTML += element.LastRegisterDewiMemo;
                                    HTML += ")";
                                }
                            }
                            HTML += '</td>';
                            HTML += '<td class="uk-text-small">';
                            if(element.IsRegisteredDewi == 1){
                                if(element.LastRegisterDewiDate){
                                    HTML += element.LastRegisterDewiDate;
                                }
                            }else{
                                HTML += "";
                            }
                            HTML += '</td>';
                            HTML +='</tr>';
                        });
                        HTML += '</table>';
                        
                        $('#list').html(HTML);
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
            });
        }
    </script>
@endsection