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
    <script src="assets/js/common.min.js"></script>
    <!-- uikit functions -->
    <script src="assets/js/uikit_custom.min.js"></script>
    <!-- htmleditor (codeMirror) -->
    <script src="assets/js/uikit_htmleditor_custom.min.js"></script>
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
    <textarea data-uk-htmleditor="{ maxsplitsize:1220, codemirror : { mode: 'text/html' } }"></textarea>    
@endsection