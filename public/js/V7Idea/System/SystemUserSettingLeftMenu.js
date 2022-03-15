var adminSubFunctionTable;
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
    adminSubFunctionTable = listAdminSubFunctions();

    //停用上一頁功能
    // backCheck();
  }
});

//get listAdminSubFunctions table
function listAdminSubFunctions() {
  var table = $('#grid-basic').DataTable({
    "processing": true,
    "serverSide": true,
    "language": {
      "lengthMenu": "顯示 _MENU_ 筆",
      "zeroRecords": "無資料",
      "info": "第 _START_ ~ _END_ 筆共 _TOTAL_ 筆",
      "search": "搜尋:",
      "processing": "處理中...",
      "paginate": {
        "first": "First",
        "last": "Last",
        "next": "下一頁",
        "previous": "上一頁"
      }
    },
    "ajax": {
      "url": "/api/v1/AdminFunction",
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.searchType = "subList";
        d.functionName = $('#functionName').val();
        d.functionURL = $('#functionURL').val();
        d.functionIfValid = $('#functionIfValid').val();
      }
    },
    "columns": [{
      "data": "FunctionName"
    }, {
      "data": "MenuOrder"
    }, {
      "data": "ParentFunctionName"
    }, {
      "data": "ParentFunctionMenuOrder"
    }, {
      "data": "FunctionURL"
    }, {
      "data": "IfValid"
    }, {
      "data": "CreateDate"
    }, {
      "width": "5%"
    }, {
      "width": "5%"
    }],
    "columnDefs": [{
      "targets": 7,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-primary\" onclick=\"editData('" + row.id + "');\">修改</div>";
      }
    }, {
      "targets": 8,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-danger\" onclick=\"deleteData('" + row.id + "');\">刪除</div>";
      }
    }],
    "bSort": false, //排序
    "bFilter": false, //搜尋
    "lengthChange": false, //每頁筆數調整
  });

  return table;
}

//do list search
function search() {
  adminSubFunctionTable.ajax.reload();
}

function toAdd() {
  $('#list').hide();
  $('#edit').hide();
  $('#add').show();
  loadMainFunctionList('#addParentFunctionId');
}

function returnToList() {
  $('#list').show();
  $('#edit').hide();
  $('#add').hide();
}

//show edit page
function editData(id) {
  //initial
  $('#list').hide();
  $('#edit').show();
  $('#add').hide();
  //load select mainlist
  loadMainFunctionList('#editForm #editParentFunctionId');
  //get detail data
  $.ajax({
    url: "/api/v1/AdminFunction/" + id,
    type: 'GET',
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.status == 1) {
        $('#editForm #editId').val(response.data.id);
        $('#editForm #editFunctionName').val(response.data.FunctionName);
        $('#editForm #editFunctionURL').val(response.data.FunctionURL);
        $('#editForm #editFunctionDesc').val(response.data.FunctionDesc);
        $('#editForm #editMenuOrder').val(response.data.MenuOrder);
        $('#editForm #editCreateBy').val(response.data.CreateBy);
        $('#editForm #editCreateDate').val(response.data.CreateDate);
        //radio
        if (response.data.IfValid == 1) {
          $('#editForm input[name=editIfValid][value=1]').attr('checked', true);
        } else {
          $('#editForm input[name=editIfValid][value=-1]').attr('checked', true);
        }
        //selectList
        $('#editForm #editParentFunctionId').val(response.data.ParentFunctionId);
      }
    },
    error: function(xhr, ajaxOptions, thrownError) {

    },
    cache: false,
    contentType: false,
    processData: false
  });
}

function deleteData(id) {
  if (confirm("確定要刪除嗎?")) {
    $.ajax({
      url: "/api/v1/AdminFunction/" + id,
      type: 'DELETE',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {
          adminSubFunctionTable.ajax.reload();
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

function sendAddData() {
  if (
    ($('#addForm #addFunctionName').val() == "") ||
    ($('#addForm #addFunctionURL').val() == "") ||
    ($('#addForm #addMenuOrder').val() == "") ||
    ($('#addForm input:radio:checked[name="addIfValid"]').val() == undefined) ||
    ($('#addForm #addParentFunctionId').val() == "")
  ) {
    alert("沒有輸入必要資料");
  } else {
    var formData = new FormData($("#addForm")[0]);

    $.ajax({
      url: "/api/v1/AdminFunction",
      type: 'POST',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        $("#add").hide();

        adminSubFunctionTable.ajax.reload();

        $("#list").show();
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
  if (
    ($('#editForm #addFunctionURL').val() == "") ||
    ($('#editForm #addMenuOrder').val() == "") ||
    ($('#editForm input:radio:checked[name="editIfValid"]').val() == undefined) ||
    ($('#editForm #editParentFunctionId').val() == "")
  ) {
    alert("沒有輸入必要資料");
  } else {
    var formData = $("#editForm").serialize();
    $.ajax({
      url: "/api/v1/AdminFunction/" + $('#editId').val(),
      type: 'PUT',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        $("#edit").hide();

        adminSubFunctionTable.ajax.reload();

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
      cache: false
    });
  }

  return false;
}

function resetEditData() {
  editData($('#editId').val());
}

function loadMainFunctionList(selectListId) {
  $.ajax({
    url: "/api/v1/AdminFunction?serchType=mainList&length=-1",
    type: 'GET',
    async: false,
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      $(selectListId).html('<option value="">請選擇</option>'); //reset select list
      $.each(response.data, function(index, obj) {
        // console.log('id:'+obj.id);
        // console.log('name'+ obj.FunctionName);
        if ((obj.IfValid == 1) && (obj.IfDelete == 0)) {
          $(selectListId).append($('<option>', {
            value: obj.id,
            text: obj.FunctionName
          }));
        }
      });
    },
    error: function(xhr, ajaxOptions, thrownError) {
      switch (xhr.status) {
        case 401:
          alert('Token 不存在');
          window.location.replace("/Admin/Login");
          break;
        default:
          alert('資料存取出現異常');
      }
    },
    cache: false
  });
}
