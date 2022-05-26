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
@section('pageTitle', $functionname.'('.$username.')')

{{-- 設定內容的主標題區 --}}
@section('breadcrumbArea')
    <ul id="breadcrumbs">
        <li><a href="/Default">Home</a></li>
        <li><a href="{!! $functionURL !!}">{!! $TOPname !!}</a></li>
        <li><a href="{!! $functionURL !!}">B2B</a></li>
    </ul>
@endsection

{{-- 設定內容 --}}
@section('content')
    <!-- search area start-->
    <h4 class="heading_a uk-margin-bottom">Search</h4>
    {!! Form::open(array('url' => $url,'method'=>'GET', 'id'=> 'searchForm')) !!}
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
                    <button type="button" class="md-btn md-btn-warning" onclick="back();">BACK</button>
                </div>
            </div>
            <div class="uk-overflow-container">
                <table id="grid-basic" class="uk-table uk-table-nowrap table_check">
                    <thead>
                        <tr>
                            {!! generateHTML('DeviceSN','s/n',$isAsc, $orderBy) !!}
                            {!! generateHTML('MacAddress','lan mac',$isAsc, $orderBy) !!}
                            {!! generateHTML('AnimalName','animal name',$isAsc, $orderBy) !!}
                            {!! generateHTML('IssueDate','issue date',$isAsc, $orderBy) !!}
                            {!! generateHTML('IfVerifyDate','verify status',$isAsc, $orderBy) !!}
                            <th class="uk-width-1-10 uk-text-small">register status</th>
                            {!! generateHTML('ShippedDate','customerInfo',$isAsc, $orderBy) !!}
                            {{-- <th class="uk-width-1-10 uk-text-small">edit</th> --}}
                        </tr>
                    </thead>
                    @if($data->count() > 0)
                        @foreach ($data as $object)
                            <tr>
                                <td class="uk-text-small">{{ $object->DeviceSN }}</td>
                                <td class="uk-text-small">{{ $object->MacAddress }}</td>
                                <td class="uk-text-small">{{ $object->AnimalName }}</td>
                                <td class="uk-text-small">
                                    {{Carbon\Carbon::parse($object->IssueDate)->format('Y-m-d H:i:s')}}
                                </td>
                                <td class="uk-text-small">
                                    @if($object->IsVerify == 1) 
                                        {{Carbon\Carbon::parse($object->IfVerifyDate)->format('Y-m-d H:i:s')}}
                                    @else
                                        N/A
                                    @endif
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
                                        {{Carbon\Carbon::parse($object->ShippedDate)->format('Y-m-d H:i:s')}}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                {{-- <td class="uk-text-center uk-text-small">
                                    <a href="{{ route($routePath.'.edit',$object->$primaryKey) }}"><i class="md-icon material-icons">&#xE254;</i></a>
                                    {!! Form::open(['id' => 'formDeleteAction'.$i , 'method' => 'DELETE','route' => [ $routePath.'.destroy', $object->$primaryKey],'style'=>'display:inline']  ) !!}
                                        <a href="javascript:if(confirm('Are you sure to delete this datum?'))$('{{ '#formDeleteAction'.$i }}').submit();"><i class="md-icon material-icons">&#xE872;</i></a>
                                    {!! Form::close() !!}
                                </td> --}}
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

        function back() {
            window.location.href = '/Customer/B2B';
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