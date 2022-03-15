var THISAPI = "/api/v1/DimBannerMapping";
var CATEGORY_API = "/api/v1/DimBannerCategory";

$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
  options.async = true;
});


$(document).ready(function() {
  // $('#edit').hide();
  // $('#edit').hide();

  var id = Cookies.get('authToken');

  // console.log('id:'+id);
  if (id == undefined) {

    Cookies.remove('authToken');
    window.location.replace("/Admin/Login");
  } else {
    //step 1 : 產生MainMenu
    createMainMenu(id);

    // toAdd();

    //停用上一頁功能
    // backCheck();
    editData($('#editId').val());
  }
  //autocomplete
  searchCategory();

  //datepicker
  $('#editOnWebStartDate').datepicker({dateFormat: "yy-mm-dd"});
  $('#editOnWebEndDate').datepicker({dateFormat: "yy-mm-dd",minDate: 0});
});


//do list search
// function search() {
//   ajaxTable.ajax.reload();
// }

// function toAdd() {
//   $('#list').hide();
//   $('#edit').hide();
//   $('#edit').show();
// }

function returnToList() {
  window.history.back();
}

function sendEditData() {
  var isSuccess = true;
  var errorMessage = '';

  //Check editBCID
  if(isSuccess && $('#editForm #editBCID').val() == ""){
    errorMessage = '版位名稱沒有輸入';
    isSuccess = false;

    $('#editForm #searchCategory').focus();
  }

  //Check addOnWebStartDate
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

  //Check addOnWebStartDate
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

  //Check addSerialNo
  if(isSuccess && $('#editForm #editSerialNo').val() == ""){
    errorMessage = 'Banner排序沒有輸入';
    isSuccess = false;

    $('#editForm #editSerialNo').focus();
  }



  // if(isSuccess){
  //   if(
  //     $('#editForm input:radio:checked[name="addIfShow"]').val() == undefined ||
  //     $('#editForm input:radio:checked[name="addIfShow"]').val() == ''
  //   ) {
  //     errorMessage = '前台呈現沒有選擇';
  //     isSuccess = false;
  //
  //     $('#editForm input[name=addIfShow]').eq(0).focus();
  //   }
  // }

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

  if (!isSuccess) {
    alert(errorMessage);
  } else {
    // tinyMCE.triggerSave();
    var formData = new FormData($("#editForm")[0]);

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
            alert('重複上架相同版位, 請再確認您要上架的版位名稱');
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
          // $('#editProductBrandID').val('');
      },
      select: function(event, ui) {
          $('#searchCategory').val(ui.item.value);
          $('#editBCID').val(ui.item.id);
      }
  });
}

//pass return true
function validateDate(inputDate){
 console.log(inputDate);
 var m = moment(inputDate.replace('/', '-'), 'YYYY-MM-DD', true);// format
 return m.isValid();
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
        $('#searchCategory').val(response.data.CategoryName);
        $('#editBCID').val(response.data.BCID);
        $('#editOnWebStartDate').val(response.data.OnWebStartDate);
        $('#editOnWebEndDate').val(response.data.OnWebEndDate);
        $('#editSerialNo').val(response.data.SerialNo);

        $('#editCreateBy').val(response.data.CreateBy);
        $('#editCreateDate').val(response.data.CreateDate);
        // if (response.data.IfShow == 1) {
        //   $('#edit input[name=editIfShow][value=1]').attr('checked', true);
        // } else {
        //   $('#edit input[name=editIfShow][value=0]').attr('checked', true);
        // }
        if (response.data.IfValid == 1) {
          $('#edit input[name=editIfValid][value=1]').attr('checked', true);
        } else {
          $('#edit input[name=editIfValid][value=0]').attr('checked', true);
        }
      }
    },
    error: function(xhr, ajaxOptions, thrownError) {

    },
    cache: false,
    contentType: false,
    processData: false
  });
}
