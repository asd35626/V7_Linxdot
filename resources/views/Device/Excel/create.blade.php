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
@section('pageTitle', $functionname)

{{-- 設定內容的主標題區 --}}
@section('breadcrumbArea')
    <ul id="breadcrumbs">
        <li><a href="/Default">Home</a></li>
        <li><a href="/Device/Hotspots">{!! $functionname !!}</a></li>
        <li><span>新增</span></li>
    </ul>
@endsection

{{-- 設定內容 --}}
@section('content')
    <h4 class="heading_a uk-margin-bottom">新增</h4>
    @if (count($errors) > 0)
        <div class="alert alert-danger parsley-errors-list filled">
            <strong>您好</strong>，您輸入的資料有問題，請在確認後重新輸入！<br><br>
        </div>
    @endif
    {!! Form::open(array('route' => $routePath.'.store','method'=>'POST','autocomplete' => 'off' , 'role' => 'presentation' , 'class'=> 'class="uk-form-stacked"', 'id'=> 'form_validation','enctype'=>'multipart/form-data')) !!}
        @include($viewPath.'.form')  
    {!! Form::close() !!}

    <script>
        function resetForm() {
            location.href = '{{ route($routePath.'.index') }}';
        }
    </script>    
@endsection