var Table;
var ThisAPI = "WebDocument";
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
    Table = listTable();

    //載入服務分類
    loadMainFunctionList('#searchDCID');
  }
});

//Logout
function logout() {
  var token = Cookies.get('authToken');
  $.ajax({
    url: "/api/v1/UserProcessTickets/" + token,
    type: 'DELETE',
    success: function(response) {
      Cookies.remove('authToken');
      window.location.replace("/Admin/Login");
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

var userAuth = '';
//get listTable table
function listTable() {

  // alert(Cookies.get('authToken'));
  userAuth = Cookies.get('authToken');

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
      "url": "/api/v1/"+ThisAPI,
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.searchTitle = $('#searchTitle').val();
        d.searchDCID = $('#searchDCID').val();
      }
    },
    "columns": [{
      "data": "DocumentCategory"
    }, {
      "data": "DocumentTitle"
    }, {
      "data": "ShortDescription"
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
      "targets": 4,
      "render": function(data, type, row) {
        if (row.IfValid == 1) return "啟用";
        else return "停用";
      }
    }, {
      "targets": 5,
      "render": function(data, type, row) {
        return "<div class=\"md-btn md-btn-primary\" onclick=\"editData('" + row.id + "');\">修改</div>";
      }
    }, {
      "targets": 6,
      "render": function(data, type, row) {
        return "<div class=\"md-btn md-btn-danger\" onclick=\"deleteData('" + row.id + "');\">刪除</div>";
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
  Table.ajax.reload();
}

function toAdd() {
  $('#list').hide();
  $('#edit').hide();
  $('#add').show();
  loadMainFunctionList('#addForm #addDCID');
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
    url: "/api/v1/"+ThisAPI+"/" + thisId,
    type: 'GET',
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.status == 1) {
        $('#editForm #editId').val(response.data.id);
        $('#editForm #editDCID').val(response.data.DCID);
        //load select mainlist
        loadMainFunctionList('#editForm #editDCID', response.data.DCID);
        $('#editForm #editDocumentTitle').val(response.data.DocumentTitle);
        $('#editForm #editSerialNO').val(response.data.SerialNO);
        $('#editForm #editShortDescription').val(response.data.ShortDescription);
        $('#editForm #editLongDescription').val(response.data.LongDescription);
        $('#editForm #editCreateBy').val(response.data.CreateBy);
        $('#editForm #editCreateDate').val(response.data.CreateDate);
        //radio
        if (response.data.IfValid == 1) {
          $('#editForm input[name=editIfValid][value=1]').attr('checked', true);
        } else {
          $('#editForm input[name=editIfValid][value=-1]').attr('checked', true);
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
      url: "/api/v1/"+ThisAPI+"/" + thisId,
      type: 'DELETE',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {
          Table.ajax.reload();
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
  check = true;
  if($('#addForm #addDCID').val() == ''){
    check = false;
    alert('請選擇服務類型');
  }

  if(check){
    if(!validateNumber($('#addSerialNO').val())){
      check = false;
      alert('請輸入排列順序');
    }
  }

  if(check){
    if($('#addDocumentTitle').val() == ''){
      check = false;
      alert('請輸入服務名稱');
    }
  }

  if(check){
    if($('#addShortDescription').val() == ''){
      check = false;
      alert('請輸入簡短敘述');
    }
  }

  if(check){
    if($('#addLongDescription').val() == ''){
      check = false;
      alert('請輸入詳細說明');
    }
  }

  if(check){
    if($('#addForm input:radio:checked[name="addIfValid"]').val() == undefined){
      check = false;
      alert('請勾選請用或停用');
    }
  }

  if(check){
    var formData = new FormData($("#addForm")[0]);

    $.ajax({
      url: "/api/v1/"+ThisAPI,
      type: 'POST',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        $("#add").hide();

        Table.ajax.reload();

        $("#list").show();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        switch (xhr.status) {
          case 422:
            alert('新增資料不完全');
            break;
          case 409:
            alert('此項服務已經存在');
            break;
          case 401:
            alert('沒有存取權限');
            window.location.replace("/Admin/Login");
            break;
          default:
            alert('無法新增資料, status' + xhr.status );
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
  check = true;
  if($('#editForm #editDCID').val() == ''){
    check = false;
    alert('請選擇服務類型');
  }

  if(check){
    if(!validateNumber($('#editSerialNO').val())){
      check = false;
      alert('請輸入排列順序');
    }
  }

  if(check){
    if($('#editDocumentTitle').val() == ''){
      check = false;
      alert('請輸入服務名稱');
    }
  }

  if(check){
    if($('#editShortDescription').val() == ''){
      check = false;
      alert('請輸入簡短敘述');
    }
  }

  if(check){
    if($('#editLongDescription').val() == ''){
      check = false;
      alert('請輸入詳細說明');
    }
  }

  if(check){
    if($('#editForm input:radio:checked[name="editIfValid"]').val() == undefined){
      check = false;
      alert('請勾選請用或停用');
    }
  }

  if(check){
    var formData = $("#editForm").serialize();
    $.ajax({
      url: "/api/v1/"+ ThisAPI + "/" + $('#editId').val(),
      type: 'PUT',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        $("#edit").hide();

        Table.ajax.reload();

        $("#list").show();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        switch (xhr.status) {
          case 422:
            alert('修改資料不完全');
            break;
          case 409:
            alert('此階級已經被使用, 無法變更');
            break;
          case 401:
            alert('Token 不存在');
            window.location.replace("/Admin/Login");
            break;
          default:
            alert('無法修改資料!' + xhr.status);
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

function loadMainFunctionList(selectListId, DCID='') {
  $.ajax({
    url: "/api/v1/WebDocumentCategory?searchTitle=IntroductionCategory",
    type: 'GET',
    // async: false,
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
            text: obj.CategoryDescription
          }));
        }
      });

      if(DCID != '') $(selectListId).val(DCID);
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

function validateNumber(input){
  var format = /^[0-9]+$/;
   if (format.test(input)){
     return true;
   }
   return false;
}
