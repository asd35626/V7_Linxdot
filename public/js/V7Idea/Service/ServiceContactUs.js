var ContactUsTable;
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
    ContactUsTable = listContactUs();

    //停用上一頁功能
    // backCheck();
  }
});

//get listContactUs table
function listContactUs() {
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
      "url": "/api/v1/UserFeedBack",
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.userName = $('#functionUserName').val();
        d.ifService = $('#list #functionIfService').val();
      }
    },
    "columns": [{
      "data": "CreateDate"
    }, {
      "data": "FeedBackType"
    }, {
      "data": "UserName"
    }, {
      "data": "UserEmail"
    }, {
      "data": "ContactPhone"
    }, {
      "data": "UserIPAddress"
    }, {
      "data": "IfService"
    }, {
      "data": "command"
    }],
    "columnDefs": [{
      "targets": 6,
      "render": function(data, type, row) {
        if (row.IfService == 1) return "已處理";
        else return "未處理";
      }
    }, {
      "targets": 7,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-warning\" onclick=\"editData('" + row.id + "');\">檢視</div>";
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
  ContactUsTable.ajax.reload();
}


function returnToList() {
  $('#list').show();
  $('#edit').hide();
}

//show edit page
function editData(thisId) {
  //initial
  $('#list').hide();
  $('#edit').show();
  loadUserFeedBackTypeList('#editUserFeedBackType');
  //get detail data
  $.ajax({
    url: "/api/v1/UserFeedBack/" + thisId,
    type: 'GET',
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.status == 1) {
        $('#editId').val(response.data.id);
        $('#editUserName').val(response.data.UserName);
        $('#editUserEmail').val(response.data.UserEmail);
        $('#editContactPhone').val(response.data.ContactPhone);

        $('#editFax').val(response.data.Fax);
        $('#editUserIPAddress').val(response.data.UserIPAddress);
        $('#editAddress').val(response.data.Address);

        $('#editCreateDate').val(response.data.CreateDate);
        $('#editFeedBackContent').val(response.data.FeedBackContent);
        $('#editServiceMessage').val(response.data.ServiceMessage);

        //radio
        if (response.data.IfService == 1) {
          $('#editForm input[name=editIfService][value=1]').attr('checked', true);
        } else {
          $('#editForm input[name=editIfService][value=-1]').attr('checked', true);
        }
        //selectList
        $('#editForm #editUserFeedBackType').val(response.data.FeedBackTypeId);
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

  var formData = $("#editForm").serialize();
  if (
    ($('#edit #editUserFeedBackType').val() == "") ||
    ($('#edit #editServiceMessage').val() == "") ||
    ($('#editForm input:radio:checked[name="editIfService"]').val() == undefined)
  ) {
    alert("請確認必填欄位是否都有輸入資料");
  } else {
    $.ajax({
      url: "/api/v1/UserFeedBack/" + $('#editId').val(),
      type: 'PUT',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        $("#edit").hide();

        ContactUsTable.ajax.reload();

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

function loadUserFeedBackTypeList(selectListId) {
  $.ajax({
    url: "/api/v1/UserFeedBackType",
    type: 'GET',
    async: false,
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      $(selectListId).html('<option value="">請選擇</option>'); //reset select list
      $.each(response.data, function(index, obj) {
        if ((obj.IfValid == 1) && (obj.IfDelete == 0)) {
          $(selectListId).append($('<option>', {
            value: obj.id,
            text: obj.FeedBackType
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
