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
    loadType('#editCouponTypeID');

    //step 3: load edit data
    editData($('#editId').val());

    //event editExpireType
    $('#editExpireType').change(function(){
      console.log('editExpireType:'+this.value);
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
    $('#editValidStartDate').datetimepicker({
      format: 'YYYY-MM-DD',
      sideBySide: false
    });

    $('#editExpireDate').datetimepicker({
      format: 'YYYY-MM-DD',
      sideBySide: false
    });

    //autocomplete
    searchProduct();
  }
});

function sendEditData() {
  var isSuccess = true;
  var errorMessage = '';

  //check editIfTotalIssueLimitation
  console.log('editIfTotalIssueLimitation:'+$('#editForm input[name=editIfTotalIssueLimitation]').val());
  if(isSuccess &&
    (
      $('#editForm input:radio:checked[name="editIfTotalIssueLimitation"]:checked').val() == undefined ||
      $('#editForm input:radio:checked[name="editIfTotalIssueLimitation"]:checked').val() == '' )
    ) {
    errorMessage = '請勾選是否有總發行的數量上限';
    isSuccess = false;

    $('#editForm input[name=editIfTotalIssueLimitation]:checked').eq(0).focus();

  }else{
    if($('#editForm input:radio:checked[name="editIfTotalIssueLimitation"]:checked').val() == 1){
      if($('#editForm #editTotalIssueQuantity').val() ==  ''){
        errorMessage = '請輸入總發行的張數上限';
        isSuccess = false;

        $('#editForm #editTotalIssueQuantity').focus();
      }else{
        if(validateNumber($('#editForm #editTotalIssueQuantity').val())){
          if($('#editForm #editTotalIssueQuantity').val() == '0'){
            errorMessage = '總發行的張數上限請輸入大於0的整數數字';
            isSuccess = false;

            $('#editForm #editTotalIssueQuantity').focus();
          }
        }else{
          errorMessage = '總發行的張數上限請輸入整數數字';
          isSuccess = false;

          $('#editForm #editTotalIssueQuantity').focus();
        }
      }
    }
  }

  if(isSuccess){
    var expTypeOption = $('#editExpireType').val();
    console.log('expTypeOption:'+expTypeOption);//nothing to do
    switch(expTypeOption){
      case '0': break;//nothing to do
      case '99': break;//nothing to do
      case '1'://check editExpireDate
        if($('#editForm #editExpireDate').val() == ""){
          console.log('editExpireDate:使用期限的結束日期沒有輸入');//nothing to do
          errorMessage = '使用期限的結束日期沒有輸入';
          isSuccess = false;

          $('#editForm #editExpireDate').focus();
        }else{
          //check date valid
          isSuccess = validateDate($('#editForm #editExpireDate').val());//pass return true
          if(isSuccess) console.log('editExpireDate pass');
          else{
            console.log('editExpireDate:格式error');
              errorMessage = '使用期限的結束日期請輸入正確的日期格式(YYYY-MM-DD)';
              $('#editForm #editExpireDate').focus();
              isSuccess = false;
          }
        }
      break;
      case '2'://check editExpireDurations
        if($('#editForm #editExpireDurations').val() ==  '' ) {
          errorMessage = '請輸入計算過期日的單位時間';
          isSuccess = false;

          $('#editForm #editExpireDurations').focus();
          console.log('editExpireDurations:過期日的單位時間');
        }else{
          if(validateNumber($('#editForm #editExpireDurations').val())){
            if($('#editForm #editExpireDurations').val() == '0'){
              errorMessage = '有效期限的時間單位請輸入大於0的整數數字';
              isSuccess = false;

              $('#editForm #editExpireDurations').focus();
              console.log('editExpireDurations:請輸入有效期限的時間');
            }
          }else{
            errorMessage = '有效期限的時間單位請輸入整數數字';
            isSuccess = false;

            $('#editForm #editExpireDurations').focus();
            console.log('editExpireDurations:有效期限的時間不是整數數字');
          }
        }
      break;
      case '3'://check editExpireDurations
        if($('#editForm #editExpireDurations').val() ==  '' ) {
          errorMessage = '請輸入計算過期日的單位時間';
          isSuccess = false;

          $('#editForm #editExpireDurations').focus();

        }else{
          if(validateNumber($('#editForm #editExpireDurations').val())){
            if($('#editForm #editExpireDurations').val() == '0'){
              errorMessage = '有效期限的時間單位請輸入大於0的整數數字';
              isSuccess = false;

              $('#editForm #editExpireDurations').focus();
            }
          }else{
            errorMessage = '有效期限的時間單位請輸入整數數字';
            isSuccess = false;

            $('#editForm #editExpireDurations').focus();
          }
        }
      break;
      default:
      //defaut is set to 無過期時間
      break;
    }
  }

  // check editIfValid

  if(isSuccess && ($('#editForm input:radio:checked[name="editIfValid"]:checked').val() == undefined ||
          $('#editForm input:radio:checked[name="editIfValid"]:checked').val() == '' )
        ) {
    errorMessage = '請勾選是否啟用';
    isSuccess = false;

    $('#editForm input[name=editIfValid]:checked').eq(0).focus();

  }

  if (!isSuccess) {
    alert(errorMessage);
  } else {

    var formData = $("#editForm").serialize();

    $.ajax({
      url: THISAPI +'/'+$('#editId').val(),
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
            alert('總發行上限不可低於已發行數量');
            break;
          case 401:
            alert('沒有存取權限');
            window.location.replace("/Admin/Login");
            break;
          default:
            alert('無法修改資料');
        }
      },
      cache: false,
    });
  }
  return false;
}

