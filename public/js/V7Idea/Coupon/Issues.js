  var table;
  var APIURL = '/api/v1/ShopCouponIssues';
  var PUBLISHAPI = '/api/v1/ShopCouponIssuesTickets';

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
        "data": "CouponCode"
      }, {
        "data": "CouponName"
      }, {
        "data": "IssueNum"
      }, {
        "data": "IssueDate"
      }, {
        "data": "data", //4. status
        "width": "8%"
      },{
        "width": "10%"
      }
      ],
      "columnDefs": [
        {
          "targets": 3,
          "render": function(data, type, row) {

              return row.IssueDate;
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
          var str = "<div class=\"btn btn-sm btn-info\" onclick=\"show('" + row.id + "');\">檢視</div>";
          if(row.TicketsCount == '0') str += "<div class=\"btn btn-sm btn-primary\" onclick=\"publish('" + row.id + "');\">發行</div>";
          return str;
        }
      }, {
        "targets": [1,2,3,4,5],
        "orderable": false
      }],
      "bSort": true, //排序
      "bFilter": false, //搜尋
      "lengthChange": false
    });

    return table;
  }

    function toAdd(){
      location.href = "/Coupon/IssuesAdd/";
    }

    //do list search
    function search() {
      table.ajax.reload();
    }

    function show(thisId){
      location.href = "/Coupon/IssuesDetail/"+thisId;
    }

    function publish(thisId){
      // ShopCouponIssuesTickets
      // 實際發行電子票券
      if(confirm('確定要發行電子票券嗎?')){
        $.ajax({
          url: PUBLISHAPI,
          type: 'POST',
          data: {"addSCIID": thisId},
          async: false,
          headers: {
            'Authorization': Cookies.get('authToken')
          },
          beforeSend: function(){
            alert('產生中請稍待!');
          },
          success: function(data) {
            if(data.status == '1'){
              alert('已產生'+data.data+'張電子票券');
            }else{
              alert('已產生電子票券出現異常情況');
            }
            table.ajax.reload();
          },
          error: function(xhr, ajaxOptions, thrownError) {
            switch (xhr.status) {
              case 422:
                alert('新增資料不完全');
                break;
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
          // contentType: false,
          // processData: false,
          dataType: "json",
        });
      }
    }
