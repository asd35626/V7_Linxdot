  var THISAPI = "/api/v1/ShopCoupon";
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

      //event addRedeemType
      $('input:radio[name=addRedeemType]').change(function(){
        console.log('addRedeemType:'+this.value);
        if(this.value == '1'){
          $('#relatedProductArea').show();
        }else{
          $('#relatedProductArea').hide();
        }
      });

      //event addAwardLimited
      $('input:radio[name=addIfAwardLimited]').change(function(){
        console.log('addIfAwardLimited:'+this.value);
        if(this.value == '1'){
          $('#memberLimitation').css('visibility','visible');
        }else{
          $('#memberLimitation').css('visibility','hidden');
        }
      });

      //event addIfTotalIssueLimitation
      $('input:radio[name=addIfTotalIssueLimitation]').change(function(){
        console.log('addIfTotalIssueLimitation:'+this.value);
        if(this.value == '1'){
          $('#totalPublishLimited').css('visibility','visible');
        }else{
          $('#totalPublishLimited').css('visibility','hidden');
        }
      });

      //event addExpireType
      // $('input:radio[name=addExpireType]').change(function(){
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
        // defaultDate: moment(),
        // format: 'YYYY-MM-DD HH:mm:ss',
        format: 'YYYY-MM-DD',
        sideBySide: false
      });

      $('#addExpireDate').datetimepicker({
        // format: 'YYYY-MM-DD HH:mm:ss',
        format: 'YYYY-MM-DD',
        sideBySide: false
      });

      //autocomplete
      searchProduct();
    }
  });

  function initail(){
    $('#ValidStartDateArea').hide();
    $('#ExpireDurationsDateArea').hide();
    $('#ExpireDateArea').hide();
  }

  function returnToList() {
    window.history.back();
  }


  function sendAddData() {
    var isSuccess = true;
    var errorMessage = '';

    //check addCode
    console.log('addCouponCode:'+$('#addForm #addCouponCode').val());
    if(isSuccess && ($('#addForm #addCouponCode').val() ==  '' )) {
      errorMessage = '請輸入代碼';
      isSuccess = false;

      $('#addForm #addCouponCode').focus();

    }

    //check addName
    console.log('addCouponName:'+$('#addForm #addCouponName').val());
    if(isSuccess && ($('#addForm #addCouponName').val() ==  '' )) {
      errorMessage = '請輸入名稱';
      isSuccess = false;

      $('#addForm #addCouponName').focus();

    }

    //check addType
    console.log('addCouponTypeID:'+$('#addCouponTypeID').val());
    if(isSuccess && ($('#addCouponTypeID').val() == '' )){
      errorMessage = '請選擇電子票券類別';
      isSuccess = false;

      $('#addForm #addCouponTypeID').focus();
    }

    // check addDiscountAmount
    console.log('addDiscountAmount:'+$('#addForm #addDiscountAmount').val());
    if(isSuccess && ($('#addForm #addDiscountAmount').val() ==  '' )) {
      errorMessage = '請輸入折抵金額';
      isSuccess = false;

      $('#addForm #addDiscountAmount').focus();

    }else{
      if(validateNumber($('#addForm #addDiscountAmount').val())){
        if($('#addForm #addDiscountAmount').val() == '0'){
          errorMessage = '折抵金額請輸入大於0的整數數字';
          isSuccess = false;

          $('#addForm #addDiscountAmount').focus();
        }
      }else{
        errorMessage = '折抵金額請輸入整數數字';
        isSuccess = false;

        $('#addForm #addDiscountAmount').focus();
      }
    }

    //check addRedeemType
    console.log('addRedeemType:'+$('#addForm input[name=addRedeemType]:checked').val());
    if(isSuccess &&
      (
        $('#addForm input:radio:checked[name="addRedeemType"]:checked').val() == undefined ||
        $('#addForm input:radio:checked[name="addRedeemType"]:checked').val() == '' )
      ) {
      errorMessage = '請勾選是否有效';
      isSuccess = false;

      $('#addForm input[name=addRedeemType]:checked').eq(0).focus();

    }else{
      if($('#addForm input[name=addRedeemType]:checked').val() == 1){

        var pSetGroup = $('.tdProductSetId');
        if(pSetGroup.length == 0){
          errorMessage = '請確認是否有輸入關聯商品';
          isSuccess = false;
        }
      }
    }

    //check addIfAwardLimited
    console.log('addIfAwardLimited:'+$('#addForm input[name=addIfAwardLimited]').val());
    if(isSuccess &&
      (
        $('#addForm input:radio:checked[name="addIfAwardLimited"]:checked').val() == undefined ||
        $('#addForm input:radio:checked[name="addIfAwardLimited"]:checked').val() == '' )
      ) {
      errorMessage = '請勾選是否有取得限制';
      isSuccess = false;

      $('#addForm input[name=addIfAwardLimited]:checked').eq(0).focus();

    }else{
      if($('#addForm input:radio:checked[name="addAwardLimited"]:checked').val() == 1){
        if($('#addForm #addIfAwardLimitedNum').val() ==  ''){
          errorMessage = '請輸入每個會員限制張數';
          isSuccess = false;

          $('#addForm #addIfAwardLimitedNum').focus();
        }else{
          if(validateNumber($('#addForm #addIfAwardLimitedNum').val())){
            if($('#addForm #addIfAwardLimitedNum').val() == '0'){
              errorMessage = '每個會員限制張數請輸入大於0的整數數字';
              isSuccess = false;

              $('#addForm #addIfAwardLimitedNum').focus();
            }
          }else{
            errorMessage = '每個會員限制張數請輸入整數數字';
            isSuccess = false;

            $('#addForm #addIfAwardLimitedNum').focus();
          }
        }
      }
    }

    //check addIfTotalIssueLimitation
    console.log('addIfTotalIssueLimitation:'+$('#addForm input[name=addIfTotalIssueLimitation]').val());
    if(isSuccess &&
      (
        $('#addForm input:radio:checked[name="addIfTotalIssueLimitation"]:checked').val() == undefined ||
        $('#addForm input:radio:checked[name="addIfTotalIssueLimitation"]:checked').val() == '' )
      ) {
      errorMessage = '請勾選是否有總發行的數量上限';
      isSuccess = false;

      $('#addForm input[name=addIfTotalIssueLimitation]:checked').eq(0).focus();

    }else{
      if($('#addForm input:radio:checked[name="addIfTotalIssueLimitation"]:checked').val() == 1){
        if($('#addForm #addTotalIssueQuantity').val() ==  ''){
          errorMessage = '請輸入總發行的張數上限';
          isSuccess = false;

          $('#addForm #addTotalIssueQuantity').focus();
        }else{
          if(validateNumber($('#addForm #addTotalIssueQuantity').val())){
            if($('#addForm #addTotalIssueQuantity').val() == '0'){
              errorMessage = '總發行的張數上限請輸入大於0的整數數字';
              isSuccess = false;

              $('#addForm #addTotalIssueQuantity').focus();
            }
          }else{
            errorMessage = '總發行的張數上限請輸入整數數字';
            isSuccess = false;

            $('#addForm #addTotalIssueQuantity').focus();
          }
        }
      }
    }

    if(isSuccess){
      var expTypeOption = $('#addExpireType').val();
      console.log('expTypeOption:'+expTypeOption);//nothing to do
      switch(expTypeOption){
        case '0': break;//nothing to do
        case '99': break;//nothing to do
        case '1'://check addExpireDate
          if($('#addForm #addExpireDate').val() == ""){
            console.log('addExpireDate:使用期限的結束日期沒有輸入');//nothing to do
            errorMessage = '使用期限的結束日期沒有輸入';
            isSuccess = false;

            $('#addForm #addExpireDate').focus();
          }else{
            //check date valid
            isSuccess = validateDate($('#addForm #addExpireDate').val());//pass return true
            if(isSuccess) console.log('addExpireDate pass');
            else{
              console.log('addExpireDate:格式error');
                errorMessage = '使用期限的結束日期請輸入正確的日期格式(YYYY-MM-DD)';
                $('#addForm #addExpireDate').focus();
                isSuccess = false;
            }
          }
        break;
        case '2'://check addExpireDurations
          if($('#addForm #addExpireDurations').val() ==  '' ) {
            errorMessage = '請輸入計算過期日的單位時間';
            isSuccess = false;

            $('#addForm #addExpireDurations').focus();
            console.log('addExpireDurations:過期日的單位時間');
          }else{
            if(validateNumber($('#addForm #addExpireDurations').val())){
              if($('#addForm #addExpireDurations').val() == '0'){
                errorMessage = '有效期限的時間單位請輸入大於0的整數數字';
                isSuccess = false;

                $('#addForm #addExpireDurations').focus();
                console.log('addExpireDurations:請輸入有效期限的時間');
              }
            }else{
              errorMessage = '有效期限的時間單位請輸入整數數字';
              isSuccess = false;

              $('#addForm #addExpireDurations').focus();
              console.log('addExpireDurations:有效期限的時間不是整數數字');
            }
          }
        break;
        case '3'://check addExpireDurations
          if($('#addForm #addExpireDurations').val() ==  '' ) {
            errorMessage = '請輸入計算過期日的單位時間';
            isSuccess = false;

            $('#addForm #addExpireDurations').focus();

          }else{
            if(validateNumber($('#addForm #addExpireDurations').val())){
              if($('#addForm #addExpireDurations').val() == '0'){
                errorMessage = '有效期限的時間單位請輸入大於0的整數數字';
                isSuccess = false;

                $('#addForm #addExpireDurations').focus();
              }
            }else{
              errorMessage = '有效期限的時間單位請輸入整數數字';
              isSuccess = false;

              $('#addForm #addExpireDurations').focus();
            }
          }
        break;
        default:
        //defaut is set to 無過期時間
        break;
      }
    }

    // check addIfValid

    if(isSuccess && ($('#addForm input:radio:checked[name="addIfValid"]:checked').val() == undefined ||
            $('#addForm input:radio:checked[name="addIfValid"]:checked').val() == '' )
          ) {
      errorMessage = '請勾選是否啟用';
      isSuccess = false;

      $('#addForm input[name=addIfValid]:checked').eq(0).focus();

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
              alert(xhr.Text);
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
                      // console.log('item.RelatedImage.RelatedImage1:'+item.RelatedImage.RelatedImage1);
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
            // $('#addProductBrandID').val('');
        },
        select: function(event, ui) {
            // $('#searchBrand').val(ui.item.label);
            addProductSet(ui.item.id, '#RelatedProductList', ui.item.value, ui.item.src);
        }
    });
  }

  function addProductSet(thisId, tableId, productName, src){
    //check thisId exist!
    if(checkProductIdExist(thisId)) console.log('pass check');
    else{
      $('#searchResult').html(productName+'已經在列表中!');
      return false;
    }
    var content = '<tr>';
    content += '<td id="'+thisId+'" class="tdProductSetId col-md-4 col-xs-4">';
    content += '<div>'
    content += productName;
    content += '</div>';
    content += '</td>';

    content +='<td class="col-md-4 col-xs-4">';
    if(src == '') content += '無圖片';//content += '<img src="../Service/'+src+'" style="max-width:100px">';
    else content += '<img src="../Service/'+src+'" style="max-width:100px">';
    content += '<input type="hidden" name="addProductId[]" value="'+thisId+'" />';
    content +='</td>';

    content += '<td class="col-md-4 col-xs-4">';
    content += '<div class="btn btn-info" onclick="delProductSet(\''+thisId+'\')">刪除</div>';
    content += '</td>';

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
    $(id).parent().remove();
  }

  function checkProductIdExist(thisId){
    var list = $('.tdProductSetId');
    var check = true;
    $.each(list, function(){
      console.log($(this).attr('id'));

      if(thisId == $(this).attr('id')){
        check = false;
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
