  var tagTable;
  var THISURLADD = "/Activity/TagAdd";
  var THISURLEDIT = "/Activity/TagEdit/";

  var THISAPI = "/api/v1/ActivityTag";
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

      //step 2 : 產生ContentArea
      tagTable = listTags();

      //停用上一頁功能
      // backCheck();
    }
  });

  //get listTags table
  function listTags() {
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
          d.searchTagName = $('#searchTagName').val();
          d.searchTagCode = $('#searchTagCode').val();
        }
      },
      "columns": [{
        "data": "TagName"
      }, {
        "data": "TagCode"
      }, {
        "data": "TagDescription"
      }, {
        "data": "TagColor"
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
      "lengthChange": false,
      "fnRowCallback": function(nRow, aData, iDisplayIndex) {
        $('td', nRow).attr('nowrap', 'nowrap');
        return nRow;
      },
    });

    return table;
  }

  //do list search
  function search() {
    tagTable.ajax.reload();
  }

  function toAdd() {
    window.location = THISURLADD;
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
            tagTable.ajax.reload();
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
