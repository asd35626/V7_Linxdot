var shopCategoryTable;
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

    // Get ID

    getNavigationBar($('#searchParentCategoryID').val());


    //step 2 : 產生ContentArea
    // shopCategoryTable = listShopCategories();
    toAdd();

    //停用上一頁功能
    // backCheck();
  }
});

//get listShopCategories table
function listShopCategories() {
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
      "url": "/api/v1/ShopCategory",
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.searchCategoryName = $('#searchCategoryName').val();
        d.searchCategoryCode = $('#searchCategoryCode').val();
        d.searchParentCategoryID = $('#searchParentCategoryID').val();
      }
    },
    "columns": [{
      "data": "CategoryName"
    }, {
      "data": "CategoryCode"
    }, {
      "data": "CategoryDesc"
    }, {
      "data": "IfValid"
    }, {
      "data": "CreateDate"
    }, {
      "width": "15%"
    }, {
      "width": "5%"
    }, {
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
        var str = "";
        str += "<div class=\"btn btn-sm btn-primary\" onclick=\"reload('" + row.id + "');\">子類別</div>";
        return str;
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
    "lengthChange": false
  });

  return table;
}

//do list search
function search() {
  shopCategoryTable.ajax.reload();
}

function toAdd() {
  $('#list').hide();
  $('#edit').hide();
  $('#add').show();
  loadMainFunctionList('#addForm #addUserType');
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
  //load select mainlist
  loadMainFunctionList('#editForm #editUserType');
  //get detail data
  $.ajax({
    url: "/api/v1/ShopCategory/" + thisId,
    type: 'GET',
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.status == 1) {
        $('#editForm #editId').val(response.data.id);
        $('#editForm #editCategoryName').val(response.data.CategoryName);
        $('#editForm #editCategoryDesc').val(response.data.CategoryDesc);
        $('#editForm #editCategoryCode').val(response.data.CategoryCode);
        $('#editForm #editCreateBy').val(response.data.CreateBy);
        $('#editForm #editCreateDate').val(response.data.CreateDate);
        //radio
        if (response.data.IfValid == 1) {
          $('#editForm input[name=editIfValid][value=1]').attr('checked', true);
        } else {
          $('#editForm input[name=editIfValid][value=-1]').attr('checked', true);
        }

        if (response.data.ParentCategoryID) {
          $('#editParentArea').show();
          $('#editParentCategoryID').val(response.data.ParentCategoryID);
          $('#editParentCategoryName').val(response.data.ParentCategoryName);
        } else {
          $('#editParentArea').hide();
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
      url: "/api/v1/ShopCategory/" + thisId,
      type: 'DELETE',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {
          shopCategoryTable.ajax.reload();
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
    ($('#addForm #addCategoryName').val() == "") ||
    ($('#addForm #addCategoryCode').val() == "") ||
    ($('#addForm input:radio:checked[name="addIfValid"]').val() == undefined)
  ) {
    alert("沒有輸入必要資料");
  } else {
    //check code
    // console.log('lv:'+$('#addLevel').val());
    var codeStr = $('#addCategoryCode').val();
    var listLevel = $('#addLevel').val();
    var check = codeCheck(codeStr, listLevel);
    if (!check) return false; //check通過才送出資料

    var formData = new FormData($("#addForm")[0]);

    $.ajax({
      url: "/api/v1/ShopCategory/",
      type: 'POST',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        window.history.back();
        //reset
        // $('#addForm #addCategoryName').val('');
        // $('#addForm #addCategoryCode').val('');
        // $('#addForm #addCategoryDesc').val('');
        // $("#add").hide();
        //
        // shopCategoryTable.ajax.reload();
        //
        // $("#list").show();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        switch (xhr.status) {
          case 422:
            alert('新增資料不完全');
            break;
          case 409:
            alert('分類代碼已經被使用');
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
    ($('#editForm #editCategoryName').val() == "") ||
    ($('#editForm #editCategoryCode').val() == "") ||
    ($('#editForm input:radio:checked[name="editIfValid"]').val() == undefined)
  ) {
    alert("沒有輸入必要資料");
  } else {
    var codeStr = $('#editCategoryCode').val();
    var listLevel = $('#addLevel').val();
    var check = codeCheck(codeStr, listLevel);
    if (!check) return false; //check通過才送出資料
    var formData = $("#editForm").serialize();
    $.ajax({
      url: "/api/v1/ShopCategory/" + $('#editId').val(),
      type: 'PUT',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        $("#edit").hide();

        shopCategoryTable.ajax.reload();

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

function loadMainFunctionList(selectListId) {
  $.ajax({
    url: "/api/v1/DimUserType",
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
            text: obj.UserName
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

function reload(thisId) {

  window.location = "/Shop/Category/" + thisId;

  //get navigationBar
  // getNavigationBar(thisId);

  //refresh
  // $('#searchParentCategoryID').val(thisId);
  // shopCategoryTable.ajax.reload();

}

function getNavigationBar(thisId) {
  if (thisId == '') {
    $('#navigationBar').html("<span onclick=\"reload('')\" style=\"text-decoration:underline\">分類</span>");
    //set parentCategoryID and parentCategoryName to addPage
    $('#addParentCategoryID').val('');
    $('#addParentCategoryName').val('');
    $('#addParentArea').hide(); //最上層不顯示
    $('#addLevel').val('');
    // console.log('length:' + $('#addLevel').val());
  } else {
    $.ajax({
      url: "/api/v1/ShopCategory/" + thisId,
      type: 'GET',
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        var content = "<span onclick=\"reload('')\" style=\"text-decoration:underline\">分類</span>";
        $.each(response.history, function(index, obj) {
          content += ">";
          content += "<span onclick=\"reload('" + obj.id + "')\" style=\"text-decoration:underline\">" + obj.name + "</span>";
        });
        $('#navigationBar').html(content);

        // console.log('length:' + response.history.length);
        //set parentCategoryID and parentCategoryName to addPage
        if (response.history.length > 0) {
          var index = response.history.length - 1;
          $('#addParentCategoryID').val(response.history[index].id);
          $('#addParentCategoryName').val(response.history[index].name);
          $('#addLevel').val(response.history.length);
          $('#addParentArea').show(); //最上層不顯示
        }
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
}

function codeCheck(codeStr, listLevel) {
  var regExp_1 = /^[0-9]{2}$/; // 1,2, 00-99
  var regExp_2 = /^[1-9]{1}[0-9]{2}$/; // 3, 100-999
  var regExp_3 = /^[2-9]{1}[0-9]{2}$/; // 4, 200-999

  var chk = false;

  if (listLevel == '') {
    if (regExp_1.test(codeStr)) return true;
    else {
      alert('類別代碼沒有符合編碼規則(第一層兩碼數字)');
      return false;
    }
  } else if (listLevel == 1) {
    if (regExp_1.test(codeStr)) return true;
    else {
      alert('類別代碼沒有符合編碼規則(第二層兩碼)');
      return false;
    }
  } else if (listLevel == 2) {
    if (regExp_2.test(codeStr)) return true;
    else {
      alert('類別代碼沒有符合編碼規則(第三層三碼數字)');
      return false;
    }
  } else if (listLevel == 3) {
    if (regExp_3.test(codeStr)) return true;
    else {
      alert('類別代碼沒有符合編碼規則(第四層三碼數字)');
      return false;
    }
  } else {
    //exception
    alert('類別代碼沒有符合編碼規則');
    return false;
  }

  return true;
}
