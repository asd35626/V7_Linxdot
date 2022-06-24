{{-- 載入主要的版型 --}}
@extends('layouts.master')

{{-- 額外增加所需要的css檔案 --}}
@section('extraCssArea')

@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')
    <style type="text/css">
        .mapboxgl-ctrl{
            display: none;
        }
        .marker {
            background-image: url('/favicon.ico');
            background-size: cover;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
        }
        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
        }
    </style>
@endsection


{{-- 設定視窗的標題 --}}
@section('title', $functionname)

{{-- 設定內容的主標題區 --}}
@section('pageTitle','IMPORT')

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
    <div class="uk-width-medium-1-3">
        <button class="md-btn" data-uk-modal="{target:'#modal_default'}" onclick="loding()">reboot</button>
        <div class="uk-modal" id="modal_default">
            <div class="uk-modal-dialog">
                <div class='uk-text-center'>Loding...<br/>
                    <img class='uk-margin-top' src='assets/img/spinners/spinner.gif' alt=''>
                </div>
            </div>
        </div>
    </div>
     <!-- <img  src='assets/img/spinners/spinner.gif' alt=''> -->

    {{-- <button class="md-btn" id="show_preloader_md">Show</button>
    <div class="uk-width-medium-1-4">
        <div class="md-preloader">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" height="48" width="48" viewbox="0 0 75 75">
                <circle cx="37.5" cy="37.5" r="33.5" stroke-width="4"/>
            </svg>
        </div>
    </div> --}}

    <button class="md-btn" onclick="loding()">reboot</button>

    <div class="uk-width-medium-1-4">
        <button type="button" class="md-btn" onclick="(function(modal){ modal = UIkit.modal.blockUI('<div class=\'uk-text-center\'>Loding...<br/><img class=\'uk-margin-top\' src=\'assets/img/spinners/spinner.gif\' alt=\'\'>');  })();">Block UI</button>
    </div>

<script>

    function loding(){
        var modal =  UIkit.modal.blockUI('<div class=\'uk-text-center\'>Loding...<br/><img class=\'uk-margin-top\' src=\'assets/img/spinners/spinner.gif\' alt=\'\'>');
        setTimeout(function(){ modal.hide() }, 5000);
    }
    
</script>

@endsection