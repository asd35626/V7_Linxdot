  var THISAPI = "/api/v1/LocationBrand";


  $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    options.async = true;
  });


  $(document).ready(function() {
    // $('#add').hide();
    // $('#edit').hide();

    var id = Cookies.get('authToken');

    // console.log('id:'+id);
    if (id == undefined) {

      Cookies.remove('authToken');
      window.location.replace("/Admin/Login");
    } else {
      //step 1 : 產生MainMenu
      createMainMenu(id);

      // toAdd();

      //停用上一頁功能
      // backCheck();
      //載入分類
      loadCategory('#addCategoryId');
      //載入所屬區域選單
      loadRegion('#addRegion');
      //選擇所屬區域帶出所屬地區
      $('#addRegion').on('change', function(){
        if($(this).val() == ''){
          //nothing to do
          resetArea('#addArea');
        }else{
          //載入所屬區域
          loadArea('#addArea', $(this).val());
        }
      });
    }
  });


  //do list search
  function search() {
    ajaxTable.ajax.reload();
  }

  function toAdd() {
    $('#list').hide();
    $('#edit').hide();
    $('#add').show();
  }

  function returnToList() {
    window.history.back();
  }


  function sendAddData() {
    tinyMCE.triggerSave();

    if (
      ($('#addBrandName').val() == "") ||
      ($('#addBrandCode').val() == "") ||
      ($('#addBrandDescription').val() == "") ||
      ($('#addForm input:radio:checked[name="addIfShow"]').val() == undefined) ||
      ($('#addForm input:radio:checked[name="addIfValid"]').val() == undefined)
    ) {
      alert("沒有輸入必要資料");
    } else {
      var formData = new FormData($("#addForm")[0]);

      $.ajax({
        url: THISAPI,
        type: 'POST',
        data: formData,
        async: false,
        headers: {
          'Authorization': Cookies.get('authToken')
        },
        success: function(data) {
          returnToList();
        },
        error: function(xhr, ajaxOptions, thrownError) {
          switch (xhr.status) {
            case 422:
              alert('新增資料不完全');
              break;
            case 409:
              alert('編號已經存在');
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
    tinyMCE.triggerSave();

    // var formData = $("#editForm").serialize();
    var formData = new FormData($("#editForm")[0]);


    $.ajax({
      url: THISAPI + "/" + $('#editId').val(),
      type: 'POST',
      data: formData,
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(data) {
        $("#edit").hide();

        ajaxTable.ajax.reload();

        $("#list").show();
      },
      error: function(xhr, ajaxOptions, thrownError) {
        switch (xhr.status) {
          case 422:
            alert('修改資料不完全');
            break;
          case 409:
            alert('名稱重複');
            break;
          case 401:
            alert('Token 不存在');
            window.location.replace("/Admin/Login");
            break;
          default:
            alert('無法修改資料');
        }
      },
      cache: false,
      contentType: false,
      processData: false
    });
    return false;
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


  function resetEditData() {
    editData($('#editId').val());
  }

  function loadRegion(selectListId){
    $.ajax({
      url: "/api/v1/StatusDef?length=-1&searchKine=Region",
      type: 'GET',
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        $(selectListId).html('<option value="">請選擇</option>'); //reset select list
        $.each(response.data, function(index, obj) {
            $(selectListId).append($('<option>', {
              value: obj.StatusID,
              text: obj.StatusName
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

  function loadArea(selectListId, searchID){
    $.ajax({
      url: "/api/v1/StatusDef?length=-1&searchKine=Area&searchID="+searchID,
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
              text: obj.StatusName
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

  function resetArea(selectListId){
    $(selectListId).html('<option value="">請選擇</option>'); //reset select list
  }

  function loadCategory(categoryId){
    $.ajax({
      url: "/api/v1/LocationCategory?searchIfValid=1&length=-1",
      type: 'GET',
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        $(categoryId).html('<option value="">請選擇</option>'); //reset select list
        $.each(response.data, function(index, obj) {
            $(categoryId).append($('<option>', {
              value: obj.id,
              text: obj.CategoryName
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
