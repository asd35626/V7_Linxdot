var table;
var APIURL = '/api/v1/ShopCoupon';
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
    table = listTable();

    //停用上一頁功能
    // backCheck();
  }
});

//get listUsers table
function listTable() {
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
      "url": APIURL,
      "type": "POST",
      "beforeSend": function(request) {
        request.setRequestHeader("Authorization", Cookies.get('authToken'));
      },
      "data": function(d) {
        d.action = "list";
        d.searchName = $('#searchName').val();
      }
    },
    "columns": [
    {
      "data": "CouponCode",
      "width": "10%"
    }, {
      "data": "CouponTypeName",
      "width": "14%"
    }, {
      "data": "CouponName",
      "width": "10%"
    }, {
      "data": "RedeemType",//3. 折抵類型
      "width": "8%"
    }, {
      "data": "DiscountAmount",
      "width": "8%"
    }, {
      "data": "data", //5. 總發行允許數量
      "width": "10%"
    },{
      "data": "data", //6. 已發行/使用數量
      "width": "10%"
    }, {
      "data": "data", //7. status
      "width": "8%"
    },{
      "width": "5%"
    }, {
      "width": "5%"
    }],
    "columnDefs": [{
      "targets": 3,
      "render": function(data, type, row) {
        var str = '折抵現金';
        if(row.RedeemType == '1') str = '關聯商品';
        return str;
      }
    },{
      "targets": 5,
      "render": function(data, type, row) {
        var str = row.TotalIssueQuantity;
        if(row.IfTotalIssueLimitation == '1') ;
        else str += '無限制';
        return str;
      }
    },{
      "targets": 6,
      "render": function(data, type, row) {
        return row.PulishCount + "/" + row.UsedCount;
      }
    },{
      "targets": 7,
      "render": function(data, type, row) {
        var str = '已停用';
          if(row.IfValid == 1) str = '啟用中';
          return str;
      }
    },{
      "targets": 8,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-primary\" onclick=\"editData('" + row.id + "');\">修改</div>";
      }
    }, {
      "targets": 9,
      "render": function(data, type, row) {
        return "<div class=\"btn btn-sm btn-danger\" onclick=\"deleteData('" + row.id + "');\">刪除</div>";
      }
    }, {
      "targets": [1,2,3,4,5,6,7,8,9],
      "orderable": false
    }],
    "bSort": true, //排序
    "bFilter": false, //搜尋
    "lengthChange": false
  });

  return table;
}

  //do list search
  function search() {
    table.ajax.reload();
  }

  function toAdd() {
    location.href = "/Coupon/CouponAdd";
  }

  function editData(thidId){
    location.href = "/Coupon/CouponEdit/"+thidId;
  }

  function deleteData(thisId) {
    if (confirm("確定要刪除嗎?")) {
      $.ajax({
        url: APIURL +"/"+ thisId,
        type: 'DELETE',
        headers: {
          'Authorization': Cookies.get('authToken')
        },
        success: function(response) {
          if (response.status == 1) {
            table.ajax.reload();
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          // window.location.replace("/Admin/Login");
          //      alert(xhr.status);
          //      alert(xhr.statusText);
          //      alert(xhr.responseText);
          switch (xhr.status) {
            case 409:
              alert('此票種已經發行過, 無法刪除');
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
  }

  function editDate(thidId){
    location.href = "/Coupon/TypeEdit/"+thidId;
  }
