var THISAPI = "/api/v1/ShopCouponIssues";
var TYPEAPI = "/api/v1/ShopCouponType";

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

    //step 2 : loadType
    loadType('#addCouponTypeID');

    //event addExpireType
    $('#addExpireType').change(function(){
      console.log('addExpireType:'+this.value);
      initail();
      if(this.value == '0'){

      }else if(this.value == '1'){
        $('#ExpireDateArea').show();
      }else if(this.value == '2'){
        $('#ExpireDurationsDateArea').show();
      }else if(this.value == '3'){
        $('#ExpireDurationsDateArea').show();
      }else if(this.value == '99'){// 99

      }else ;
    });

    //datetimepicker
    $('#addValidStartDate').datetimepicker({
      format: 'YYYY-MM-DD',
      sideBySide: false
    });

    $('#addValidDateTo').datetimepicker({
      format: 'YYYY-MM-DD',
      sideBySide: false
    });

    //autocomplete
    searchCoupon();
  }
});


function sendAddData() {
  var isSuccess = true;
  var errorMessage = '';

  //check addCouponID

  if(isSuccess && ($('#addCouponID').val() ==  '' )) {
    console.log('addCouponID:'+$('#addCouponID').val());
    errorMessage = '請先輸入票卷種類名稱';
    isSuccess = false;

    $('#searchCoupon').focus();

  }

  // check addIssueNum
  console.log('#addIssueNum:'+$('#addIssueNum').val());
  if(isSuccess){
    if($('#addIssueNum').val() ==  '' ) {
      errorMessage = '請輸入發行的數量';
      isSuccess = false;

      $('#addIssueNum').focus();

    }else{
      if(validateNumber($('#addIssueNum').val())){
        if($('#addIssueNum').val() == '0'){
          errorMessage = '發行的數量請輸入大於0的整數數字';
          isSuccess = false;

          $('#addIssueNum').focus();
        }
      }else{
        errorMessage = '發行的數量請輸入整數數字';
        isSuccess = false;

        $('#addIssueNum').focus();
      }
    }
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
          case 403:
          //      alert(xhr.status);
          //      alert(xhr.statusText);
          var res = JSON.parse(xhr.responseText);
               alert(res.message);
               console.log(xhr.responseText);
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

function loadData(couponObj) {
    $('#addCouponCode').val(couponObj.CouponCode);
    $('#addCouponName').val(couponObj.CouponName);
    //
    $('#addCouponTypeID').val(couponObj.CouponTypeID);
    //
    $('#addTicketsUsed').val(couponObj.UsedCount);
    $('#addTicketsCount').val(couponObj.PulishCount);
    $('#addTotalIssueQuantity').val(couponObj.TotalIssueQuantity);
    $('#addIssueNum').val(couponObj.IssueNum);
    //
    if (couponObj.RedeemType == 1) {
      $('#addForm input[name=addRedeemType][value=1]').attr('checked', true);
    } else {
      $('#addForm input[name=addRedeemType][value=0]').attr('checked', true);
    }
    //select
    $('#addExpireType').val(couponObj.ExpireType);
    $('#addExpireDate').attr('disabled', true);
    switch(couponObj.ExpireType){
      case '0':break;
      case '1':
        $('#addValidDateTo').attr('disabled', true);
        $('#addValidDateTo').val(couponObj.ExpireDate);
        $('#ExpireDateArea').show();
      break;
      case '2':
        $('#addExpireDurations').val(couponObj.ExpireDurations);
        $('#addExpireDate').attr('disabled', false);
        $('#ExpireDurationsDateArea').show();
      break;
      case '3':
        $('#addExpireDurations').val(couponObj.ExpireDurations);
        $('#addExpireDate').attr('disabled', false);
        $('#ExpireDurationsDateArea').show();
      break;
      case '99':
        $('#addValidDateTo').attr('disabled', false);
        $('#ExpireDateArea').show();
      break;
    }
}

function initail(){
  $('#ValidStartDateArea').hide();
  $('#ExpireDurationsDateArea').hide();
  $('#ExpireDateArea').hide();
}

function returnToList() {
  window.history.back();
}


function loadType(selectListId){
  $.ajax({
    url: TYPEAPI + "?length=-1",
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
            text: obj.CouponTypeName + '(' + obj.CouponTypeCode +')'
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

function searchCoupon(){
  console.log('searchCoupon');
  $('#searchCoupon').autocomplete({
      source: function(request, response) {
        console.log('request.term:'+request.term);
          $.ajax({
              url: "/api/v1/ShopCoupon?length=-1",
              type: 'GET',
              async: false,
              headers: {
                  'Authorization': Cookies.get('authToken')
              },
              data: {
                  searchName: request.term
              },
              success: function(output) {
                  var outArr = new Array();
                  $.each(output.data, function(i, item) {

                    var tempArr = {
                        id: item.id,
                        label: item.CouponName +'('+item.CouponCode+')',
                        value: item.CouponName,
                        CouponName: item.CouponName,
                        CouponCode: item.CouponCode,
                        CouponTypeID: item.CouponTypeID,
                        RedeemType: item.RedeemType,
                        PulishCount: item.PulishCount,
                        UsedCount: item.UsedCount,
                        TotalIssueQuantity: item.TotalIssueQuantity,
                        ExpireType: item.ExpireType,
                        ExpireDurations: item.ExpireDurations,
                        ExpireDate: item.ExpireDate
                    };
                    outArr.push(tempArr);
                  });

                  response(outArr);
              }
          });
      },
      minLength: 1,
      change: function(event, ui) {
          // $('#addProductBrandID').val('');
      },
      select: function(event, ui) {
          $('#addCouponID').val(ui.item.id);
          var couponObj = {
            id : ui.item.id,
            CouponName: ui.item.CouponName,
            CouponCode: ui.item.CouponCode,
            CouponTypeID: ui.item.CouponTypeID,
            RedeemType: ui.item.RedeemType,
            PulishCount: ui.item.PulishCount,
            UsedCount: ui.item.UsedCount,
            TotalIssueQuantity: ui.item.TotalIssueQuantity,
            ExpireType: ui.item.ExpireType,
            ExpireDurations: ui.item.ExpireDurations,
            ExpireDate: ui.item.ExpireDate
          };
          loadData(couponObj);
      }
  });
}



//pass return true
function validateNumber(input){
  var format = /^[0-9]+$/;
   if (format.test(input)){
     return true;
   }
   return false;
}

  //pass return true
 function validateDate(inputDate){
   console.log(inputDate);
   var m = moment(inputDate.replace('/', '-'), 'YYYY-MM-DD', true);// format
   return m.isValid();
 }