function editData(thisId) {
  //get detail data
  $.ajax({
    url: THISAPI +'/'+ thisId,
    type: 'GET',
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.status == 1) {
        $('#editCouponCode').val(response.data.CouponCode);
        $('#editCouponName').val(response.data.CouponName);
        //
        $('#editCouponTypeID').val(response.data.CouponTypeID);
        //
        $('#editForm #editCreateBy').val(response.data.CreateBy);
        $('#editForm #editCreateDate').val(response.data.CreateDate);
        //
        $('#editForm #editTicketsUsed').val(response.data.TicketsUsed);
        $('#editForm #editTicketsCount').val(response.data.TicketsCount);
        $('#editForm #editTotalIssueQuantity').val(response.data.TotalIssueQuantity);
        $('#editForm #editIssueNum').val(response.data.IssueNum);
        //
        if (response.data.RedeemType == 1) {
          $('#editForm input[name=editRedeemType][value=1]').attr('checked', true);
        } else {
          $('#editForm input[name=editRedeemType][value=0]').attr('checked', true);
        }
        //select
        $('#editExpireType').val(response.data.ExpireType);
        switch(response.data.ExpireType){
          case '0':break;
          case '1':
            $('#editExpireDate').val(response.data.ValidDateTo);
            $('#ExpireDateArea').show();
          break;
          case '2':
            $('#editExpireDurations').val(response.data.ExpireDurations);
            $('#ExpireDurationsDateArea').show();
          break;
          case '3':
            $('#editExpireDurations').val(response.data.ExpireDurations);
            $('#ExpireDurationsDateArea').show();
          break;
          case '99':
            $('#editExpireDate').val(response.data.ValidDateTo);
            $('#ExpireDateArea').show();
          break;
        }
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

function searchProduct(){
  $('#searchProduct').autocomplete({
      source: function(request, response) {
        console.log('request.term:'+request.term);
          $.ajax({
              url: "/api/v1/ShopProduct?length=-1",
              type: 'GET',
              async: false,
              headers: {
                  'Authorization': Cookies.get('authToken')
              },
              data: {
                  searchAutoComplete: request.term
              },
              success: function(output) {
                  var outArr = new Array();
                  $.each(output.data, function(i, item) {
                    console.log('item.RelatedImage.RelatedImage1:'+item.RelatedImage.RelatedImage1);
                    var srcImg = '';//'files/shop/1vsAdvXUlizRW6a.jpg'
                    if(item.RelatedImage.RelatedImage1 == '') ;
                    else srcImg = item.RelatedImage.RelatedImage1;
                    var tempArr = {
                        id: item.id,
                        label: item.ProductName +'('+item.ProductCode+')',
                        value: item.ProductName,
                        src: srcImg
                    };
                    outArr.push(tempArr);
                  });

                  response(outArr);
              }
          });
      },
      minLength: 1,
      change: function(event, ui) {
          // $('#editProductBrandID').val('');
      },
      select: function(event, ui) {
          // $('#searchBrand').val(ui.item.label);
          var productObj = {
            id : ui.item.id,
            relatedId : '',
            productName : ui.item.value,
            src : ui.item.src
          };
          editProductSet(productObj, '#RelatedProductList');
      }
  });
}

function editProductSet(thisObj, tableId){
  //check thisId exist!
  if(checkProductIdExist(thisObj.id)) console.log('pass check');
  else{
    $('#searchResult').html(thisObj.productName+'已經在列表中!');
    return false;
  }
  var content = '<tr>';
  content += '<td id="'+thisObj.id+'" class="tdProductSetId col-md-4 col-xs-4">';
  content += '<input id="'+thisObj.id+'_relatedId" type="hidden" name="editRelatedId[]" value="'+thisObj.relatedId+'" />';
  content += '<input id="'+thisObj.id+'_IfValid" type="hidden" name="editProductIfValid[]" value="1"/>';
  content += '<div>'
  content += thisObj.productName;
  content += '</div>';
  content += '</td>';

  content +='<td class="col-md-4 col-xs-4">';
  if(thisObj.src == '') content += '無圖片';
  else content += '<img src="/Service/'+thisObj.src+'" style="max-width:100px">';// /Service/ + src will be equals ../../Service/ + src
  content +='</td>';

  // content += '<td class="col-md-4 col-xs-4">';
  // content += '<div class="btn btn-info" onclick="delProductSet(\''+thisObj.id+'\')">刪除</div>';
  // content += '</td>';

  content += '</tr>';
  $(tableId).append(content);//'#RelatedProductList'
}

function delProductSet(thisId){
  console.log('ProductSet Length: '+ $('#RelatedProductList .tdProductSetId').length);
  // if($('#RelatedProductList .tdProductSetId').length < 2){
  //   alert('無法刪除, 最少要輸入一筆商品資料');
  //   return false;
  // }
  console.log('thisId:'+thisId);
  var id = '#'+thisId;
  var relatedId = '#'+thisId+'_relatedId';
  var validId = '#'+thisId+'_IfValid';
  if($(relatedId).val() == ''){
    console.log('relatedId:'+$(relatedId).val()+',del ok');
    $(id).parent().remove();
  }else{
    $(validId).val(0);
    $(id).parent().hide();
    console.log('relatedId:'+$(relatedId).val()+',set to ' + $(validId).val());
  }
}

function checkProductIdExist(thisId){
  var list = $('.tdProductSetId');
  var check = true;
  $.each(list, function(){
    console.log($(this).attr('id'));

    if(thisId == $(this).attr('id')){
      check = false;
      //check
      var ifValidId = '#'+thisId+'_IfValid';
      var rowId = '#'+thisId;
      if($(ifValidId).val() == '0'){
        console.log('this Product exist, set ifValid = 1 and show');
        $(ifValidId).val(1);
        $(rowId).parent().show();
      }
      return false;
    }
  });
  return check;
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
