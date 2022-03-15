var categoryTable;
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
    categoryTable = listCategories();

    //停用上一頁功能
    // backCheck();
  }
});

//get listCategories table
function listCategories() {
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
      "url": "/api/v1/WebShowCategory",
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.searchTitle = $('#searchCategoryName').val();
        d.searchCategoryCode = $('#searchCategoryCode').val();
        d.searchParentCategoryID = $('#searchParentCategoryID').val();
      }
    },
    "columns": [{
      "data": "CategoryName" //0
    }, {
      "data": "CategoryCode"  //1
    }, {
      "data": "CategoryDesc" //2
    }, {
      "data": "IfValid" //3
    }, {
      "data": "CreateDate" //4
    },
    // {
    //   "width": "15%" //5     //先關閉子分類 2017-10-03
    // },
    {
      "width": "5%" //6
    }, {
      "width": "5%" //7
    }],
    "columnDefs": [{
      "targets": 3,
      "render": function(data, type, row) {
        if (row.IfValid == 1) return "啟用";
        else return "停用";
      }
    },
    //先關閉子分類 2017-10-03
    // {
    //   "targets": 5,
    //   "render": function(data, type, row) {
    //     var str = "";
    //     str += "<div class=\"btn btn-sm btn-primary\" onclick=\"reload('" + row.id + "');\">子類別</div>";
    //     return str;
    //   }
    // },
    {
      "targets": 5,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-primary\" onclick=\"editData('" + row.id + "');\">修改</div>";
      }
    }, {
      "targets": 6,
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
  categoryTable.ajax.reload();
}

function toAdd() {
  var id = $('#searchParentCategoryID').val();
  if (id == '') {
    window.location = "/Show/CategoryAdd";
  } else {
    window.location = "/Show/CategoryAdd/" + id;
  }
}

function returnToList() {
  $('#list').show();
  $('#edit').hide();
  $('#add').hide();
}

//show edit page
function editData(thisId) {

  window.location = "/Show/CategoryEdit/" + thisId;
}

function deleteData(thisId) {
  if (confirm("確定要刪除嗎?")) {
    $.ajax({
      url: "/api/v1/WebShowCategory/" + thisId,
      type: 'DELETE',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {
          categoryTable.ajax.reload();
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


function reload(thisId) {

  window.location = "/Show/Category/" + thisId;

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
      url: "/api/v1/WebShowCategory/" + thisId,
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
