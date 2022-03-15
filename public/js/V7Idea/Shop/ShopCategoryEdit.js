var shopCategoryTable;
$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
  options.async = true;
});


$(document).ready(function() {
  // $('#add').hide();
  // $('#edit').hide();
  // $('#list').show();
  var id = Cookies.get('authToken');

  // console.log('id:'+id);
  if (id == undefined) {

    Cookies.remove('authToken');
    window.location.replace("/Admin/Login");
  } else {
    //step 1 : 產生MainMenu
    createMainMenu(id);

    // Get ID

    editData($('#editCategoryID').val());


    //step 2 : 產生ContentArea
    // shopCategoryTable = listShopCategories();

    //停用上一頁功能
    // backCheck();
  }
});

function returnToList() {
  // $('#list').show();
  // $('#edit').hide();
  // $('#add').hide();
  window.history.back();
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
        window.history.back();
        // $("#edit").hide();
        //
        // shopCategoryTable.ajax.reload();
        //
        // $("#list").show();
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
