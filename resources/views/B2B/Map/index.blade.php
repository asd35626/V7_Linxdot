{{-- 載入主要的版型 --}}
@extends('layouts.master')

@section('extraHeaderInfo')
    <louis></louis>
@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')
    <script>
    </script>
@endsection

{{-- 設定視窗的標題 --}}
@section('title', 'Hostposts')

{{-- 設定內容的主標題區 --}}
@section('pageTitle', 'Hotspots')

{{-- 設定內容的主標題區 --}}
@section('breadcrumbArea')
    <ul id="breadcrumbs">
        <li><span>Map</span></li>
    </ul>
@endsection

{{-- 設定內容 --}}
@section('content')
    {{-- <p>Welcome to Linxdot Admin！</p> --}}
    <embed src="https://linxdot-api.v7idea.com/worldmap/" width="100%" height="500">
@endsection