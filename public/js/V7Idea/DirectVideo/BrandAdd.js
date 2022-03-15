var THISAPI = "/api/v1/WebDirectVideo";
var CATEGORY_API = "/api/v1/WebDirectVideoCategory";

$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
  options.async = true;
});


$(document).ready(function() {
  $('#add').hide();
  $('#edit').hide();

  var id = Cookies.get('authToken');

  // console.log('id:'+id);
  if (id == undefined) {

    Cookies.remove('authToken');
    window.location.replace("/Admin/Login");
  } else {
    //step 1 : 產生MainMenu
    createMainMenu(id);

    toAdd();

    //停用上一頁功能
    // backCheck();
  }
  //autocomplete
  searchCategory();

  //datepicker
  $('#addOnWebStartDate').datepicker({dateFormat: "yy-mm-dd"});
  $('#addOnWebEndDate').datepicker({dateFormat: "yy-mm-dd",minDate: 0});
});


//do list search
function search() {
  ajaxTable.ajax.reload();
}

function toAdd() {
  $('#list').hide();
  $('#edit').hide();
  $('#add').show();
}

function returnToList() {
  window.history.back();
}


function sendAddData() {
  var isSuccess = true;
  var errorMessage = '';

  //Check addBrandName
  if(isSuccess && $('#addForm #addBrandName').val() == ""){
    errorMessage = '節目名稱沒有輸入';
    isSuccess = false;

    $('#addForm #addBrandName').focus();
  }

  //Check addBrandCode
  if(isSuccess && $('#addForm #addBrandCode').val() == ""){
    errorMessage = '節目代碼沒有輸入';
    isSuccess = false;

    $('#addForm #addBrandCode').focus();
  }

  //Check addDVCID
  if(isSuccess && $('#addForm #addDVCID').val() == ""){
    errorMessage = '節目分類沒有輸入';
    isSuccess = false;

    $('#addForm #searchCategory').focus();
  }

  //Check addOnWebStartDate
  if(isSuccess){
    if($('#addForm #addOnWebStartDate').val() == ""){
      errorMessage = '上架日期沒有輸入';
      isSuccess = false;

      $('#addForm #addOnWebStartDate').focus();
    }else{
      //check date valid
      isSuccess = validateDate($('#addForm #addOnWebStartDate').val());//pass return true
      if(isSuccess) console.log('OnWebStartDate pass');
      else{
          errorMessage = '上架日期請輸入正確的日期格式(YYYY-MM-DD)';
          $('#addForm #addOnWebStartDate').focus();
      }
    }
  }

  //Check addOnWebStartDate
  if(isSuccess){
    if($('#addForm #addOnWebEndDate').val() == ""){
      errorMessage = '下架日期沒有輸入';
      isSuccess = false;

      $('#addForm #addOnWebEndDate').focus();
    }else{
      //check date valid
      isSuccess = validateDate($('#addForm #addOnWebEndDate').val());//pass return true
      if(isSuccess) console.log('OnWebStartDate pass');
      else{
          errorMessage = '下架日期請輸入正確的日期格式(YYYY-MM-DD)';
          $('#addForm #addOnWebEndDate').focus();
      }
    }
  }

  //Check addContentURL
  if(isSuccess && $('#addForm #addContentURL').val() == ""){
    errorMessage = '節目內容連結沒有輸入';
    isSuccess = false;

    $('#addForm #addContentURL').focus();
  }

  //Check addBrandShortDescription
  if(isSuccess && $('#addForm #addBrandShortDescription').val() == ""){
    errorMessage = '節目敘述沒有輸入';
    isSuccess = false;

    $('#addForm #addBrandShortDescription').focus();
  }

  if(isSuccess){
    if(
      $('#addForm input:radio:checked[name="addIfShow"]').val() == undefined ||
      $('#addForm input:radio:checked[name="addIfShow"]').val() == ''
    ) {
      errorMessage = '前台呈現沒有選擇';
      isSuccess = false;

      $('#addForm input[name=addIfShow]').eq(0).focus();
    }
  }

  if(isSuccess){
    if(
      $('#addForm input:radio:checked[name="addIfValid"]').val() == undefined ||
      $('#addForm input:radio:checked[name="addIfValid"]').val() == ''
    ) {
      errorMessage = '啟用/停用沒有選擇';
      isSuccess = false;

      $('#addForm input[name=addIfValid]').eq(0).focus();
    }
  }

  if (!isSuccess) {
    alert(errorMessage);
  } else {
    tinyMCE.triggerSave();
    var formData = new FormData($("#addForm")[0]);

    $.ajax({
      url: THISAPI,
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
            alert('新增資料不完全');
            break;
          case 409:
            alert('編號已經存在');
            break;
          case 401:
            alert('沒有存取權限');
            window.location.replace("/Admin/Login");
            break;
          default:
            alert('無法新增資料');
        }
      },
      cache: false,
      contentType: false,
      processData: false
    });
  }
  return false;
}

function sendEditData() {
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
      $("#edit").hide();

      ajaxTable.ajax.reload();

      $("#list").show();
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
  return false;
}

function deleteData(thisId) {
  if (confirm("確定要刪除嗎?")) {
    $.ajax({
      url: THISAPI + "/" + thisId,
      type: 'DELETE',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {
          ajaxTable.ajax.reload();
        }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        // window.location.replace("/Admin/Login");
        // 			alert(xhr.status);
        // 			alert(xhr.statusText);
        // 			alert(xhr.responseText);
      },
      cache: false,
      contentType: false,
      processData: false
    });
  }
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
          $('#addDVCID').val(ui.item.id);
      }
  });
}

//pass return true
function validateDate(inputDate){
 console.log(inputDate);
 var m = moment(inputDate.replace('/', '-'), 'YYYY-MM-DD', true);// format
 return m.isValid();
}
