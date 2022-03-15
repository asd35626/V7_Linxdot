var adminDereeTable;
var adminDetailTable;
$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
  options.async = true;
});


$(document).ready(function() {
  $('#showdetail').hide();

  var id = Cookies.get('authToken');

  // console.log('id:'+id);
  if (id == undefined) {

    Cookies.remove('authToken');
    window.location.replace("/Admin/Login");
  } else {
    //step 1 : 產生MainMenu
    createMainMenu(id);

    //step 2 : 產生ContentArea
    adminDereeTable = listAdminDegreeFunctions();

    //停用上一頁功能
    // backCheck();
  }
});

//get listAdminDegreeFunctions table
function listAdminDegreeFunctions() {
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
      "url": "/api/v1/DimUserDegreeToUserType",
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.userDegreeId = $('#functionDegreeId').val();
        d.userDegreeName = $('#functionName').val();
      }
    },
    "columns": [{
      "data": "UserTypeName"
    }, {
      "data": "DegreeId"
    }, {
      "data": "DegreeName"
    }, {
      "data": "IfValid"
    }, {
      "data": "CreateDate"
    }, {
      "data": "command",
      "width": "5%"
    }],
    "columnDefs": [{
      "targets": 3,
      "render": function(data, type, row) {
        if (row.IfValid == 1) return "啟用";
        else return "停用";
      }
    }, {
      "targets": 5,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-primary\" onclick=\"showDetail('" + row.DegreeId + "','" + row.UserType + "','" + row.DegreeName + "');\">瀏覽</div>";
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
  adminDereeTable.ajax.reload();
}

//showDetail
function showDetail(degreeId, userType, degreeName) {
  $('#detailDegreeId').val(degreeId);
  $('#detailUserTypeId').val(userType);
  $('#showdetail .row h3').html('所屬權限定義:(使用級別-' + degreeName + ')');
  if (adminDetailTable == undefined) {
    adminDetailTable = listAdminDetailTable();
  } else {
    adminDetailTable.ajax.reload();
  }
  $('#showdetail').show();
}

function listAdminDetailTable() {
  var table = $('#grid-basic-detail').DataTable({
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
      "url": "/api/v1/AdminDefaultPermission",
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "getPermissonlist";
        d.functionDegreeId = $('#detailDegreeId').val();
        d.functionUserTypeId = $('#detailUserTypeId').val();
      },
      "error": function() {
        alert('查無資料');
      }
    },
    "columns": [{
      "data": "ParentFunctionName"
    }, {
      "data": "FunctionName"
    }, {
      "data": "MenuOrder"
    }, {
      "data": "command"
    }],
    "columnDefs": [{
      "targets": 3,
      "render": function(data, type, row) {
        if (row.IfAccess == 1) {
          return "<div class=\"btn btn-sm btn-danger\" onclick=\"turnOff('" + row.PermissionId + "','" + row.FunctionId + "');\">開啟中</div>";
        } else {
          return "<div class=\"btn btn-sm btn-primary\" onclick=\"turnOn('" + row.PermissionId + "','" + row.FunctionId + "');\">已關閉</div>";
        }
      }
    }],
    "bSort": false, //排序
    "bFilter": false, //搜尋
    "lengthChange": false, //每頁筆數調整
    "paging": false,
  });

  return table;
}

function turnOn(permissionId, functionId) {
  // console.log('permissionId:'+permissionId);
  if (confirm("確定要開啟功能？")) {
    if (permissionId === 'null') {
      $.ajax({
        url: "/api/v1/AdminDefaultPermission",
        type: 'POST',
        dataType: "json",
        data: {
          "userTypeId": $('#detailUserTypeId').val(),
          "userDegreeId": $('#detailDegreeId').val(),
          "functionId": functionId,
          "ifAccess": 1
        },
        beforeSend: function(request) {
          request.setRequestHeader("Authorization", Cookies.get('authToken'));
        },
        success: function(response) {
          adminDetailTable.ajax.reload();
        },
        error: function(xhr, ajaxOptions, thrownError) {

          alert('登入權限已失效, 即將返回登入頁面');
          // console.log('xhr.status:'+xhr.status);
          // console.log('ajaxOptions:'+ajaxOptions);
          // console.log('thrownError:'+thrownError);

          Cookies.remove('authToken');
          window.location.replace("/Admin/Login");
        }
      });
    } else {
      $.ajax({
        url: "/api/v1/AdminDefaultPermission/" + permissionId,
        type: 'PUT',
        dataType: "json",
        data: {
          "editIfAccess": "1"
        },
        beforeSend: function(request) {
          request.setRequestHeader("Authorization", Cookies.get('authToken'));
        },
        success: function(response) {
          adminDetailTable.ajax.reload();
        },
        error: function(xhr, ajaxOptions, thrownError) {

          alert('登入權限已失效, 即將返回登入頁面');
          // console.log('xhr.status:'+xhr.status);
          // console.log('ajaxOptions:'+ajaxOptions);
          // console.log('thrownError:'+thrownError);

          Cookies.remove('authToken');
          window.location.replace("/Admin/Login");
        }
      });
    }
  }
}

function turnOff(permissionId, functionId) {
  if (confirm("確定要關閉功能？")) {
    $.ajax({
      url: "/api/v1/AdminDefaultPermission/" + permissionId,
      type: 'PUT',
      dataType: "json",
      data: {
        "editIfAccess": "-1"
      },
      beforeSend: function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      success: function(response) {
        adminDetailTable.ajax.reload();
      },
      error: function(xhr, ajaxOptions, thrownError) {

        alert('登入權限已失效, 即將返回登入頁面');
        // console.log('xhr.status:'+xhr.status);
        // console.log('ajaxOptions:'+ajaxOptions);
        // console.log('thrownError:'+thrownError);

        Cookies.remove('authToken');
        window.location.replace("/Admin/Login");
      }
    });
  }
}
