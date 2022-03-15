
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

      //strp 2 : load Activiry list
      loadActiviryList('#addBrandID');
    }
  });


  function returnToList() {
    window.history.back();
  }



  function sendAddData() {
    var isSuccess = true;
    var errorMessage = '';

    if($('#addBrandID').val() == ''){
        isSuccess = false;
        errorMessage = "請選擇要對應的培訓與場館";

        $('#addBrandID').focus();
    }

    if(isSuccess){
      if($('#addOnWebStartDate').val() == ''){
        isSuccess = false;
        errorMessage = "請輸入上架時間";

        $('#addOnWebStartDate').focus();
      }else{
        isSuccess = validateDate($('#addOnWebStartDate').val());

        if(isSuccess) console.log('OnWebStartDate pass');
        else{
          errorMessage = '上架日期請輸入正確的日期格式(YYYY-MM-DD)';
          $('#addOnWebStartDate').focus();
        }
      }
    }

    if(isSuccess){
      if($('#addOnWebEndDate').val() == ''){
        isSuccess = false;
        errorMessage = "請輸入下架時間";

        $('#addOnWebEndDate').focus();
      }else{
        isSuccess = validateDate($('#addOnWebEndDate').val());

        if(isSuccess) console.log('OnWebEndDate pass');
        else{
          errorMessage = '下架日期請輸入正確的日期格式(YYYY-MM-DD)';
          $('#addOnWebEndDate').focus();
        }
      }
    }

    if(isSuccess){

      if(
        ($('#addForm input:radio:checked[name="addIfValid"]').val() == undefined) ||
        ($('#addForm input:radio:checked[name="addIfValid"]').val() == '')
      ) {
        errorMessage = '商品建檔歸屬部門沒有選擇';
        isSuccess = false;

        $('#addForm input[name=addIfValid]').eq(0).focus();

      }
    }

    if (!isSuccess){
      alert(errorMessage);
    } else {
      //check code
      var formData = new FormData($("#addForm")[0]);

      $.ajax({
        url: "/api/v1/ActivityMapping/",
        type: 'POST',
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
              alert('新增資料不完全');
              break;
            case 409:
              alert('該對應已經存在');
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


  function loadActiviryList(selectListId) {
    $.ajax({
      url: "/api/v1/ActivityBrand",
      type: 'GET',
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        $(selectListId).html('<option value="">請選擇</option>'); //reset select list
        $.each(response.data, function(index, obj) {
          console.log('id:'+obj.id);
          console.log('name'+ obj.BrandName);
          if ((obj.IfValid == 1) && (obj.IfDelete == 0)) {
            $(selectListId).append($('<option>', {
              value: obj.id,
              text: obj.BrandName
            }));
          }
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

  //pass return true
 function validateDate(inputDate){
   console.log(inputDate);
   var m = moment(inputDate.replace('/', '-'), 'YYYY-MM-DD', true);// format
   return m.isValid();
 }
