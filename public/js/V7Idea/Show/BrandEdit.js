var THISAPI = "/api/v1/WebShow";
var CATEGORY_API = "/api/v1/WebShowCategory";

$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
  options.async = true;
});


$(document).ready(function() {

  var id = Cookies.get('authToken');

  // console.log('id:'+id);
  if (id == undefined) {

    Cookies.remove('authToken');
    window.location.replace("/Admin/Login");
  } else {
    //step 1 : 產生MainMenu
    createMainMenu(id);

    //step 2 : 產生ContentArea
    editData($('#editTagID').val());

    //停用上一頁功能
    // backCheck();
  }
  //autocomplete
  searchCategory();

  //datepicker
  $('#editOnWebStartDate').datepicker({dateFormat: "yy-mm-dd"});
  $('#editOnWebEndDate').datepicker({dateFormat: "yy-mm-dd",minDate: 0});
});


function returnToList() {
  window.history.back();
}

//show edit page
function editData(thisId) {
  //initial
  //get detail data
  $.ajax({
    url: THISAPI + "/" + thisId,
    type: 'GET',
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.status == 1) {
        $('#editId').val(response.data.id);
        $('#editBrandName').val(response.data.DocumentName);
        $('#editBrandCode').val(response.data.DocumentCode);
        $('#editContentURL').val(response.data.ContentURL);
        $('#editBrandShortDescription').val(response.data.ShortDescription);
        $('#editSCID').val(response.data.SCID);
        $('#searchCategory').val(response.data.CategoryName);
        $('#editOnWebEndDate').val(response.data.OnWebEndDate);
        $('#editOnWebStartDate').val(response.data.OnWebStartDate);
        //上傳圖片

        if(response.data.SmallIconPath) $('#editShowSmallIcon').prepend('<img src="/Service/' + response.data.SmallIconPath + '" />');
        if(response.data.BigIconPath) $('#editShowBigIcon').prepend('<img src="/Service/' + response.data.BigIconPath + '" />');


        $('#editCreateBy').val(response.data.CreateBy);
        $('#editCreateDate').val(response.data.CreateDate);
        if (response.data.IfShow == 1) {
          $('#edit input[name=editIfShow][value=1]').attr('checked', true);
        } else {
          $('#edit input[name=editIfShow][value=0]').attr('checked', true);
        }
        if (response.data.IfValid == 1) {
          $('#edit input[name=editIfValid][value=1]').attr('checked', true);
        } else {
          $('#edit input[name=editIfValid][value=0]').attr('checked', true);
        }
        //
        tinymce.get('editBrandDescription').setContent(response.data.LongDescription);
        //
      }
    },
    error: function(xhr, ajaxOptions, thrownError) {

    },
    cache: false,
    contentType: false,
    processData: false
  });
}


