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
    <div class="md-card uk-margin-medium-bottom">
        <div class="md-card-content">
            <div class="uk-overflow-container">
                <table id="grid-basic" class="uk-table uk-table-nowrap table_check">
                    <thead>
                        <tr>
                            {!! generateHTML('DeviceSN','S/N',$isAsc, $orderBy) !!}
                            {!! generateHTML('MacAddress','MAC Address',$isAsc, $orderBy) !!}
                            {!! generateHTML('AnimalName','Animal name',$isAsc, $orderBy) !!}
                            <th class="uk-width-1-10 uk-text-small">Height</th>
                            {!! generateHTML('LastUpdateOnLineTime','Status',$isAsc, $orderBy) !!}
                            {!! generateHTML('P2P_Connected','p2p_connected',$isAsc, $orderBy) !!}
                            {!! generateHTML('P2P_Dialable','dialable',$isAsc, $orderBy) !!}
                            {!! generateHTML('P2P_NatType','nat_type',$isAsc, $orderBy) !!}
                            {!! generateHTML('Region','regions',$isAsc, $orderBy) !!}
                            {!! generateHTML('LastUpdateOnLineTime','Latest online time',$isAsc, $orderBy) !!}
                            {!! generateHTML('DewiStatus','Dewi',$isAsc, $orderBy) !!}
                            {!! generateHTML('MinerVersion','Miner version',$isAsc, $orderBy) !!}
                            <th class="uk-width-1-10 uk-text-small">More</th>
                        </tr>
                    </thead>
                    @if($data->count() > 0)
                        @foreach ($data as $object)
                            <tr>
                                <td class="uk-text-small">{{ $object->DeviceSN }}</td>
                                <td class="uk-text-small">{{ $object->MacAddress }}</td>
                                <td class="uk-text-small">{{ $object->AnimalName }}</td>
                                <td class="uk-text-small">{{ $object->BlockHeight }}</td>
                                <td class="uk-text-small">
                                    @if($object->LastUpdateOnLineTime)
                                        <?php 
                                            $now = date_create( date('Y-m-d H:i:s',time() - (8 * 3600)));
                                            $LastUpdateOnLineTime = date_create( $object->LastUpdateOnLineTime);
                                            $time = date_diff($now, $LastUpdateOnLineTime);

                                            $minutes = $time->days * 24 * 60;
                                            $minutes += $time->h * 60;
                                            $minutes += $time->i;
                                            if($minutes >= 10){
                                                print('<span class="material-icons" style="color:#FF5959"> circle </span> offline');
                                            }else{
                                                print('<span class="material-icons" style="color:#59FF59"> circle </span> online');
                                            }
                                        ?>
                                    @else
                                        <span class="material-icons" style="color:#FF5959"> circle </span> offline
                                    @endif
                                </td>
                                <td class="uk-text-small">
                                    @if($object->P2P_Connected)
                                        @if($object->P2P_Connected == 1)
                                            <span class="material-icons" style="color:#59FF59"> circle </span>Yes
                                        @elseif($object->P2P_Connected == 0)
                                            <span class="material-icons" style="color:#FF5959"> circle </span>No
                                        @else
                                            <span class="material-icons" style="color:#ABABAB"> circle </span>
                                        @endif
                                    @else
                                        <span class="material-icons" style="color:#ABABAB"> circle </span>
                                    @endif
                                </td>
                                <td class="uk-text-small">
                                    @if($object->P2P_Dialable)
                                        @if($object->P2P_Dialable == 1)
                                            <span class="material-icons" style="color:#59FF59"> circle </span>Yes
                                        @elseif($object->P2P_Dialable == 0)
                                            <span class="material-icons" style="color:#FF5959"> circle </span>No
                                        @else
                                            <span class="material-icons" style="color:#ABABAB"> circle </span>
                                        @endif
                                    @else
                                        <span class="material-icons" style="color:#ABABAB"> circle </span>
                                    @endif
                                </td>
                                <td class="uk-text-small">
                                    @if($object->P2P_NatType)
                                        @if($object->P2P_NatType == 1)
                                            <span class="material-icons" style="color:#59FF59"> circle </span>Yes
                                        @elseif($object->P2P_NatType == 0)
                                            <span class="material-icons" style="color:#FF5959"> circle </span>No
                                        @else
                                            <span class="material-icons" style="color:#ABABAB"> circle </span>
                                        @endif
                                    @else
                                        <span class="material-icons" style="color:#ABABAB"> circle </span>
                                    @endif
                                </td>
                                <td class="uk-text-small">{{ $object->Region }}</td>

                                <td class="uk-text-small">
                                    @if($object->LastUpdateOnLineTime)
                                        {{ Carbon\Carbon::parse($object->LastUpdateOnLineTime)->format('Y-m-d H:i:s') }}
                                    @endif
                                </td>
                                <td class="uk-text-small">{{ $object->DewiStatus }}</td>
                                <td class="uk-text-small">{{ substr($object->MinerVersion, -15) }}</td>
                                <td class="uk-text-small">
                                    <button type="button" class="btn btn-secondary" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Bottom popover">
                                        Popover on bottom
                                    </button>
                                    <span class="material-icons userMOUSE" >more_vert</span>
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