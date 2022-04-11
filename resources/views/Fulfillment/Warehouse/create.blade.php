
{{-- 載入主要的版型 --}}
@extends('layouts.master')

{{-- 額外增加所需要的css檔案 --}}
@section('extraCssArea')
<style type="text/css">
    .stream_title{
        top: -6px;
        font-size: 12px;
        color: #727272;
        position: relative;
        left: 4px;
    }
</style>    
@endsection

{{-- 增加所需要的Script; 將會放置在主板型的後面 --}}
@section('scriptArea')
  <script>
    function getUserDegreeList(UserType, selectListId){
      $.ajax({
        url: '/api/v1/GetUserDegreeList',
        type: 'POST',
        async: false,
        headers: {
          'Authorization': Cookies.get('authToken')
        },
        data : {
          'UserType' : UserType
        },
        success: function(data) {
          console.log('data.status:'+data.status);
          if(data.status == 1){
            $(selectListId).empty().append($('<option>', {
                value: '',
                text: '請選擇'
              }));

            $.each(data.data, function(k, v) {
                console.log('option:'+k+':'+v);
              $(selectListId).append($('<option>', {
                value: k,
                text: v
              }));
            });
          }
          // $(selectListId).attr('disabled', false);
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert('error');
        },
        cache: false
      });
    }

    $(document).ready(function(){
        $("label[for='UserType']").addClass( "stream_title" );
        $("label[for='DegreeId']").addClass( "stream_title" );
        $('#UserType').on('change', function(){
          console.log('UserType:'+$('#UserType').val());
          if($('#UserType').val() == ''){
            if($('#DegreeId').val() != ''){
              //reset degree
              $('#DegreeId').empty().append($('<option>', {
                  value: '',
                  text: '請選擇'
                }));
            }
          }else{
            getUserDegreeList($('#UserType').val(),'#DegreeId');
          }
        });
    });
    // function GetFromUser(UID,selectListId){
    //     console.log('123'+UID);
    //   $.ajax({
    //     url: '/api/v1/GetFromUser',
    //     type: 'POST',
    //     async: false,
    //     headers: {
    //       'Authorization': Cookies.get('authToken')
    //     },
    //     data : {
    //       'UID' : UID
    //     },
    //     success: function(data) {
    //       console.log('data.status:'+data.status);
    //       if(data.status == 1){
    //         $(selectListId).empty().append($('<option>', {
    //             value: '',
    //             text: '請選擇'
    //         }));

    //         $.each(data.data, function(k, v) {
    //             console.log('option:'+k+':'+v);
    //           $(selectListId).append($('<option>', {
    //             value: k,
    //             text: v
    //           }));
    //         });
    //       }
    //       // $(selectListId).attr('disabled', false);
    //     },
    //     error: function(xhr, ajaxOptions, thrownError) {
    //       alert('error');
    //     },
    //     cache: false
    //   });
    // }
    // // 總代經紀顯示/隱藏
    // $(document).ready(function(e){
    //     var UserType = $('#UserType').find('option:selected').val();
    //     var DegreeId = $('#DegreeId').find('option:selected').val();
    //     // alert(type);
    //     if(UserType == 20 && DegreeId == 10){
    //         $('#FromUID').show();
    //         $('#FromUID2').hide();
    //     }else if(UserType == 20 && DegreeId == 5){
    //         $('#FromUID').show();
    //         $('#FromUID2').show();
    //     }else if(UserType == 20 && DegreeId == 19){
    //         $('#FromUID').show();
    //         $('#FromUID2').hide();
    //     }else{
    //         $('#FromUID').hide();
    //         $('#FromUID2').hide();
    //     }
    //     $('#DegreeId').on('change', function(){
    //         var UserType = $('#UserType').find('option:selected').val();
    //         var DegreeId = $('#DegreeId').find('option:selected').val();
    //         // alert(type);
    //         if(UserType == 20 && DegreeId == 10){
    //             $('#FromUID').show();
    //             $('#FromUID2').hide();
    //         }else if(UserType == 20 && DegreeId == 5){
    //             $('#FromUID').show();
    //             $('#FromUID2').show();
    //         }else if(UserType == 20 && DegreeId == 19){
    //             $('#FromUID').show();
    //             $('#FromUID2').hide();
    //         }else{
    //             $('#FromUID').hide();
    //             $('#FromUID2').hide();
    //         }
    //     });
    //     $('#UserType').on('change', function(){
    //         var UserType = $('#UserType').find('option:selected').val();
    //         var DegreeId = $('#DegreeId').find('option:selected').val();
    //         // alert(type);
    //         if(UserType == 20 && DegreeId == 10){
    //             $('#FromUID').show();
    //             $('#FromUID2').hide();
    //         }else if(UserType == 20 && DegreeId == 5){
    //             $('#FromUID').show();
    //             $('#FromUID2').show();
    //         }else if(UserType == 20 && DegreeId == 19){
    //             $('#FromUID').show();
    //             $('#FromUID2').hide();
    //         }else{
    //             $('#FromUID').hide();
    //             $('#FromUID2').hide();
    //         }
    //     });
    //     $('#FromUserID').on('change', function(){
    //         if($('#FromUserID').val() == ''){
    //                 $('#FromUser').empty().append($('<option>', {
    //                     value: '',
    //                     text: '請選擇'
    //                 }));
    //         }else{
    //             GetFromUser($('#FromUserID').val(),'#FromUser');
    //         }
    //     });
    // });

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
        <li><a href="/System/UserSetting/AccountManagement">{{ $functionname }}</a></li>
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
    {!! Form::open(array('route' => $routePath.'.store','method'=>'POST','autocomplete' => 'off' , 'role' => 'presentation' , 'class'=> 'class="uk-form-stacked"', 'id'=> 'form_validation')) !!}
           @include($viewPath.'.form')  
    {!! Form::close() !!}

    <script>
        function resetForm() {
            location.href = '{{ route($routePath.'.index') }}';
        }
    </script>    
@endsection