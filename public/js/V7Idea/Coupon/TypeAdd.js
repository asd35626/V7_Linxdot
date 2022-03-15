  var THISAPI = "/api/v1/ShopCouponType";


  $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    options.async = true;
  });


  $(document).ready(function() {

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
    }
  });

  function returnToList() {
    window.history.back();
  }


  function sendAddData() {
    var isSuccess = true;
    var errorMessage = '';

    //check addName

    if(isSuccess && ($('#addForm #addCouponTypeName').val() ==  '' )) {
      errorMessage = '請輸入名稱';
      isSuccess = false;

      $('#addForm #addCouponTypeName').focus();

    }

    //check addCode

    if(isSuccess && ($('#addForm #addCouponTypeCode').val() ==  '' )) {
      errorMessage = '請輸入代碼';
      isSuccess = false;

      $('#addForm #addCouponTypeCode').focus();

    }

    // check addIfValid

    if(isSuccess && ($('#addForm input:radio:checked[name="addIfValid"]').val() == undefined || $('#addForm input:radio:checked[name="addIfValid"]').val() == '' )) {
      errorMessage = '請勾選是否有效';
      isSuccess = false;

      $('#addForm input[name=addIfValid]').eq(0).focus();

    }



    if (!isSuccess) {
      alert(errorMessage);
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
              alert('類別已經存在');
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

