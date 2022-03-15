  var shopTable;
  var locationTable;
  var activityTable;

  $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    options.async = true;
  });
  var ProductSetId = 1;//組合商品List id
  var ProductSpecId = 1;//商品規格List id

  $(document).ready(function() {
    $('#list').show();
    $('#listLocations').show();
    $('#listActivities').show();

    var id = Cookies.get('authToken');

    // console.log('id:'+id);
    if (id == undefined) {

      Cookies.remove('authToken');
      window.location.replace("/Admin/Login");
    } else {
      //step 1 : 產生MainMenu
      createMainMenu(id);

      //step 2 : 產生ContentArea
      shopTable = listShopProducts();
      locationTable = listLocations();
      activityTable = listActivities();
    }
  });

  //get listShopProducts table
  function listShopProducts() {
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
        "url": "/api/v1/ShopProduct",
        "type": "POST",
        "beforeSend": function(request) {
          request.setRequestHeader("Authorization", Cookies.get('authToken'));
        },
        "data": function(d) {
          d.action = "list";
          d.searchProductName = $('#searchProductName').val();
          d.searchProductCode = $('#searchProductCode').val();
        }
      },
      "columns": [{
        "data": "ProductStatus","width": "8%" //0
      }, {
        "data": "function","width": "12%" //1 上下架/商品代碼
      }, {
        "data": "ProductName","width": "14%" //2
      }, {
        "data": "ListPrice","width": "7.5%" //3 波力網路價
      }, {
        "data": "Cost","width": "7.5%" //4 成本價
      }, {
        "data": "Profit","width": "7%" //5 毛利
      }, {
        "data": "MaxInventory/SafeInventory","width": "8.5%","min-width":"63px" //6 庫存
      }, {
        "data": "function","width": "10%" // 7 廠商名稱/編號
      }, {
        "data": "SupplierDepartment","width": "7%" // 8 部門
      }, {
        "data": "function","width": "8%" // 9 MD
      }, {
        "data": "function","width": "10%" // 10 最後更新
      }, {
        "data": "function","width": "10%" // 11 管理
      }],
      "columnDefs": [
      {
        "targets": 1,
        "render": function(data, type, row) {
          var str = row.ProductCode + "<br>";
          str += "<red>"+row.OnWebStartDate + "</red>~<br><red>" + row.OnWebEndDate+"</red>";
          return str;
        }
      }, {
        "targets": 5,
        "render": function(data, type, row) {
          var str = row.ListPrice - row.Cost;
          return str;
        }
      }, {
        "targets": 6,
        "render": function(data, type, row) {
          var str = "";
          str = row.MaxInventory + "/" + row.SafeInventory;
          return str;
        }
      }, {
        "targets": 7,
        "render": function(data, type, row) {
          var str = "";
          str = row.SupplierName + "/<br>" + row.SupplierMemberNo;
          return str;
        }
      }, {
        "targets": 9,
        "render": function(data, type, row) {
          var str = "無資料";
          return str;
        }
      }, {
        "targets": 10,
        "render": function(data, type, row) {
          var str = "";
          str = row.LastModifiedDate + "/<br>" + row.LastModifiedBy;
          // str = row.LastModifiedDate;
          return str;
        }
      }, {
        "targets": 11,
        "render": function(data, type, row) {
          var str = "<div class=\"btn btn-sm btn-primary\" onclick=\"editData('" + row.id + "');\">內容管理</div>";
          str += "<div class=\"btn btn-sm btn-primary\" onclick=\"showLocations('" + row.id + "');\">場館管理</div>";
          str += "<div class=\"btn btn-sm btn-primary\" onclick=\"showActivities('" + row.id + "');\">賽事管理</div>";
          return str;
        }
      }],
      "bSort": false, //排序
      "bFilter": false, //搜尋
      "lengthChange": false,
    });

    return table;
  }

  //do list search
  function search() {
    shopTable.ajax.reload();
  }

  function toAdd() {
    location.href = "/Shop/ProductAdd";
  }

  //show edit page
  function editData(thisId) {
    location.href = "/Shop/ProductEdit/" + thisId;
  }

  function listLocations(){

    var table = $('#grid-basic-location').DataTable({
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
        "url": "/api/v1/LocationMapping",
        "type": "POST",
        "beforeSend": function(request) {
          request.setRequestHeader("Authorization", Cookies.get('authToken'));
        },
        "data": function(d) {
          d.action = "list";
          d.length = '-1';
          d.searchProductID = $('#locationProductId').val();
        }
      },
      "columns": [{
        "data": "BrandCode","width": "8%" //0 場館代碼
      }, {
        "data": "BrandName","width": "12%" //1 場館名稱
      }, {
        "data": "OnWebStartDate","width": "14%" //2上架日期
      }, {
        "data": "OnWebEndDate","width": "7.5%" //3 下架日期
      }, {
        "data": "status","width": "7.5%" //4 狀態
      }, {
        "data": "function","width": "7%" //5 功能
      }],
      "columnDefs": [
      {
        "targets": 4,
        "render": function(data, type, row) {
          if(row.IfValid == 1) str = "啟用中"
          else str = "停用中";
          return str;
        }
      }, {
        "targets": 5,
        "render": function(data, type, row) {
          var str = "";
          str += "<div class=\"btn btn-sm btn-primary\" onclick=\"toEditMapping('location','" + row.id + "');\">修改</div>";
          if(row.Ifvalid == 0) str += "<div class=\"btn btn-sm btn-primary\" onclick=\"deleteLocationMapping('" + row.id + "');\">立即下架</div>";
          return str;
        }
      }],
      "bSort": false, //排序
      "bFilter": false, //搜尋
      "lengthChange": false,
    });

    return table;
  }

  function listActivities(){

    var table = $('#grid-basic-activity').DataTable({
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
        "url": "/api/v1/ActivityMapping",
        "type": "POST",
        "beforeSend": function(request) {
          request.setRequestHeader("Authorization", Cookies.get('authToken'));
        },
        "data": function(d) {
          d.action = "list";
          d.length = '-1';
          d.searchProductID = $('#activityProductId').val();
        }
      },
      "columns": [{
        "data": "BrandCode","width": "8%" //0 場館代碼
      }, {
        "data": "BrandName","width": "12%" //1 場館名稱
      }, {
        "data": "OnWebStartDate","width": "14%" //2上架日期
      }, {
        "data": "OnWebEndDate","width": "7.5%" //3 下架日期
      }, {
        "data": "status","width": "7.5%" //4 狀態
      }, {
        "data": "function","width": "7%" //5 功能
      }],
      "columnDefs": [
      {
        "targets": 4,
        "render": function(data, type, row) {
          if(row.IfValid == 1) str = "啟用中"
          else str = "停用中";
          return str;
        }
      }, {
        "targets": 5,
        "render": function(data, type, row) {
          var str = "";
          str += "<div class=\"btn btn-sm btn-primary\" onclick=\"toEditMapping('activity','" + row.id + "');\">修改</div>";
          if(row.Ifvalid == 0) str += "<div class=\"btn btn-sm btn-primary\" onclick=\"deleteActivityMapping('" + row.id + "');\">立即下架</div>";
          return str;
        }
      }],
      "bSort": false, //排序
      "bFilter": false, //搜尋
      "lengthChange": false,
    });

    return table;
  }

  function showLocations(thisId){
    $('#locationList').show();
    $('#activityList').hide();
    console.log(thisId);
    $('#locationProductId').val(thisId);
    locationTable.ajax.reload();
  }

  function showActivities(thisId){
    $('#locationList').hide();
    $('#activityList').show();
    console.log(thisId);
    $('#activityProductId').val(thisId);
    activityTable.ajax.reload();
  }

  function toAddMapping(option){
    var id = '';
    switch(option){
      case 'location':
        location.href = "/Location/MappingAdd/" + $('#locationProductId').val();
      break;
      case 'activity':
        location.href = "/Activity/MappingAdd/" + $('#activityProductId').val();
      break;
      case 'delete':
      console.log(option + ':' + id);
      break;
    }
  }

  function toEditMapping(option, thisId){

    switch(option){
      case 'location':
        location.href = "/Location/MappingEdit/" + thisId;
      break;
      case 'activity':
        location.href = "/Activity/MappingEdit/" + thisId;
      break;
    }
  }


  function deleteLocationMapping(thisId){
    if(confirm('確定要直接下架嗎?')){
      $.ajax({
        url: "/api/v1/LocationMapping/" + thisId,
        type: 'DELETE',
        async: false,
        headers: {
          'Authorization': Cookies.get('authToken')
        },
        success: function(data) {
          locationTable.ajax.reload();
        },
        error: function(xhr, ajaxOptions, thrownError) {
          switch (xhr.status) {
            case 422:
              alert('修改資料不完全');
              break;
            case 409:
              alert('此對應已經存在, 無法變更');
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
  }

  function deleteActivityMapping(thisId){
    if(confirm('確定要直接下架嗎?')){
      $.ajax({
        url: "/api/v1/ActivityMapping/" + thisId,
        type: 'DELETE',
        async: false,
        headers: {
          'Authorization': Cookies.get('authToken')
        },
        success: function(data) {
          activityTable.ajax.reload();
        },
        error: function(xhr, ajaxOptions, thrownError) {
          switch (xhr.status) {
            case 422:
              alert('修改資料不完全');
              break;
            case 409:
              alert('此對應已經存在, 無法變更');
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
  }