function sendEditData() {

  var isSuccess = true;
  var errorMessage = '';

  //Check editBrandName
  if(isSuccess && $('#editForm #editBrandName').val() == ""){
    errorMessage = '節目名稱沒有輸入';
    isSuccess = false;

    $('#editForm #editBrandName').focus();
  }

  //Check editBrandCode
  if(isSuccess && $('#editForm #editBrandCode').val() == ""){
    errorMessage = '節目代碼沒有輸入';
    isSuccess = false;

    $('#editForm #editBrandCode').focus();
  }

  //Check editSCID
  if(isSuccess && $('#editForm #editSCID').val() == ""){
    errorMessage = '節目分類沒有輸入';
    isSuccess = false;

    $('#editForm #searchCategory').focus();
  }

  //Check editOnWebStartDate
  if(isSuccess){
    if($('#editForm #editOnWebStartDate').val() == ""){
      errorMessage = '上架日期沒有輸入';
      isSuccess = false;

      $('#editForm #editOnWebStartDate').focus();
    }else{
      //check date valid
      isSuccess = validateDate($('#editForm #editOnWebStartDate').val());//pass return true
      if(isSuccess) console.log('OnWebStartDate pass');
      else{
          errorMessage = '上架日期請輸入正確的日期格式(YYYY-MM-DD)';
          $('#editForm #editOnWebStartDate').focus();
      }
    }
  }

  //Check editOnWebStartDate
  if(isSuccess){
    if($('#editForm #editOnWebEndDate').val() == ""){
      errorMessage = '下架日期沒有輸入';
      isSuccess = false;

      $('#editForm #editOnWebEndDate').focus();
    }else{
      //check date valid
      isSuccess = validateDate($('#editForm #editOnWebEndDate').val());//pass return true
      if(isSuccess) console.log('OnWebStartDate pass');
      else{
          errorMessage = '下架日期請輸入正確的日期格式(YYYY-MM-DD)';
          $('#editForm #editOnWebEndDate').focus();
      }
    }
  }

  //Check editContentURL
  if(isSuccess && $('#editForm #editContentURL').val() == ""){
    errorMessage = '節目內容連結沒有輸入';
    isSuccess = false;

    $('#editForm #editContentURL').focus();
  }

  //Check editBrandShortDescription
  if(isSuccess && $('#editForm #editBrandShortDescription').val() == ""){
    errorMessage = '節目敘述沒有輸入';
    isSuccess = false;

    $('#editForm #editBrandShortDescription').focus();
  }

  if(isSuccess){
    if(
      $('#editForm input:radio:checked[name="editIfShow"]').val() == undefined ||
      $('#editForm input:radio:checked[name="editIfShow"]').val() == ''
    ) {
      errorMessage = '前台呈現沒有選擇';
      isSuccess = false;

      $('#editForm input[name=editIfShow]').eq(0).focus();
    }
  }

  if(isSuccess){
    if(
      $('#editForm input:radio:checked[name="editIfValid"]').val() == undefined ||
      $('#editForm input:radio:checked[name="editIfValid"]').val() == ''
    ) {
      errorMessage = '啟用/停用沒有選擇';
      isSuccess = false;

      $('#editForm input[name=editIfValid]').eq(0).focus();
    }
  }

  if(isSuccess){
    tinyMCE.triggerSave();

    // var formData = $("#editForm").serialize();
    var formData = new FormData($("#editForm")[0]);


    $.ajax({
      url: THISAPI + "/" + $('#editId').val(),
      type: 'POST',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        returnToList();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        switch (xhr.status) {
          case 422:
            alert('修改資料不完全');
            break;
          case 409:
            alert('名稱重複');
            break;
          case 401:
            alert('Token 不存在');
            window.location.replace("/Admin/Login");
            break;
          default:
            alert('無法修改資料');
        }
      },
      cache: false,
      contentType: false,
      processData: false
    });
  }else{
    alert(errorMessage);
  }

  return false;
}


function resetEditData() {
  editData($('#editId').val());
}

function searchCategory(){
  $('#searchCategory').autocomplete({
      source: function(request, response) {
        console.log('request.term:'+request.term);
          $.ajax({
              url: CATEGORY_API + "?length=-1",
              type: 'GET',
              async: false,
              headers: {
                  'Authorization': Cookies.get('authToken')
              },
              data: {
                  searchAutoComplete: request.term
              },
              success: function(output) {
                  var outArr = new Array();
                  $.each(output.data, function(i, item) {
                    var tempArr = {
                        id: item.id,
                        label: item.CategoryName,
                        value: item.CategoryName
                    };
                    outArr.push(tempArr);
                  });

                  response(outArr);
              }
          });
      },
      minLength: 1,
      change: function(event, ui) {
          // $('#addProductBrandID').val('');
      },
      select: function(event, ui) {
          $('#searchCategory').val(ui.item.value);
          $('#editSCID').val(ui.item.id);
      }
  });
}

//pass return true
function validateDate(inputDate){
 console.log(inputDate);
 var m = moment(inputDate.replace('/', '-'), 'YYYY-MM-DD', true);// format
 return m.isValid();
}
