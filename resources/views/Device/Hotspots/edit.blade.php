{{-- 載入主要的版型 --}}
@extends('layouts.master')

{{-- 額外增加所需要的css檔案 --}}
@section('extraCssArea')
    
@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')
    <script>
        function RegisteredDewi(mac){
            $.ajax({
                url: '/api/v1/RegisteredDewi',
                type: 'POST',
                async: false,
                headers: {
                    'Authorization': Cookies.get('authToken')
                },
                data : {
                    mac:mac
                },
                success: function(response) {
                    if(response.status == 0){
                        // alert('success!');
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
                                    // alert('success!');
                                    window.location.reload();
                                }else{
                                    alert(response.errorMessage);
                                }
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                console.log('error');
                            },
                        });
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
@if($Action == 'EDIT')
  @section('pageTitle', 'EDIT')
@else
  @section('pageTitle', 'Query')
@endif

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
    <h4 class="heading_a uk-margin-bottom">
        @if($Action == 'EDIT')
          Edit
        @else
          Query
        @endif
    </h4>
    @if (count($errors) > 0)
        <div class="alert alert-danger parsley-errors-list filled">
            <strong>您好</strong>，您輸入的資料有問題，請在確認後重新輸入！<br><br>
        </div>
    @endif
    {!! Form::model($data, ['method' => 'PATCH','route' => [ $routePath.'.update', $targetId]]) !!} 
        @include($viewPath.'.form')        
    {!! Form::close() !!}
 
    <script>
        function resetForm() {
            location.href = '{{ route($routePath.'.index') }}';
        }
    </script>    
@endsection