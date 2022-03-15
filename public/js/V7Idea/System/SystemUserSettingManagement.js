var userTable;

$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
  options.async = true;
});


$(document).ready(function() {
  $('#add').hide();
  $('#edit').hide();

  var id = Cookies.get('authToken');

  if (id == undefined) {

    Cookies.remove('authToken');
    window.location.replace("/Admin/Login");
  } else {
    //step 1 : 產生MainMenu
    createMainMenu(id);

    //step 2 : 產生ContentArea
    userTable = listUsers();

    //停用上一頁功能
    // backCheck();
  }
});

//get listUsers table
function listUsers() {
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
      "url": "/api/v1/DimUser",
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.searchUserType = 10;
        d.searchMemberNo = $('#searchMemberNo').val();
        d.searchRealName = $('#searchRealName').val();
      }
    },
    "columns": [{
      "data": "MemberNo"
    }, {
      "data": "RealName"
    }, {
      "data": "ContactPhone"
    }, {
      "data": "DegreeName"
    }, {
      "data": "CreateDate"
    }, {
      "width": "5%"
    }, {
      "width": "5%"
    }],
    "columnDefs": [{
      "targets": 5,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-primary\" onclick=\"editData('" + row.Id + "');\">修改</div>";
      }
    }, {
      "targets": 6,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-danger\" onclick=\"deleteData('" + row.Id + "');\">刪除</div>";
      }
    }],
    "bSort": false, //排序
    "bFilter": false, //搜尋
    "lengthChange": false
  });

  return table;
}

//do list search
function search() {
  userTable.ajax.reload();
}

function toAdd() {
  $('#list').hide();
  $('#edit').hide();
  $('#add').show();
  loadDegreedList('#addForm #addDegreeId');
}

function returnToList() {
  $('#list').show();
  $('#edit').hide();
  $('#add').hide();
}

//show edit page
function editData(thisId) {
  //initial
  $('#list').hide();
  $('#edit').show();
  $('#add').hide();
  loadDegreedList('#editForm #editDegreeId');
  //get detail data
  $.ajax({
    url: "/api/v1/DimUser/" + thisId,
    type: 'GET',
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.status == 1) {
        $('#editId').val(response.data.Id);
        $('#editMemberNo').val(response.data.MemberNo);
        $('#editRealName').val(response.data.RealName);
        $('#editContactPhone').val(response.data.ContactPhone);
        $('#editUserEmail').val(response.data.UserEmail);
        $('#editUserMobile').val(response.data.UserMobile);
        $('#editUserPosition').val(response.data.UserPosition);
        $('#editUserPositionDegree').val(response.data.UserPositionDegree);
        $('#editWorkingNumber').val(response.data.WorkingNumber);
        $('#editDegreeId').val(response.data.DegreeId);
        $('#editCreateBy').val(response.data.CreateBy);
        $('#editCreateDate').val(response.data.CreateDate);

        if (response.data.IfValid == 1) {
          $('#edit input[name=editIfValid][value=1]').attr('checked', true);
        } else {
          $('#edit input[name=editIfValid][value=-1]').attr('checked', true);
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

function deleteData(thisId) {
  if (confirm("確定要刪除嗎?")) {
    $.ajax({
      url: "/api/v1/DimUser/" + thisId,
      type: 'DELETE',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {
          userTable.ajax.reload();
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
    ($('#addMemberNo').val() == "") ||
    ($('#addUserPassword').val() == "") ||
    ($('#addRealName').val() == "") ||
    ($('#addDegreeId').val() == "") ||
    ($('#addForm input:radio:checked[name="addIfValid"]').val() == undefined)
  ) {
    alert("沒有輸入必要資料");
  } else {
    var formData = new FormData($("#addForm")[0]);

    $.ajax({
      url: "/api/v1/DimUser/",
      type: 'POST',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        $("#add").hide();

        userTable.ajax.reload();

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
  var formData = $("#editForm").serialize();
  $.ajax({
    url: "/api/v1/DimUser/" + $('#editId').val(),
    type: 'PUT',
    data: formData,
    async: false,
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(data) {
      $("#edit").hide();

      userTable.ajax.reload();

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
  return false;
}

function resetEditData() {
  editData($('#editId').val());
}

function loadDegreedList(selectListId) {
  $.ajax({
    url: "/api/v1/DimUserDegreeToUserType?userType=10&length=-1",
    type: 'GET',
    async: false,
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      console.log('selectListId:' + selectListId);
      $(selectListId).html('<option value="">請選擇</option>'); //reset select list
      $.each(response.data, function(index, obj) {
        // console.log('DegreeId:'+obj.DegreeId);
        // console.log('DegreeName:'+ obj.DegreeName);
        if (obj.IfValid == 1) {
          $(selectListId).append($('<option>', {
            value: obj.DegreeId,
            text: obj.DegreeName
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
