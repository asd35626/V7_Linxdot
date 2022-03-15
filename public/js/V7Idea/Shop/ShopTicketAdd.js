// var shopTable;
$.ajaxPrefilter(function(options, originalOptions, jqXHR) {
  options.async = true;
});
var ProductSetId = 1;//組合商品List id
var ProductSpecId = 1;//商品規格List id

$(document).ready(function() {

  var id = Cookies.get('authToken');

  // console.log('id:'+id);
  if (id == undefined) {

    Cookies.remove('authToken');
    window.location.replace("/Admin/Login");
  } else {
    //step 1 : 產生MainMenu
    createMainMenu(id);

    // Get ID

    // getNavigationBar($('#searchParentCategoryID').val());


    //step 2 : 產生ContentArea
    toAdd();

    //載入品牌資料
    loadBrand('#addProductBrand');
    //停用上一頁功能
    // backCheck();

    //商品分類下拉選單, 選擇載入 start
    $('#addProductCategoryList_1').on('change',function(){
      // console.log($('#addProductCategoryList_1').val());
      //set addShopCategoryID
      $('#addShopCategoryID').val($('#addProductCategoryList_1').val());
      //update list_2
      if($('#addProductCategoryList_1').val() == ""){
        //reset 2 & hide
        $('#addProductCategoryList_2').html('<option value="">請選擇</option>').hide();

      }else{
        loadCategoryList($('#addProductCategoryList_1').val(), '#addProductCategoryList_2');
      }
      //reset/hide  list_3 & list 4
      $('#addProductCategoryList_3').html('<option value="">請選擇</option>').hide();
      $('#addProductCategoryList_4').html('<option value="">請選擇</option>').hide();
    });

    $('#addProductCategoryList_2').on('change',function(){
      if($('#addProductCategoryList_2').val() == ""){
        //reset list_3 & list 4
        $('#addProductCategoryList_3').html('<option value="">請選擇</option>').hide();
        $('#addProductCategoryList_4').html('<option value="">請選擇</option>').hide();
        //set form list_1
        $('#addShopCategoryID').val($('#addProductCategoryList_1').val());
      }else {
        //set addShopCategoryID
        $('#addShopCategoryID').val($('#addProductCategoryList_2').val());
        //update list_3
        loadCategoryList($('#addProductCategoryList_2').val(), '#addProductCategoryList_3');
        //reset  list 4
        $('#addProductCategoryList_4').html('<option value="">請選擇</option>').hide();
      }
    });

    $('#addProductCategoryList_3').on('change',function(){
      if($('#addProductCategoryList_3').val() == ""){
        //reset 4
        $('#addProductCategoryList_4').html('<option value="">請選擇</option>').hide();
        //set from 2
        $('#addShopCategoryID').val($('#addProductCategoryList_2').val());
      }else {
        //set addShopCategoryID
        $('#addShopCategoryID').val($('#addProductCategoryList_3').val());
        //update list_3
        if($('#addProductCategoryList_3').val() == "") ;
        else loadCategoryList($('#addProductCategoryList_3').val(), '#addProductCategoryList_4');
      }
    });

    $('#addProductCategoryList_4').on('change',function(){
      if($('#addProductCategoryList_3').val() == ""){
        //set from 3
        $('#addShopCategoryID').val($('#addProductCategoryList_3').val());
      }else {
        //set addShopCategoryID
        $('#addShopCategoryID').val($('#addProductCategoryList_4').val());
      }
    });
    //商品分類下拉選單, 選擇載入 end
    //是否贈與點數
    $('input:radio[name="addIfRewardPoints"]').change(function() {
        console.log('addRewardPoints:'+$(this).val());
        if($(this).val() == 1) $('#addRewardPointsArea').css('visibility','visible');
        if($(this).val() == -1) $('#addRewardPointsArea').css('visibility','hidden');
    });

  }
  $('#addDeliverDate').datepicker({dateFormat: "yy-mm-dd"});
  $('#addOnWebStartDate').datepicker({dateFormat: "yy-mm-dd"});
  $('#addOnWebEndDate').datepicker({dateFormat: "yy-mm-dd",minDate: 0});
});

