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
      toAdd();

      //停用上一頁功能
      // backCheck();
    }
  });




  function toAdd() {
    $('#list').hide();
    $('#edit').hide();
    $('#add').show();
  }

  function returnToList() {
    window.history.back();
  }


  function sendAddData() {
    if (
      ($('#addForm #addTagName').val() == "") ||
      ($('#addForm #addTagCode').val() == "") ||
      ($('#addForm #addTagColor').val() == "") ||
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
              alert('標籤已經存在');
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
