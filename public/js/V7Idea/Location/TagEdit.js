  var THISAPI = "/api/v1/LocationTag";

  $.ajaxPrefilter(function(options, originalOptions, jqXHR) {
    options.async = true;
  });


  $(document).ready(function() {
    // $('#add').hide();
    // $('#edit').hide();
    // $('#list').show();
    var id = Cookies.get('authToken');

    // console.log('id:'+id);
    if (id == undefined) {

      Cookies.remove('authToken');
      window.location.replace("/Admin/Login");
    } else {
      //step 1 : 產生MainMenu
      createMainMenu(id);

      //step 2 : 產生ContentArea
      editData($('#editTagID').val());

      //停用上一頁功能
      // backCheck();
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
          $('#editForm #editId').val(response.data.id);
          $('#editForm #editTagName').val(response.data.TagName);
          $('#editForm #editTagDescription').val(response.data.TagDescription);
          $('#editForm #editTagCode').val(response.data.TagCode);
          $('#editForm #editTagColor').val(response.data.TagColor);
          $('#editForm #editCreateBy').val(response.data.CreateBy);
          $('#editForm #editCreateDate').val(response.data.CreateDate);
          //radio
          if (response.data.IfValid == 1) {
            $('#editForm input[name=editIfValid][value=1]').attr('checked', true);
          } else {
            $('#editForm input[name=editIfValid][value=-1]').attr('checked', true);
          }
          if (response.data.IfShow == 1) {
            $('#editForm input[name=editIfShow][value=1]').attr('checked', true);
          } else {
            $('#editForm input[name=editIfShow][value=-1]').attr('checked', true);
          }
          if (response.data.IfShow == 1) {
            $('#editForm input[name=editIfShowOnSearchArea][value=1]').attr('checked', true);
          } else {
            $('#editForm input[name=editIfShowOnSearchArea][value=-1]').attr('checked', true);
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
    if (
      ($('#editForm #editTagName').val() == "") ||
      ($('#editForm #editTagCode').val() == "") ||
      ($('#editForm #editTagColor').val() == "") ||
      ($('#editForm input:radio:checked[name="editIfValid"]').val() == undefined)
    ) {
      alert("沒有輸入必要資料");
    } else {

      var formData = $("#editForm").serialize();
      $.ajax({
        url: THISAPI + "/" + $('#editId').val(),
        type: 'PUT',
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
