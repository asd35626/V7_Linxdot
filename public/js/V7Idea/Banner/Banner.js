var ajaxTable;
var ajaxMapTable;
var THISURLADD = "/Banner/BannerAdd";
var THISURLEDIT = "/Banner/BannerEdit/";

var THISAPI = "/api/v1/DimBanner";
var THISMAPAPI = "/api/v1/DimBannerMapping";

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
    ajaxTable = listAjaxTable();
    ajaxMapTable = listAjaxMapTable();
    //停用上一頁功能
    // backCheck();
  }
});

//get listAjaxTable table
function listAjaxTable() {
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
      "url": THISAPI,
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.searchBannerName = $('#list #BrandName').val();
        // d.searchDocumentCode = $('#list #BrandCode').val();
      }
    },
    "columns": [{
      "data": "BannerName",
      "width": "8%"
    }, {
      "data": "BannerType",
      "width": "8%"
    }, {
      "data": "BannerDescription",
    }, {
      "data": "BannerLink"
    }, {
      "data": "IfValid",
      "width": "8%"
    }, {
      "data": "CreateDate"
    }, {
      "width": "5%"
    }, {
      "width": "5%"
    }, {
      "width": "5%"
    }],
    "columnDefs": [{
      "targets": 1,
      "render": function(data, type, row) {
        if (row.BannerType == '1') return "影音";
        else return "圖片";
      }
    }, {
      "targets": 4,
      "render": function(data, type, row) {
        if (row.IfValid == '1') return "啟用中";
        else return "停用中";
      }
    }, {
      "targets": 6,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-primary\" onclick=\"show('" + row.id + "');\">瀏覽</div>";
      }
    },{
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
  ajaxTable.ajax.reload();
}

function toAdd() {
  window.location = THISURLADD;
}

function toAddMapping(){
  location.href = "/Banner/MappingAdd/" + $('#BID').val();
}
//show edit page
function editData(thisId) {
  window.location = THISURLEDIT + thisId;
}


function deleteData(thisId) {
  if (confirm("確定要刪除嗎?")) {
    $.ajax({
      url: THISAPI + "/" + thisId,
      type: 'DELETE',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {
          ajaxTable.ajax.reload();
          $('#MappingList').hide();
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


//get listAjaxMapTable table
function listAjaxMapTable() {
  var table = $('#grid-basic-map').DataTable({
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
      "url": THISMAPAPI,
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.searchBID = $('#MappingList #BID').val();
        d.searchBCID = $('#MappingList #BCID').val();
      }
    },
    "columns": [{
      "data": "CategoryPosition",
      "width": "8%"
    }, {
      "data": "SerialNo",
      "width": "10%"
    }, {
      "data": "CategoryName",
    }, {
      "data": "CategoryCode"
    }, {
      "data": "OnWebStartDate",
      "width": "8%"
    }, {
      "data": "OnWebEndDate",
      "width": "8%"
    }, {
      "data": "Status",
      "width": "8%"
    }, {
      "width": "15%"
    }],
    "columnDefs": [{
      "targets": 7,
      "render": function(data, type, row) {
        var str = "<div class=\"btn btn-sm btn-primary\" onclick=\"editMap('" + row.id + "');\">修改</div>";
        str += "<div class=\"btn btn-sm btn-danger\" onclick=\"deleteMap('" + row.id + "');\">立即下架</div>";
        return str;
      }
    }],
    "bSort": false, //排序
    "bFilter": false, //搜尋
    "lengthChange": false, //每頁筆數調整
  });

  return table;
}

function show(BID){
  console.log('BID:'+BID);
  $('#BID').val(BID);
  ajaxMapTable.ajax.reload();
  $('#MappingList').show();
}

function deleteMap(thisId) {
  if (confirm("確定要立即下架嗎?")) {
    $.ajax({
      url: THISMAPAPI + "/" + thisId,
      type: 'DELETE',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
          ajaxTable.ajax.reload();
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

function editMap(thisId){
  location.href = "/Banner/MappingEdit/" + thisId;
}
