var shopTagTable;
$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
  options.async = true;
});


$(document).ready(function() {
  $('#add').hide();
  $('#edit').hide();
  $('#list').show();
  var id = Cookies.get('authToken');

  // console.log('id:'+id);
  if (id == undefined) {

    Cookies.remove('authToken');
    window.location.replace("/Admin/Login");
  } else {
    //step 1 : 產生MainMenu
    createMainMenu(id);

    //step 2 : 產生ContentArea
    shopTagTable = listShopTags();

    //停用上一頁功能
    // backCheck();
  }
});

//get listShopTags table
function listShopTags() {
  var table = $('#grid-basic').DataTable({
    "processing": true,
    "serverSide": true,
    "autoWidth": false,
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
      "url": "/api/v1/ShopTag",
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.searchTagName = $('#searchTagName').val();
        d.searchTagCode = $('#searchTagCode').val();
      }
    },
    "columns": [{
      "data": "TagName",
    }, {
      "data": "TagCode",
    }, {
      "data": "TagDescription",
    }, {
      "data": "TagColor",
    }, {
      "data": "IfValid",
    }, {
      "data": "CreateDate",
      // title: "CreateDate",
    }, {
      width: '5%'
    }, {
      width: '5%'
    }],
    "columnDefs": [{
      "targets": 4,
      "render": function(data, type, row) {
        if (row.IfValid == 1) return "啟用";
        else return "停用";
      }
    }, {
      "targets": 6,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-primary\" onclick=\"editData('" + row.id + "');\">修改</div>";
      }
    }, {
      "targets": 7,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-danger\" onclick=\"deleteData('" + row.id + "');\">刪除</div>";
      }
    }],
    "bSort": false, //排序
    "bFilter": false, //搜尋
    "lengthChange": false,
    "fnRowCallback": function(nRow, aData, iDisplayIndex) {
      $('td', nRow).attr('nowrap', 'nowrap');
      return nRow;
    },
  });

  return table;
}

//do list search
function search() {
  shopTagTable.ajax.reload();
}

function toAdd() {
  // $('#list').hide();
  // $('#edit').hide();
  // $('#add').show();
  window.location = "/Shop/TagAdd";
}

function returnToList() {
  $('#list').show();
  $('#edit').hide();
  $('#add').hide();
}

//show edit page
function editData(thisId) {
  window.location = "/Shop/TagEdit/" + thisId;
}

function deleteData(thisId) {
  if (confirm("確定要刪除嗎?")) {
    $.ajax({
      url: "/api/v1/ShopTag/" + thisId,
      type: 'DELETE',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {
          shopTagTable.ajax.reload();
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
    ($('#addForm #addTagName').val() == "") ||
    ($('#addForm #addTagCode').val() == "") ||
    ($('#addForm #addTagColor').val() == "") ||
    ($('#addForm input:radio:checked[name="addIfValid"]').val() == undefined)
  ) {
    alert("沒有輸入必要資料");
  } else {

    var formData = new FormData($("#addForm")[0]);

    $.ajax({
      url: "/api/v1/ShopTag/",
      type: 'POST',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        $("#add").hide();

        shopTagTable.ajax.reload();

        $("#list").show();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        switch (xhr.status) {
          case 422:
            alert('新增資料不完全');
            break;
          case 409:
            alert('標籤已經存在');
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
    ($('#editForm #editTagName').val() == "") ||
    ($('#editForm #editTagCode').val() == "") ||
    ($('#editForm #editTagColor').val() == "") ||
    ($('#editForm input:radio:checked[name="editIfValid"]').val() == undefined)
  ) {
    alert("沒有輸入必要資料");
  } else {

    var formData = $("#editForm").serialize();
    $.ajax({
      url: "/api/v1/ShopTag/" + $('#editId').val(),
      type: 'PUT',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        $("#edit").hide();

        shopTagTable.ajax.reload();

        $("#list").show();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        switch (xhr.status) {
          case 422:
            alert('修改資料不完全');
            break;
          case 409:
            alert('此代碼已經被使用, 無法變更');
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
