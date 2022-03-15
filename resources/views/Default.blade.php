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
@section('title', 'HOME')

{{-- 設定內容的主標題區 --}}
@section('pageTitle', '')

{{-- 設定內容的主標題區 --}}
@section('breadcrumbArea')
    
@endsection

{{-- 設定內容 --}}
@section('content')
    <p>歡迎進入本系統！</p>
@endsection