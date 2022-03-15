var memberTable;

$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
  options.async = true;
});


$(document).ready(function() {

  var id = Cookies.get('authToken');

  if (id == undefined) {

    Cookies.remove('authToken');
    window.location.replace("/Admin/Login");
  } else {
    //step 1 : 產生MainMenu
    createMainMenu(id);

    //step 2 : 產生ContentArea
    memberTable = listMembers();

    //停用上一頁功能
    // backCheck();
  }
});

//get listMembers table
function listMembers() {
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
      "url": "/api/v1/DimMember",
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.searchMemberNo = $('#searchMemberNo').val();
        d.searchRealName = $('#searchRealName').val();
        d.searchContactPhone = $('#searchContactPhone').val();
        d.searchDegreeID = 10;
      }
    },
    "columns": [{
      "data": "MemberNo", "width": "15%"
    }, {
      "data": "RealName", "width": "15%"
    }, {
      "data": "ContactPhone", "width": "15%"
    },{
      "data": "IfValid", "width": "5%"
    }, {
      "data": "function"
    }],
    "columnDefs": [{
      "targets": 3,
      "render": function(data, type, row) {
        if(row.IfValid == 1) return "啟用中";
        else return "已停用";
      }
    }, {
      "targets": 4,
      "render": function(data, type, row) {
        var str = "<div class=\"btn btn-sm btn-primary\" onclick=\"alert('訂單');\">訂單</div>";
        str += "<div class=\"btn btn-sm btn-primary\" onclick=\"alert('點數');\">點數</div>";
        str += "<div class=\"btn btn-sm btn-primary\" onclick=\"alert('票卷');\">票卷</div>";
        str += "<div class=\"btn btn-sm btn-primary\" onclick=\"alert('收藏');\">收藏</div>";
        return str
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
  memberTable.ajax.reload();
}

function toAdd() {
  $('#list').hide();
  $('#edit').hide();
  $('#add').show();
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

  //get detail data
  $.ajax({
    url: "/api/v1/DimUserType/" + thisId,
    type: 'GET',
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.status == 1) {
        $('#editId').val(response.data.UserTypeId);
        $('#editUserTypeId').val(response.data.UserTypeId);
        $('#editUserTypeName').val(response.data.UserName);
        $('#editUserTypeMemo').val(response.data.UserMemo);
        $('#editCreateBy').val(response.data.CreateBy);
        $('#editCreateDate').val(response.data.CreateDate);
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
      url: "/api/v1/DimUserType/" + thisId,
      type: 'DELETE',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {
          memberTable.ajax.reload();
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
  if (($('#AddUserTypeId').val() == "") || ($('#AddUserTypeName').val() == "")) {
    alert("沒有輸入必要資料");
  } else {
    var formData = new FormData($("#addForm")[0]);

    $.ajax({
      url: "/api/v1/DimUserType/",
      type: 'POST',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        $("#add").hide();

        memberTable.ajax.reload();

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
    url: "/api/v1/DimUserType/" + $('#editId').val(),
    type: 'PUT',
    data: formData,
    async: false,
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(data) {
      $("#edit").hide();

      memberTable.ajax.reload();

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
