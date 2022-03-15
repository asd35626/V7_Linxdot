  var THISAPI = "/api/v1/ActivityBrand";


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

      //step 2 : 產生ContentArea
      loadCategory('#editCategoryId');
      editData($('#editTagID').val());

      //停用上一頁功能
      // backCheck();

      //載入所屬區域選單
      loadRegion('#editRegion');
      //選擇所屬區域帶出所屬地區
      $('#editRegion').on('change', function(){
        if($(this).val() == ''){
          //nothing to do
          resetArea('#editArea');
        }else{
          //載入所屬區域
          loadArea('#editArea', $(this).val());
        }
      });
    }
  });


  function returnToList() {
    window.history.back();
  }

  //show edit page
  function editData(thisId) {
    //initial
    // $('#list').hide();
    // $('#edit').show();
    // $('#add').hide();

    //get detail data
    $.ajax({
      url: THISAPI + "/" + thisId,
      type: 'GET',
      headers: {
        'Authorization': Cookies.get('authToken')
      },
      success: function(response) {
        if (response.status == 1) {
          $('#editId').val(response.data.id);
          $('#editBrandName').val(response.data.BrandName);
          $('#editBrandCode').val(response.data.BrandCode);
          $('#editWebTag').val(response.data.WebTag);
          $('#editBrandShortDescription').val(response.data.BrandShortDescription);
          //loadArea
          if(response.data.Region){
            loadRegion('#editRegion', response.data.Region);
            if(response.data.Area){
              loadArea('#editArea', response.data.Region, response.data.Area);
            }else loadArea('#editArea', response.data.Region);
          }else loadRegion('#editRegion');
          //image
          if(response.data.RelatedImage1){
            $('#editShowRelatedImage1').prepend('<img src="/Service/' + response.data.RelatedImage1 + '" />');
          }else{
            $('#editRelatedImage1').empty();
          }
          //
          $('#editCategoryId').val(response.data.CategoryId);
          $('#editCreateBy').val(response.data.CreateBy);
          $('#editCreateDate').val(response.data.CreateDate);
          if (response.data.IfShow == 1) {
            $('#edit input[name=editIfShow][value=1]').attr('checked', true);
          } else {
            $('#edit input[name=editIfShow][value=0]').attr('checked', true);
          }
          if (response.data.IfValid == 1) {
            $('#edit input[name=editIfValid][value=1]').attr('checked', true);
          } else {
            $('#edit input[name=editIfValid][value=0]').attr('checked', true);
          }
          //
          tinymce.get('editBrandDescription').setContent(response.data.BrandDescription);
          //
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
        returnToList();
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


  function resetEditData() {
    editData($('#editId').val());
  }

  function loadRegion(selectListId, regionId){
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
        $(selectListId).val(regionId);
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

  function loadArea(selectListId, searchID, areaId=''){
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
        if(areaId){
          $(selectListId).val(areaId);
        }
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
      url: "/api/v1/ActivityCategory?searchIfValid=1&length=-1",
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
