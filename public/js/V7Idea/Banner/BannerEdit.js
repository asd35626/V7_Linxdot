var THISAPI = "/api/v1/DimBanner";
var CATEGORY_API = "/api/v1/DimBannerCategory";

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

    // toAdd();

    //停用上一頁功能
    // backCheck();
    // console.log($('#editTagID').val());
    editData($('#editTagID').val());
  }
  //autocomplete
  // searchCategory();

  //datepicker
  // $('#editOnWebStartDate').datepicker({dateFormat: "yy-mm-dd"});
  // $('#editOnWebEndDate').datepicker({dateFormat: "yy-mm-dd",minDate: 0});
  $('#editBannerType').on('change',function(){
    console.log($(this).val());
    if($(this).val() == '1'){
      $('#movieArea').show();
    }else{
      $('#movieArea').hide();
    }
  });
});


function returnToList() {
  window.history.back();
}


function sendEditData() {
  var isSuccess = true;
  var errorMessage = '';

  //Check addBannerName
  if(isSuccess && $('#editForm #editBannerName').val() == ""){
    errorMessage = 'Banner名稱沒有輸入';
    isSuccess = false;

    $('#editForm #editBannerName').focus();
  }

  //Check addBannerType
  if(isSuccess){
    if($('#editForm #editBannerType').val() == ""){
      errorMessage = 'Banner類型';
      isSuccess = false;

      $('#editForm #editBannerType').focus();
    }else{
      if($('#editForm #editBannerType').val() == "1"){
        //check 影音連結
        if($('#editForm #editBannerMovieURL').val() == ""){
          errorMessage = '請輸入影音連結';
          $('#editForm #editBannerMovieURL').focus();
          isSuccess = false;
        }else{
          console.log('MovieURL pass');
        }
      }
    }
  }

  //Check addBannerLink
  if(isSuccess && $('#editForm #editBannerLink').val() == ""){
    errorMessage = 'Banner連結沒有輸入';
    isSuccess = false;

    $('#editForm #editBannerLink').focus();
  }

  //Check addBannerDescription
  if(isSuccess && $('#editForm #editBannerDescription').val() == ""){
    errorMessage = 'Banner敘述';
    isSuccess = false;

    $('#editForm #editBannerDescription').focus();
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
    // var formData = $("#editForm").serialize();
    var formData = new FormData($("#editForm")[0]);


    $.ajax({
      url: THISAPI + "/" + $('#editTagID').val(),
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
  editData($('#editTagID').val());
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
          // $('#editProductBannerID').val('');
      },
      select: function(event, ui) {
          $('#searchCategory').val(ui.item.value);
          $('#editBCID').val(ui.item.id);
      }
  });
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
        $('#editBannerName').val(response.data.BannerName);
        $('#editBannerType').val(response.data.BannerType);
        if(response.data.BannerType == 1){
          $('#editBannerMovieURL').val(response.data.BannerLink);
          $('#movieArea').show();
        }
        $('#editBannerLink').val(response.data.BannerLink);
        $('#editBannerDescription').val(response.data.BannerDescription);

        if(response.data.BannerPhotoPath){
          $('#editShowBannerPhotoPath').prepend('<img src="/Service/' + response.data.BannerPhotoPath + '" />');
        }else{
          $('#editBannerPhotoPath').empty();
        }

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

//pass return true
function validateDate(inputDate){
 console.log(inputDate);
 var m = moment(inputDate.replace('/', '-'), 'YYYY-MM-DD', true);// format
 return m.isValid();
}
