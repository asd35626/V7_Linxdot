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

      //step 2 : 產生ContentArea
      editData($('#editId').val());

      //停用上一頁功能
      // backCheck();
    }
  });

  function returnToList() {
    window.history.back();
  }

  //show edit page
  function editData(thisId) {

    //get detail data
    $.ajax({
      url: THISAPI + "/" + thisId,
      type: 'GET',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {

          $('#editForm #editCouponTypeName').val(response.data.CouponTypeName);
          $('#editForm #editCouponTypeCode').val(response.data.CouponTypeCode);

          $('#editForm #editCreateBy').val(response.data.CreateBy);
          $('#editForm #editCreateDate').val(response.data.CreateDate);
          //radio
          if (response.data.IfValid == 1) {
            $('#editForm input[name=editIfValid][value=1]').attr('checked', true);
          } else {
            $('#editForm input[name=editIfValid][value=-1]').attr('checked', true);
          }
        }
      },
      error: function(xhr, ajaxOptions, thrownError) {

      },
      cache: false,
      contentType: false,
      processData: false
    });
  }


  function sendEditData() {
    var isSuccess = true;
    var errorMessage = '';

    //check Name

    if(isSuccess && ($('#editForm #editCouponTypeName').val() ==  '' )) {
      errorMessage = '請輸入名稱';
      isSuccess = false;

      $('#addForm #addCouponTypeName').focus();

    }

    //check Code

    if(isSuccess && ($('#editForm #editCouponTypeCode').val() ==  '' )) {
      errorMessage = '請輸入代碼';
      isSuccess = false;

      $('#editForm #editCouponTypeCode').focus();

    }

    // check IfValid

    if(isSuccess && ($('#editForm input:radio:checked[name="editIfValid"]').val() == undefined || $('#editForm input:radio:checked[name="editIfValid"]').val() == '' )) {
      errorMessage = '請勾選是否有效';
      isSuccess = false;

      $('#editForm input[name=editIfValid]').eq(0).focus();

    }

    if (!isSuccess) {
      alert(errorMessage);
    } else {

      var formData = $("#editForm").serialize();
      $.ajax({
        url:  THISAPI + "/" +  $('#editId').val(),
        type: 'PUT',
        data: formData,
        async: false,
        headers: {
          'Authorization': Cookies.get('authToken')
        },
        success: function(data) {
          window.history.back();
        },
        error: function(xhr, ajaxOptions, thrownError) {
          switch (xhr.status) {
            case 422:
              alert('修改資料不完全');
              break;
            case 409:
              alert('此代碼已經被使用, 無法變更');
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

    return false;
  }

  function resetEditData() {
    editData($('#editId').val());
  }
