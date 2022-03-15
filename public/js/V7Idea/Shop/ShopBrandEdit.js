var THISAPI = "/api/v1/ShopBrand";


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

    //step 2 : 產生ContentArea
    editData($('#editTagID').val());

    //停用上一頁功能
    // backCheck();
  }
});


function returnToList() {
  window.history.back();
}

//show edit page
function editData(thisId) {
  //initial
  $('#list').hide();
  $('#edit').show();
  $('#add').hide();

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
        $('#editBrandName').val(response.data.BrandName);
        $('#editBrandCode').val(response.data.BrandCode);
        $('#editBrandShortDescription').val(response.data.BrandShortDescription);
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
        tinymce.get('editBrandDescription').setContent(response.data.BrandDescription);
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
  return false;
}


function resetEditData() {
  editData($('#editId').val());
}
