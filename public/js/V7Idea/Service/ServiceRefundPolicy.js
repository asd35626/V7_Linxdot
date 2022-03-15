$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
  options.async = true;
});


$(document).ready(function() {
  $('#add').show();
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
    loadDCID();
    //step 3 : load WebDocument
    loadWebDocument();
    //停用上一頁功能
    // backCheck();
  }
});

function sendAddData() {
  tinyMCE.triggerSave();
  if (
    ($('#addForm #addLongDescription').val() == "") ||
    ($('#addForm input:radio:checked[name="addIfValid"]').val() == undefined)
  ) {
    alert("沒有輸入必要資料");
  } else {
    var formData = new FormData($("#addForm")[0]);

    $.ajax({
      url: "/api/v1/WebDocument",
      type: 'POST',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == '1') {
          location.reload();
        }
      },
      error: function(xhr, ajaxOptions, thrownError) {
        switch (xhr.status) {
          case 422:
            alert('新增資料不完全');
            break;
          case 409:
            alert('型別編號已經存在');
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
  // alert('editLongDescription:' + $('#editLongDescription').val());

  var formData = $("#editForm").serialize();
  $.ajax({
    url: "/api/v1/WebDocument/" + $('#editId').val(),
    type: 'PUT',
    data: formData,
    async: false,
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.status == '1') {
        alert('更新完成, 即將載入新頁面');
        loadWebDocument();
      }
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
    cache: false
  });
  return false;
}

function resetEditData() {
  loadWebDocument();
}

function loadDCID() {
  $.ajax({
    url: "/api/v1/WebDocumentCategory",
    type: 'GET',
    data: {
      "searchTitle": "退款政策"
    },
    async: false,
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.data.length != 0) {
        $('#addDCID').val(response.data[0].id);
        $('#editDCID').val(response.data[0].id);
        $('#addDocumentTitle').val(response.data[0].CategoryTitle);
        $('#editDocumentTitle').val(response.data[0].CategoryTitle);
      } else {
        alert("請先至前台網站分類管理新增關於波力項目");
      }
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
    cache: false
  });
}

function loadWebDocument() {
  $.ajax({
    url: "/api/v1/WebDocument",
    type: 'GET',
    data: {
      "searchTitle": "退款政策"
    },
    async: false,
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.data.length != 0) {
        $('#editId').val(response.data[0].id);
        // $('#editLongDescription').val(response.data[0].LongDescription);
        $('#editCreateBy').val(response.data[0].CreateBy);
        $('#editCreateDate').val(response.data[0].CreateDate);
        //radio
        if (response.data[0].IfValid == 1) {
          $('#editForm input[name=editIfValid][value=1]').attr('checked', true);
        } else {
          $('#editForm input[name=editIfValid][value=-1]').attr('checked', true);
        }

        //tinymce.activeEditor.setContent(template)
        tinymce.activeEditor.setContent(response.data[0].LongDescription);
        $('#edit').show();
        $('#add').hide();
      } else {
        $('#edit').hide();
        $('#add').show();
      }
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
    cache: false
  });
}