function toAdd() {
  // $('#list').hide();
  // $('#edit').hide();
  // $('#add').show();
  //
  $('#addForm')[0].reset();
  loadDepartmentList('#addProductDepartmentList');
  loadCategoryList('','#addProductCategoryList_1');

  //autocomplete
  searchSupplier();
  searchBrand();
}

function returnToList() {
  // $('#list').show();
  // $('#edit').hide();
  // $('#add').hide();
  window.history.back();
}

function sendAddData() {

  var isSuccess = true;
  var errorMessage = '';

  // check DepartmentID

  if(isSuccess && ($('#addForm input:radio:checked[name="addDepartmentID"]').val() == undefined || $('#addForm input:radio:checked[name="addDepartmentID"]').val() == '' )) {
    errorMessage = '商品建檔歸屬部門沒有選擇';
    isSuccess = false;

    $('#addForm input[name=addDepartmentID]').eq(0).focus();

  }

  // CHECK #addForm #addSupplierID

  if(isSuccess && $('#addForm #addSupplierID').val() == "" ) {
    errorMessage = '沒有選擇供應商';
    isSuccess = false;

    $('#addForm #searchSupplier').focus();
  }


  //Check addProductName
  if(isSuccess && $('#addForm #addProductName').val() == ""){
      errorMessage = '商品名稱沒有輸入';
      isSuccess = false;

      $('#addProductName').focus();
  }

  //Check addShortDescription
  if(isSuccess && $('#addForm #addShortDescription').val() == ""){
    errorMessage = '商品促銷標語沒有輸入';
    isSuccess = false;

    $('#addForm #addShortDescription').focus();
  }

  //Check addBrand
  if(isSuccess && $('#addForm #addProductBrandID').val() == ""){
    errorMessage = '商品品牌沒有輸入';
    isSuccess = false;

    $('#addForm #searchBrand').focus();
  }

  //Check addProductType
  if(isSuccess && $('#addForm input:radio:checked[name="addProductType"]').val() == undefined){
      errorMessage = '商品類型沒有選擇';
      isSuccess = false;

      $('#addForm input[name=addProductType]').eq(0).focus();
  }else{
    if($('#addForm input:radio:checked[name="addProductType"]').val() == 3){
      //check
       if($('#addDeliverDate').val() == ""){
         errorMessage = '預計出貨日期沒有輸入';
         isSuccess = false;

         $('#addDeliverDate').focus();
       }else{
         //check date valid
         isSuccess = validateDate($('#addDeliverDate').val());//pass return true
         if(isSuccess) console.log('OnWebStartDate pass');
         else{
             errorMessage = '預計出貨日期請輸入正確的日期格式(YYYY-MM-DD)';
             $('#addDeliverDate').focus();
        }
      }
    }
  }

  //Check addOnWebStartDate
  if(isSuccess){
    if($('#addForm #addOnWebStartDate').val() == ""){
      errorMessage = '上架日期沒有輸入';
      isSuccess = false;

      $('#addForm #addOnWebStartDate').focus();
    }else{
      //check date valid
      isSuccess = validateDate($('#addForm #addOnWebStartDate').val());//pass return true
      if(isSuccess) console.log('OnWebStartDate pass');
      else{
          errorMessage = '上架日期請輸入正確的日期格式(YYYY-MM-DD)';
          $('#addForm #addOnWebStartDate').focus();
      }
    }
  }

  //Check addOnWebEndDate
  if(isSuccess){
    if($('#addForm #addOnWebEndDate').val() == ""){
      errorMessage = '下架日期沒有輸入';
      isSuccess = false;

      $('#addForm #addOnWebEndDate').focus();
    }else{
      //check date valid
      isSuccess = validateDate($('#addForm #addOnWebEndDate').val());//pass return true
      if(isSuccess) console.log('OnWebEndDate pass');
      else{
          errorMessage = '下架日期請輸入正確的日期格式(YYYY-MM-DD)';
          $('#addForm #addOnWebEndDate').focus();
      }
    }
  }

  //Check addShopCategoryID
  if(isSuccess && $('#addForm #addShopCategoryID').val() == ""){
    errorMessage = '商品分類沒有選擇';
    isSuccess = false;

    $('#addProductCategoryList_1').focus();
    console.log('l_1:'+document.getElementById("addProductCategoryList_1").length);
    console.log('l_2:'+document.getElementById("addProductCategoryList_2").length);
    console.log('l_3:'+document.getElementById("addProductCategoryList_3").length);
    console.log('l_4:'+document.getElementById("addProductCategoryList_4").length);
  }

  //Check addRefPrice
  if(isSuccess){
    if($('#addForm #addRefPrice').val() == ""){
      errorMessage = '商品市價沒有輸入';
      isSuccess = false;

      $('#addForm #addRefPrice').focus();
    }else{
      //Check Number
      if(validateNumber($('#addForm #addRefPrice').val())) ;//pass
      else {
        errorMessage = '商品市價請輸入整數數字';
        isSuccess = false;

        $('#addForm #addRefPrice').focus();
      }
    }
  }

  //Check addCost
  if(isSuccess){
    if($('#addForm #addCost').val() == ""){
      errorMessage = '成本價沒有輸入';
      isSuccess = false;

      $('#addForm #addCost').focus();
    }else{
      //Check Number
      if(validateNumber($('#addForm #addCost').val())) ;//pass
      else {
        errorMessage = '成本價請輸入整數數字';
        isSuccess = false;

        $('#addForm #addCost').focus();
      }
    }
  }


  //Check addProductMaterial
  if(isSuccess && $('#addForm #addProductMaterial').val() == ""){
    errorMessage = '商品材質沒有輸入';
    isSuccess = false;

    $('#addForm #addProductMaterial').focus();
  }

  //Check addProductUnit
  if(isSuccess && $('#addForm #addProductUnit').val() == ""){
    errorMessage = '商品單位沒有輸入';
    isSuccess = false;

    $('#addForm #addProductUnit').focus();
  }

  //Check ProductSet
  if(isSuccess && $('#addForm #ProductSetList .tdProductSetId').length < 1){
    errorMessage = '商品組合至少要有一筆資料';
    isSuccess = false;

    $('#addForm #addProductUnit').focus();
  }
  //Check ProductSet SubList
  if(isSuccess){
    var chk = checkProductSet();
    if(chk === true) ;
    else{
      isSuccess = false;
      errorMessage = chk;
    }
  }

  //Check ProductSpec
  if(isSuccess && $('#addForm #ProductSpecList .tdProductSpecId').length < 1){
    errorMessage = '商品規格至少要有一筆資料';
    isSuccess = false;

  }

  //Check addProductSpecType1
  if(isSuccess && $('#addForm #addProductSpecType1').val() == ""){
    errorMessage = '第一層規格名稱沒有輸入';
    isSuccess = false;

    $('#addForm #addProductSpecType1').focus();
  }

  if(isSuccess){
    var chk = checkProductSpec();
    if(chk === true) ;
    else{
      isSuccess = false;
      errorMessage = chk;
    }
  }
  // console.log('chk:'+chk);

  //Check addProductUnit
  if(isSuccess){
    if($('#addForm #addMaxQtyOfEachOne').val() == ""){
      errorMessage = '商品可被購買數量沒有輸入';
      isSuccess = false;

      $('#addForm #addMaxQtyOfEachOne').focus();
    }else{
      //Check Number
      if(validateNumber($('#addForm #addMaxQtyOfEachOne').val())) ;//pass
      else {
        errorMessage = '商品可被購買數量請輸入整數數字';
        isSuccess = false;

        $('#addForm #addMaxQtyOfEachOne').focus();
      }
    }
  }


  //Check addIfRewardPoints
  if(isSuccess){
    if($('#addForm input:radio:checked[name="addIfRewardPoints"]').val() == undefined){
      errorMessage = '購買商品是否贈送點數沒有選擇';
      isSuccess = false;

      $('#addForm input[name=addIfShow]').eq(0).focus();
    }else{
      if($('#addForm input:radio:checked[name="addIfRewardPoints"]').val() == 1){
        //Check RewardPoints
        if( $('#addRewardPoints').val() == ''){
          errorMessage = '贈送點數沒有輸入';
          isSuccess = false;

          $('#addRewardPoints').focus();
        }else{
          //Check Number
          if(validateNumber($('#addRewardPoints').val())) ;//pass
          else {
            errorMessage = '贈送點數請輸入整數數字';
            isSuccess = false;

            $('#addRewardPoints').focus();
          }
        }
      }
    }
  }


  //Check addIfShow
  if(isSuccess && $('#addForm input:radio:checked[name="addIfShow"]').val() == undefined){
    errorMessage = '商品是否顯示於前台沒有選擇';
    isSuccess = false;

    $('#addForm input[name=addIfShow]').eq(0).focus();
  }

  //Check addIfExamine
  if(isSuccess && $('#addForm input:radio:checked[name="addIfExamine"]').val() == undefined){
    errorMessage = '商品是否通過審核沒有選擇';
    isSuccess = false;

    $('#addForm input[name=addIfExamine]').eq(0).focus();
  }

  //Check addIfValid
  if(isSuccess && $('#addForm input:radio:checked[name="addIfValid"]').val() == undefined){
    errorMessage = '商品是否啟用沒有選擇';
    isSuccess = false;

    $('#addForm input[name=addIfValid]').eq(0).focus();
  }


  // final process

  if(!isSuccess) {
    alert(errorMessage);

  } else {

    // save data to data model.
    tinyMCE.triggerSave();
    var formData = new FormData($("#addForm")[0]);

    $.ajax({
      url: "/api/v1/ShopProduct/",
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
            alert('商品名稱已經存在');
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

  return isSuccess;
}

// 取得歸屬部門list 目前只有三個部門 EC,LV,PS可歸屬
function loadDepartmentList(id){
  $.ajax({
    url: "/api/v1/DimUserDegreeToUserType?userType=10",
    type: 'GET',
    async: false,
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      var content = "";
      $.each(response.data, function(index, obj) {
        switch (obj.DegreeCode) {
          case "EC"://電子商務部
            content += '<input name="addDepartmentID" type="radio" value="'+obj.id+'" >'+obj.DegreeName+"\t";
            break;
          case "LV"://直播電商部
            content += '<input name="addDepartmentID" type="radio" value="'+obj.id+'" >'+obj.DegreeName+"\t";
            break;
          case "PS"://票卷業務部
            content += '<input name="addDepartmentID" type="radio" value="'+obj.id+'" >'+obj.DegreeName+"\t";
            break;
          default: //nothing to do
        }
      });
      // console.log(content);
      $(id).html(content);
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
// 取得商品類別list
// parentCategoryId 父類別id, 第一層沒有父類別
// selectListId 下拉選單的id
function loadCategoryList(parentCategoryId, selectListId){
  $.ajax({
    url: "/api/v1/ShopCategory?searchParentCategoryID="+parentCategoryId+"&length=-1",
    type: 'GET',
    async: false,
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      $(selectListId).html('<option value="">請選擇</option>').show(); //reset select list
      $.each(response.data, function(index, obj) {
        // console.log('id:'+obj.id);
        // console.log('name'+ obj.FunctionName);
        if ((obj.IfValid == 1) && (obj.IfDelete == 0)) {
          $(selectListId).append($('<option>', {
            value: obj.id,
            text: obj.CategoryName
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

function searchSupplier() {
    $('#searchSupplier').autocomplete({
        source: function(request, response) {
          console.log('request.term:'+request.term);
            $.ajax({
                url: "/api/v1/DimUser",
                type: 'GET',
                async: false,
                headers: {
                    'Authorization': Cookies.get('authToken')
                },
                data: {
                    searchUserType:20,
                    searchAutoComplete: request.term
                },
                success: function(output) {
                    var outArr = new Array();
                    $.each(output.data, function(i, item) {
                      var tempArr = {
                          id: item.Id,
                          label: item.CompanyName,
                          value: item.MemberNo
                      };
                      outArr.push(tempArr);
                    });

                    response(outArr);
                }
            });
        },
        minLength: 1,
        change: function(event, ui) {
            // $(roomId).val('');
        },
        select: function(event, ui) {
            $('#searchSupplierResult').val(ui.item.value +" "+ ui.item.label);
            $('#addSupplierID').val(ui.item.id);
        }
    });
}

function setSupplier(){
  if($('#searchSupplierResult').attr('readonly') == undefined){
    //未鎖定
    $('#searchSupplierResult').attr('readonly',true);
    $('#searchSupplier').attr('readonly',true);
  }else{
    if(confirm('確定要取消鎖定嗎?')){
      $('#searchSupplierResult').attr('readonly',false);
      $('#searchSupplier').attr('readonly',false);
    }
  }
}

function addProductSet(){
  var content = '<tr>';
  content += '<td id="addProductSetId_'+ProductSetId+'" class="tdProductSetId">';
  content += '<span onclick="addProductSet()" style="cursor: pointer">㊉</span> ';
  content += '<span onclick="delProductSet('+ProductSetId+')" style="cursor: pointer">㊀</span>';
  content += '</td>';
  content += '<td><input name="addProductSetInventory[]" class="form-control" id="addProductSetId_'+ProductSetId+'_Inventory" /></td>';
  content += '<td><input name="addProductSetListPrice[]" class="form-control" id="addProductSetId_'+ProductSetId+'_ListPrice" /></td>';
  content += '<td><input class="form-control" id="addProductSetId_'+ProductSetId+'_CheckPrice" /></td>';
  content += '</tr>';
  $('#ProductSetList').append(content);
  ProductSetId++;
}

function delProductSet(thisId){
  console.log('ProductSet: '+ $('#addForm #ProductSetList .tdProductSetId').length);
  if($('#addForm #ProductSetList .tdProductSetId').length < 2){
    alert('無法刪除, 最少要輸入一筆組合商品資料');
    return false;
  }
  console.log('thisId:'+thisId);
  var id='#addProductSetId_'+thisId;
  $(id).parent().remove();
}

function checkProductSet(){
  //檢查商品組合輸入
  //pass return true;
  //error return message
  //jquery .each 裡 return 相當於 continue; return false 相當於 break;
  var returnStr = true;
  var list = $('.tdProductSetId');
  $.each(list, function(){
    var thisId = $(this).attr('id')
    var inventoryId = '#'+thisId+'_Inventory';
    var listPriceId = '#'+thisId+'_ListPrice';
    var listPriceCheckId = '#'+thisId+'_CheckPrice';
    // console.log('id:'+thisId);//id
    // console.log('ProductSet_Inventory:'+$(inventoryId).val());
    // console.log('ProductSet_ListPrice:'+$(listPriceId).val());
    // console.log('ProductSet_PriceCheck:'+$(listPriceCheckId).val());
    //Check ProductSet Inventory
    if($(inventoryId).val() == ''){
      // console.log('組合設定的數量沒有輸入資料');
      $(inventoryId).focus();
      returnStr = '組合設定的數量沒有輸入資料';
      return false;
    }else{
      //Check Number
      if(validateNumber($(inventoryId).val())) ;//pass
      else {
        $(inventoryId).focus();
        returnStr = '組合設定的數量請輸入整數數字';
        return false;
      }
    }

    //Check ProductSet Inventory
    if($(listPriceId).val() == ''){
      // console.log('組合設定的波利網路價沒有輸入資料');
      $(listPriceId).focus();
      returnStr = '組合設定的波利網路價沒有輸入資料';
      return false;
    }else{
      //Check Number
      if(validateNumber($(listPriceId).val())) ;//pass
      else {
        $(listPriceId).focus();
        returnStr = '組合設定的波利網路價請輸入整數數字';
        return false;
      }
    }

    //Check ProductSet Inventory
    if($(listPriceCheckId).val() == ''){
      // console.log('組合設定的確認價格沒有輸入資料');
      $(listPriceCheckId).focus();
      returnStr = '組合設定的確認價格沒有輸入資料';
      return false;
    }else{
      //Check Number
      if(validateNumber($(listPriceCheckId).val())) ;//pass
      else {
        $(listPriceCheckId).focus();
        returnStr = '組合設定的確認價格請輸入整數數字';
        return false;
      }
    }

    //Check ProductSet Inventory
    if($(listPriceId).val() != $(listPriceCheckId).val()){
      // console.log('組合設定的波利網路價與確認價格沒有相同');
      $(listPriceCheckId).focus();
      returnStr = '組合設定的波利網路價與確認價格沒有相同';
      return false;
    }
  });

  return returnStr;
}

function checkProductSpec(){
  var returnStr = true;
  var list = $('.tdProductSpecId');

  $.each(list, function(){
    var thisId = $(this).attr('id')
    var specName1 = '#'+thisId+'_Name_1';
    var specName2 = '#'+thisId+'_Name_2';
    var inventoryId = '#'+thisId+'_Inventory';
    var safeInventoryId = '#'+thisId+'_SafeInventory';
    console.log('id:'+thisId);//id
    console.log('specName1:'+$(specName1).val());
    console.log('specName2:'+$(specName2).val());
    console.log('inventoryId:'+$(inventoryId).val());
    console.log('safeInventoryId:'+$(safeInventoryId).val());

    //Check ProductSpec Name
    if($(specName1).val() == ''){
      returnStr = '規格設定的名稱1沒有輸入資料';
      $(specName1).focus();
      return false;
    }

    //Check when Type2 exist
    if($('#addProductSpecType2').val() != ''){
      if($(specName2).val() == ''){
        returnStr = '規格設定的名稱2沒有輸入資料';
        $(specName2).focus();
        return false;
      }
    }

    //Check ProductSpec Inventory
    if($(inventoryId).val() == ''){
      returnStr = '規格設定的庫存數量沒有輸入資料';
      $(inventoryId).focus();
      return false;
    }else{
      //Check Number
      if(validateNumber($(inventoryId).val())) ;//pass
      else {
        $(inventoryId).focus();
        returnStr = '規格設定的庫存數量請輸入整數數字';
        return false;
      }
    }

    //Check ProductSpec SafeInventory
    if($(safeInventoryId).val() == ''){
      returnStr = '規格設定的安全庫存沒有輸入資料';
      $(safeInventoryId).focus();
      return false;
    }else{
      //Check Number
      if(validateNumber($(safeInventoryId).val())) ;//pass
      else {
        $(safeInventoryId).focus();
        returnStr = '規格設定的安全庫請輸入整數數字';
        return false;
      }
    }
  });

  return returnStr;
}

function searchBrand(){
  $('#searchBrand').autocomplete({
      source: function(request, response) {
        console.log('request.term:'+request.term);
          $.ajax({
              url: "/api/v1/ShopBrand",
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
                    var tempArr = {
                        id: item.id,
                        label: item.BrandCode,
                        value: item.BrandName
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
          $('#searchBrand').val(ui.item.value);
          $('#addProductBrandID').val(ui.item.id);
      }
  });
}

function addProductSpec(){
  var content = '<tr>';

  content += '<td id="addProductSpecId_'+ProductSpecId+'" class="tdProductSpecId">';
  content += '<span onclick="addProductSpec()" style="cursor:pointer">㊉</span> ';
  content += '<span onclick="delProductSpec('+ProductSpecId+')" style="cursor:pointer">㊀</span>';
  content += '</td>';
  content += '<td><input name="addProductSpecCode[]" class="form-control" id="addProductSpecId_'+ProductSpecId+'_Code" /></td>';
  content += '<td><input name="addProductSpecName1[]" class="form-control" id="addProductSpecId_'+ProductSpecId+'_Name_1" /></td>';
  content += '<td><input name="addProductSpecName2[]" class="form-control" id="addProductSpecId_'+ProductSpecId+'_Name_2" /></td>';
  content += '<td><input name="addProductSpecInventory[]" class="form-control" id="addProductSpecId_'+ProductSpecId+'_Inventory" /></td>';
  content += '<td><input name="addProductSpecSafeInventory[]" class="form-control" id="addProductSpecId_'+ProductSpecId+'_SafeInventory" /></td>';
  content += '<td><input name="addProductSpecSupplierProductNumber[]" class="form-control" id="addProductSpecId_'+ProductSpecId+'_SupplierProductNumber" /></td>';
  content += '</tr>';
  $('#ProductSpecList').append(content);
  ProductSpecId++;
}

function delProductSpec(thisId){
  if($('#addForm #ProductSpecList .tdProductSpecId').length < 2){
    alert('無法刪除, 最少要輸入一筆商品規格資料');
    return false;
  }
  var id='#addProductSpecId_'+thisId;
  console.log('thisId:'+id);
  $(id).parent().remove();
}

function loadBrand(selectListId){
  $.ajax({
    url: "/api/v1/ShopBrand?length=-1",
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
