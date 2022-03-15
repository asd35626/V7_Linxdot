var table;
var APIURL = '/api/v1/ShopCouponIssuesTickets';

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

    //step 3 : load select
    loadCouponID('#searchCouponID');

    //step 4: datetimepicker
    $('#searchIssueDateStart').datetimepicker({
      // defaultDate: moment(),
      format: 'YYYY-MM-DD',
      sideBySide: true
    });

    $('#searchIssueDateEnd').datetimepicker({
      format: 'YYYY-MM-DD',
      sideBySide: true
    });
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
        d.searchCode = $('#searchCode').val();
        d.searchCouponID = $('#searchCouponID').val();
        d.searchIssueDateStart = $('#searchIssueDateStart').val();
        d.searchIssueDateEnd = $('#searchIssueDateEnd').val();
      }
    },
    "columns": [
    {
      "data": "TypeName"
    }, {
      "data": "CouponName"
    }, {
      "data": "IssueDate" //IssueDate
    }, {
      "data": "Owner"
    }, {
      "data": "TicketCode"
    }, {
      "data": "data" // 5. ValidDateFrom/ValidDateTo
    },{
      "data": "data" // 6. IfValid
    },{
      "data": "data" // 7. function
    }
    ],
    "columnDefs": [
      {
        "targets": 5,
        "render": function(data, type, row) {
            var str = '';
            str += row.ValidDateFrom + '/'+row.ValidDateTo;
            return str;
        }
      },{
      "targets": 6,
      "render": function(data, type, row) {
        var str = '已停用';
          if(row.IfValid == 1) str = '啟用中';
          return str;
      }
    },{
      "targets": 7,
      "render": function(data, type, row) {
        var str = '';
        if(row.IfUse == '0') str = "<div class=\"btn btn-sm btn-info\" onclick=\"deleteData('" + row.id + "');\">刪除</div>";
        return str;
      }
    }, {
      "targets": [1,2,3,4,5,6,7],
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
          switch (xhr.status) {
            case 409:
            //      alert(xhr.status);
            //      alert(xhr.statusText);
            var res = JSON.parse(xhr.responseText);
                 alert(res.message);
                 console.log(xhr.responseText);
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

  // function searchCouponID(){
  //   $('#searchCouponIDInput').autocomplete({
  //       source: function(request, response) {
  //         console.log('request.term:'+request.term);
  //           $.ajax({
  //               url: "/api/v1/ShopCoupon",
  //               type: 'GET',
  //               async: false,
  //               headers: {
  //                   'Authorization': Cookies.get('authToken')
  //               },
  //               data: {
  //                   searchName: request.term
  //               },
  //               success: function(output) {
  //                   var outArr = new Array();
  //                   $.each(output.data, function(i, item) {
  //                     var tempArr = {
  //                         id: item.id,
  //                         label: item.CouponName +'('+item.CouponCode+')',
  //                         value: item.CouponName
  //                     };
  //                     outArr.push(tempArr);
  //                   });
  //
  //                   response(outArr);
  //               }
  //           });
  //       },
  //       minLength: 1,
  //       change: function(event, ui) {
  //         // var inputField = $('#searchCouponIDInput');
  //         // console.log('length: '+inputField.length);
  //         //   $('#searchCouponID').val('');
  //       },
  //       select: function(event, ui) {
  //           $('#searchCouponIDInput').val(ui.item.label);
  //           $('#searchCouponID').val(ui.item.id);
  //       }
  //   });
  // }

  function loadCouponID(selectListId){
    $.ajax({
      url: "/api/v1/ShopCoupon?length=-1",
      type: 'GET',
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        $(selectListId).html('<option value="">請選擇</option>'); //reset select list
        $.each(response.data, function(index, obj) {
            $(selectListId).append($('<option>', {
              value: obj.id,
              text: obj.CouponName + '('+obj.CouponCode+')'
            }));
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
