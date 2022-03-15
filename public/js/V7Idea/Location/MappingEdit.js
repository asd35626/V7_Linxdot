
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

      //strp 2 : load location list
      loadLocationList('#editBrandID');

      //step 3 : load edit data
      editData($('#editId').val());
    }
  });


  function returnToList() {
    window.history.back();
  }

  function loadLocationList(selectListId) {
    $.ajax({
      url: "/api/v1/LocationBrand",
      type: 'GET',
      async: false,
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        $(selectListId).html('<option value="">請選擇</option>'); //reset select list
        $.each(response.data, function(index, obj) {
          // console.log('id:'+obj.id);
          // console.log('name'+ obj.FunctionName);
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

  //show edit page
  function editData(thisId) {
    //initial
    // console.log('load data:'+thisId);
    //get detail data
    $.ajax({
      url: "/api/v1/LocationMapping/" + thisId,
      type: 'GET',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {
          $('#editForm #editId').val(response.data.id);
          $('#editForm #editBrandID').val(response.data.LocationID);
          $('#editForm #editOnWebStartDate').val(response.data.OnWebStartDate);
          $('#editForm #editOnWebEndDate').val(response.data.OnWebEndDate);
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
    if (isSuccess && $('#editForm #editBrandID').val() == ""){
      isSuccess = false;
       errorMessage = '培訓與場館沒有選擇';
       $('#editForm #editBrandID').focus();
    }

    if(isSuccess){
      if($('#editOnWebStartDate').val() == ''){
        isSuccess = false;
        errorMessage = "請輸入上架時間";

        $('#editOnWebStartDate').focus();
      }else{
        isSuccess = validateDate($('#editOnWebStartDate').val());

        if(isSuccess) console.log('OnWebStartDate pass');
        else{
          errorMessage = '上架日期請輸入正確的日期格式(YYYY-MM-DD)';
          $('#editOnWebStartDate').focus();
        }
      }
    }

    if(isSuccess){
      if($('#editOnWebEndDate').val() == ''){
        isSuccess = false;
        errorMessage = "請輸入上架時間";

        $('#editOnWebEndDate').focus();
      }else{
        isSuccess = validateDate($('#editOnWebEndDate').val());

        if(isSuccess) console.log('OnWebEndDate pass');
        else{
          errorMessage = '下架日期請輸入正確的日期格式(YYYY-MM-DD)';
          $('#editOnWebEndDate').focus();
        }
      }
    }

    if(isSuccess){

      if(
        ($('#editForm input:radio:checked[name="editIfValid"]').val() == undefined) ||
        ($('#editForm input:radio:checked[name="editIfValid"]').val() == '')
      ) {
        errorMessage = '是否啟用沒有選擇';
        isSuccess = false;

        $('#editForm input[name=editIfValid]').eq(0).focus();

      }
    }

    if(!isSuccess){
      alert(errorMessage);
    }else{
      var formData = $("#editForm").serialize();
      $.ajax({
        url: "/api/v1/LocationMapping/" + $('#editId').val(),
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

    return false;
  }

  function resetEditData() {
    editData($('#editId').val());
  }
  //pass return true
 function validateDate(inputDate){
   console.log(inputDate);
   var m = moment(inputDate.replace('/', '-'), 'YYYY-MM-DD', true);// format
   return m.isValid();
 }
