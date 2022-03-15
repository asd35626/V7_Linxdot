  var table;
  var APIURL = '/api/v1/ShopCouponType';
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

  //get list table
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
        "data": "CouponTypeCode",
        "width": "10%"
      }, {
        "data": "CouponTypeName"
      }, {
        "data": "CouponCount",
        "width": "5%"
      }, {
        "data": "data",
        "width": "10%"
      }, {
        "data": "data",
        "width": "5%"
      },{
        "width": "5%"
      }, {
        "width": "5%"
      }],
      "columnDefs": [
      {
        "targets": 3,
        "render": function(data, type, row) {
          return row.allRelatedTickets + "/" + row.allUsedTickets;
        }
      },{
        "targets": 4,
        "render": function(data, type, row) {
          var str = '已停用';
          if(row.IfValid == 1) str = '啟用中';
          return str;
        }
      },{
        "targets": 5,
        "render": function(data, type, row) {
          return "<div class=\"btn btn-sm btn-primary\" onclick=\"editData('" + row.id + "');\">修改</div>";
        }
      }, {
        "targets": 6,
        "render": function(data, type, row) {
          return "<div class=\"btn btn-sm btn-danger\" onclick=\"deleteData('" + row.id + "');\">刪除</div>";
        }
      }, {
        "targets": [1,2,3,4,5,6],
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
    location.href = "/Coupon/TypeAdd";
  }

  function editData(thidId){
    location.href = "/Coupon/TypeEdit/"+thidId;
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
          }else{
            alert('此類別正在使用中, 無法刪除');
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          // window.location.replace("/Admin/Login");
          //      alert(xhr.status);
          //      alert(xhr.statusText);
          //      alert(xhr.responseText);
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

