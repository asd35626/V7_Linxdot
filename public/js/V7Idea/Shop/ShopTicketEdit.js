var shopTable;
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

    //載入品牌資料
    loadBrand('#editProductBrand');

    // load
    editData($('#editId').val());

    //商品分類下拉選單, 選擇載入 start
    $('#editProductCategoryList_1').on('change',function(){
      // console.log('list_1:'+$('#editProductCategoryList_1').val());
      //set addShopCategoryID
      $('#editShopCategoryID').val($('#editProductCategoryList_1').val());
      //update list_2
      if($('#editProductCategoryList_1').val() == ""){
        //reset 2 & hide
        $('#editProductCategoryList_2').html('<option value="">請選擇</option>').hide();
      }else{
        loadCategoryList($('#editProductCategoryList_1').val(), '#editProductCategoryList_2');
      }
      //reset list_3 & list 4
      $('#editProductCategoryList_3').html('<option value="">請選擇</option>').hide();
      $('#editProductCategoryList_4').html('<option value="">請選擇</option>').hide();

    });

    $('#editProductCategoryList_2').on('change',function(){
      if($('#editProductCategoryList_2').val() == ""){
        //reset list_3 & list 4
        $('#editProductCategoryList_3').html('<option value="">請選擇</option>').hide();
        $('#editProductCategoryList_4').html('<option value="">請選擇</option>').hide();
        //set form list_1
        $('#editShopCategoryID').val($('#editProductCategoryList_1').val());
      }else {
        //set addShopCategoryID
        $('#editShopCategoryID').val($('#editProductCategoryList_2').val());
        //update list_3
        loadCategoryList($('#editProductCategoryList_2').val(), '#editProductCategoryList_3');
        //reset  list 4
        $('#editProductCategoryList_4').html('<option value="">請選擇</option>').hide();
      }
    });

    $('#editProductCategoryList_3').on('change',function(){
      if($('#editProductCategoryList_3').val() == ""){
        //reset 4
        $('#editProductCategoryList_4').html('<option value="">請選擇</option>').hide();
        //set from 2
        $('#editShopCategoryID').val($('#editProductCategoryList_2').val());
      }else {
        //set addShopCategoryID
        $('#editShopCategoryID').val($('#editProductCategoryList_3').val());
        //update list_3
        if($('#editProductCategoryList_3').val() == "") ;
        else loadCategoryList($('#editProductCategoryList_3').val(), '#editProductCategoryList_4');
      }
    });

    $('#editProductCategoryList_4').on('change',function(){
      if($('#editProductCategoryList_3').val() == ""){
        //set from 3
        $('#editShopCategoryID').val($('#editProductCategoryList_3').val());
      }else {
        //set addShopCategoryID
        $('#editShopCategoryID').val($('#editProductCategoryList_4').val());
      }
    });
    //商品分類下拉選單, 選擇載入 end
    //是否贈與點數
    $('input:radio[name="editIfRewardPoints"]').change(function() {
        console.log('editRewardPoints:'+$(this).val());
        if($(this).val() == 1) $('#editRewardPointsArea').css('visibility','visible');
        if($(this).val() == -1) $('#editRewardPointsArea').css('visibility','hidden');
    });
  }
  $('#addDeliverDate').datepicker({dateFormat: "yy-mm-dd"});
  $('#addOnWebStartDate').datepicker({dateFormat: "yy-mm-dd"});
  $('#addOnWebEndDate').datepicker({dateFormat: "yy-mm-dd",minDate: 0});
});

function returnToList() {
  // $('#list').show();
  // $('#edit').hide();
  // $('#edit').hide();
  window.history.back();
}

//show edit page
function editData(thisId) {
  //initial
  $('#editForm')[0].reset();
  loadDepartmentList('#editProductDepartmentList');
  loadCategoryList('','#editProductCategoryList_1');

  //autocomplete
  searchSupplier();
  searchBrand();
  // get detail data
  $.ajax({
    url: "/api/v1/ShopProduct/" + thisId,
    type: 'GET',
    headers: {
      'Authorization': Cookies.get('authToken')
    },
    success: function(response) {
      if (response.status == 1) {
        //"SupplierID": "9b4d1595-d340-42fe-b0a3-445184a27054",
        $('#searchSupplierResult').val(response.data.SupplierMemberNo + " " + response.data.SupplierName);
        $('#editSupplierID').val(response.data.SupplierID);
        $('#editForm #editCreateBy').val(response.data.CreateBy);
        $('#editForm #editCreateDate').val(response.data.CreateDate);

        $('#editForm #editProductName').val(response.data.ProductName);
        $('#editForm #editShortDescription').val(response.data.ShortDescription);
        //radio DepartmentID
        var departmentId = response.data.DepartmentID;
        if(response.data.DepartmentID) $('#editForm input[name=editDepartmentID][value='+departmentId+']').attr('checked', true);
        $('#editProductBrandID').val(response.data.BrandID); //品牌
        $('#searchBrand').val(response.data.BrandName); //品牌名稱

        if(response.data.DeliverDate) $('#editDeliverDate').val(response.data.DeliverDate);//預計出貨日期
        if(response.data.DeliverDays) $('#editDeliverDays').val(response.data.DeliverDays);//下單後幾天出貨
        $('#editOnWebStartDate').val(response.data.OnWebStartDate);//上架日期
        $('#editOnWebEndDate').val(response.data.OnWebEndDate);//下架日期

        //radio ProductType 商品類型
        if (response.data.ProductType == 0) {
          $('#editForm input[name=editProductType][value=0]').attr('checked', true);
        } else if(response.data.ProductType == 3){
          $('#editForm input[name=editProductType][value=3]').attr('checked', true);
        } else if(response.data.ProductType == 1){
          $('#editForm input[name=editProductType][value=1]').attr('checked', true);
        } else if(response.data.ProductType == 2){
          $('#editForm input[name=editProductType][value=2]').attr('checked', true);
        } else ;

        $('#editSupplierProductNumber').val(response.data.SupplierProductNumber);//供應商貨號
        $('#editWebTag').val(response.data.WebTag);//網路標籤
        $('#editRefPrice').val(response.data.RefPrice);//商品市價
        $('#editCost').val(response.data.Cost);//成本價
        // $('#editListPrice').val(response.data.ListPrice);//波力網路價
        // $('#editListPriceCheck').val(response.data.ListPrice);

        //商品類別選單
        // console.log('ShopCategoryParentID.length:'+response.data.ShopCategoryParentID.length);
        var list_id = 1;
        if(response.data.ShopCategoryParentID.length > 0){
          for(var i = response.data.ShopCategoryParentID.length; i > 0; i--){
            var categoryListId = '#editProductCategoryList_' + list_id;

            // console.log('i:' + i);
            // console.log('parentId:' + response.data.ShopCategoryParentID[i-1]);
            if((i-2) < 0){//index 超出array範圍
              // console.log('selected:' + response.data.ShopCategoryID);
              loadCategoryList(response.data.ShopCategoryParentID[i-1], categoryListId, response.data.ShopCategoryID);
            }else{
              // console.log('selected:' + response.data.ShopCategoryParentID[i-2]);
              loadCategoryList(response.data.ShopCategoryParentID[i-1], categoryListId, response.data.ShopCategoryParentID[i-2]);
            }

            list_id++;
          }
        }

        $('#editShopCategoryID').val(response.data.ShopCategoryID);

        //"ShopCategoryID": "d31d7eaf-ddc2-438b-98d7-94a0847bd8e5",
        $('#editMaxInventory').val(response.data.MaxInventory);//商品庫存數量
        $('#editSafeInventory').val(response.data.SafeInventory);//安全庫存數量

        //上傳圖片
        $.each(response.data.RelatedImage, function(k, v){
          // console.log('k:'+k + ',v:'+v);//k:RelatedImage1,v:files/shop/Wj6vSXm6HcAzzbK.jpg
          var thisImageId = '#' + k; //'#editRelatedImage1'
          var thisShowImageId = '#editShow' + k;//'#editShowRelatedImage1'
          if(v == '') $(thisImageId).empty();
          else{
            $(thisShowImageId).prepend('<img src="/Service/' + v + '" />');
          }
        });
        //商品可被購買數量
        $('#editMaxQtyOfEachOne').val(response.data.MaxQtyOfEachOne);
        //商品規格1,2
        $('#editProductSpecType1').val(response.data.Spec1stTypeName);
        $('#editProductSpecType2').val(response.data.Spec2ndTypeName);
        //商品介紹
        tinymce.get('editLongDescription').setContent(response.data.LongDescription);
        //店家說明
        tinymce.get('editStoreDescription').setContent(response.data.StoreDescription);
        //出貨說明
        tinymce.get('editDeliverDescription').setContent(response.data.DeliverDescription);
        //商品規格
        tinymce.get('editProductSpecDescription').setContent(response.data.ProductSpecDescription);
        //
        if (response.data.IfValid == 1) {
          $('#editForm input[name=editIfValid][value=1]').attr('checked', true);
        } else {
          $('#editForm input[name=editIfValid][value=-1]').attr('checked', true);
        }

        //radio IfShow
        if (response.data.IfShow == 1) {
          $('#editForm input[name=editIfShow][value=1]').attr('checked', true);
        } else {
          $('#editForm input[name=editIfShow][value=-1]').attr('checked', true);
        }

        //radio IfExamine
        if (response.data.IfExamine == 1) {
          $('#editForm input[name=editIfExamine][value=1]').attr('checked', true);
        } else {
          $('#editForm input[name=editIfExamine][value=-1]').attr('checked', true);
        }

        //radio IfRewardPoints
        if (response.data.IfRewardPoints == 1) {
          $('#editForm input[name=editIfRewardPoints][value=1]').attr('checked', true);
          $('#editRewardPointsArea').css('visibility','visible');
        } else {
          $('#editForm input[name=editIfRewardPoints][value=-1]').attr('checked', true);
          $('#editRewardPointsArea').css('visibility','hidden');
        }
        $('#editRewardPoints').val(response.data.RewardPoints);
        //load ProductSet
        if(response.data.ProductSet.length > 0){
          $.each(response.data.ProductSet, function(index, obj){
            addProductSet(obj);
          });
        }
        //load ProductSpec
        if(response.data.ProductSpec.length > 0){
          $.each(response.data.ProductSpec, function(index, obj){
            addProductSpec(obj);
          });
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

  // check DepartmentID

  if(isSuccess && ($('#editForm input:radio:checked[name="editDepartmentID"]').val() == undefined || $('#editForm input:radio:checked[name="editDepartmentID"]').val() == '' )) {
    errorMessage = '商品建檔歸屬部門沒有選擇';
    isSuccess = false;

    $('#editForm input[name=editDepartmentID]').eq(0).focus();

  }

  // CHECK #editForm #editSupplierID

  if(isSuccess && $('#editForm #editSupplierID').val() == "" ) {
    errorMessage = '沒有選擇供應商';
    isSuccess = false;

    $('#editForm #searchSupplier').focus();
  }

  //Check addProductName
  if(isSuccess && $('#editForm #editProductName').val() == ""){
      errorMessage = '商品名稱沒有輸入';
      isSuccess = false;

      $('#editProductName').focus();
  }

  //Check addShortDescription
  if(isSuccess && $('#editForm #editShortDescription').val() == ""){
    errorMessage = '商品促銷標語沒有輸入';
    isSuccess = false;

    $('#editForm #editShortDescription').focus();
  }


  //Check addBrand
  if(isSuccess && $('#editForm #editProductBrandID').val() == ""){
    errorMessage = '商品品牌沒有輸入';
    isSuccess = false;

    $('#editForm #searchBrand').focus();
  }

  //Check addProductType
  if(isSuccess && $('#editForm input:radio:checked[name="editProductType"]').val() == undefined){
      errorMessage = '商品類型沒有選擇';
      isSuccess = false;

      $('#editForm input[name=addProductType]').eq(0).focus();
  }else{
    if($('#editForm input:radio:checked[name="editProductType"]').val() == 3){
      if($('#editDeliverDate').val() == ''){
        errorMessage = '上架日期沒有輸入';
        isSuccess = false;

        $('#editDeliverDate').focus();
      }else{
        //check date valid
        isSuccess = validateDate($('#editDeliverDate').val());//pass return true
        if(isSuccess) console.log('OnWebStartDate pass');
        else{
            errorMessage = '預計出貨日期請輸入正確的日期格式(YYYY-MM-DD)';
            $('#editDeliverDate').focus();
        }
      }
    }
  }

  //Check addOnWebStartDate
  if(isSuccess){
    if($('#editForm #editOnWebStartDate').val() == ""){
      errorMessage = '上架日期沒有輸入';
      isSuccess = false;

      $('#editForm #editOnWebStartDate').focus();
    }else{
      //check date valid
      isSuccess = validateDate($('#editForm #editOnWebStartDate').val());//pass return true
      if(isSuccess) console.log('OnWebStartDate pass');
      else{
        errorMessage = '上架日期請輸入正確的日期格式(YYYY-MM-DD)';
        $('#addDeliverDate').focus();
      }
    }
  }

  //Check addOnWebEndDate
  if(isSuccess){
    if($('#editForm #editOnWebEndDate').val() == ""){
      errorMessage = '下架日期沒有輸入';
      isSuccess = false;

      $('#editForm #editOnWebEndDate').focus();
    }else{
      //check date valid
      isSuccess = validateDate($('#editForm #editOnWebEndDate').val());//pass return true
      if(isSuccess) console.log('OnWebEndDate pass');
      else{
        errorMessage = '下架日期請輸入正確的日期格式(YYYY-MM-DD)';
        $('#editForm #editOnWebEndDate').focus();
      }
    }
  }

  //Check addShopCategoryID
  if(isSuccess && $('#editForm #editShopCategoryID').val() == ""){
    errorMessage = '商品分類沒有選擇';
    isSuccess = false;

    $('#editProductCategoryList_1').focus();
    console.log('l_1:'+document.getElementById("editProductCategoryList_1").length);
    console.log('l_2:'+document.getElementById("editProductCategoryList_2").length);
    console.log('l_3:'+document.getElementById("editProductCategoryList_3").length);
    console.log('l_4:'+document.getElementById("editProductCategoryList_4").length);
  }

  //Check addRefPrice
  if(isSuccess){
    if($('#editForm #editRefPrice').val() == ""){
      errorMessage = '商品市價沒有輸入';
      isSuccess = false;

      $('#editForm #editRefPrice').focus();
    }else{
      //Check Number
      if(validateNumber($('#editForm #editRefPrice').val())) ;//pass
      else {
        errorMessage = '商品市價請輸入整數數字';
        isSuccess = false;

        $('#editForm #editRefPrice').focus();
      }
    }
  }

  //Check addCost
  if(isSuccess){
    if($('#editForm #editCost').val() == ""){
      errorMessage = '成本價沒有輸入';
      isSuccess = false;

      $('#editForm #editCost').focus();
    }else{
      //Check Number
      if(validateNumber($('#editForm #editCost').val())) ;//pass
      else {
        errorMessage = '成本價請輸入整數數字';
        isSuccess = false;

        $('#editForm #editCost').focus();
      }
    }
  }

  //Check addProductMaterial
  if(isSuccess && $('#editForm #editProductMaterial').val() == ""){
    errorMessage = '商品材質沒有輸入';
    isSuccess = false;

    $('#editForm #editProductMaterial').focus();
  }

  //Check addProductUnit
  if(isSuccess && $('#editForm #editProductUnit').val() == ""){
    errorMessage = '商品單位沒有輸入';
    isSuccess = false;

    $('#editForm #editProductUnit').focus();
  }

  //Check ProductSet
  if(isSuccess && $('#editForm #ProductSetList .tdProductSetId').length < 1){
    errorMessage = '商品組合至少要有一筆資料';
    isSuccess = false;

    $('#editForm #editProductUnit').focus();
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
  if(isSuccess && $('#editForm #ProductSpecList .tdProductSpecId').length < 1){
    errorMessage = '商品規格至少要有一筆資料';
    isSuccess = false;

  }

  //Check addProductSpecType1
  if(isSuccess && $('#editForm #editProductSpecType1').val() == ""){
    errorMessage = '第一層規格名稱沒有輸入';
    isSuccess = false;

    $('#editForm #editProductSpecType1').focus();
  }

  if(isSuccess){
    var chk = checkProductSpec();
    if(chk === true) ;
    else{
      isSuccess = false;
      errorMessage = chk;
    }
  }

  //Check addProductUnit
  if(isSuccess){
    if($('#editForm #editMaxQtyOfEachOne').val() == ""){
      errorMessage = '商品可被購買數量沒有輸入';
      isSuccess = false;

      $('#editForm #editMaxQtyOfEachOne').focus();
    }else{
      //Check Number
      if(validateNumber($('#editForm #editMaxQtyOfEachOne').val())) ;//pass
      else {
        errorMessage = '商品可被購買數量請輸入整數數字';
        isSuccess = false;

        $('#editForm #editMaxQtyOfEachOne').focus();
      }
    }
  }

  //Check addIfRewardPoints
  if(isSuccess){
    if($('#editForm input:radio:checked[name="editIfRewardPoints"]').val() == undefined){
      errorMessage = '購買商品是否贈送點數沒有選擇';
      isSuccess = false;

      $('#editForm input[name=editIfShow]').eq(0).focus();
    }else{
      if($('#editForm input:radio:checked[name="editIfRewardPoints"]').val() == 1){
        //Check RewardPoints
        if( $('#editRewardPoints').val() == ''){
          errorMessage = '贈送點數沒有輸入';
          isSuccess = false;

          $('#editRewardPoints').focus();
        }else{
          //Check Number
          if(validateNumber($('#editRewardPoints').val())) ;//pass
          else {
            errorMessage = '贈送點數請輸入整數數字';
            isSuccess = false;

            $('#editRewardPoints').focus();
          }
        }
      }
    }
  }

  //Check addIfShow
  if(isSuccess && $('#editForm input:radio:checked[name="editIfShow"]').val() == undefined){
    errorMessage = '商品是否顯示於前台沒有選擇';
    isSuccess = false;

    $('#editForm input[name=editIfShow]').eq(0).focus();
  }

  //Check addIfExamine
  if(isSuccess && $('#editForm input:radio:checked[name="editIfExamine"]').val() == undefined){
    errorMessage = '商品是否通過審核沒有選擇';
    isSuccess = false;

    $('#editForm input[name=editIfExamine]').eq(0).focus();
  }

  //Check addIfValid
  if(isSuccess && $('#editForm input:radio:checked[name="editIfValid"]').val() == undefined){
    errorMessage = '商品是否啟用沒有選擇';
    isSuccess = false;

    $('#editForm input[name=editIfValid]').eq(0).focus();
  }

  // final process

  if(!isSuccess) {
    alert(errorMessage);

  } else {

    tinyMCE.triggerSave();
    // var formData = $("#editForm").serialize();
    var formData = new FormData($("#editForm")[0]);
    $.ajax({
      // url: "/api/v1/ShopProduct/" + $('#editId').val(),
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
      cache: false,
      contentType: false,
      processData: false
    });
  }

  return false;
}

function resetEditData() {
  location.href = "/Shop/ProductEdit/" + $('#editId').val();
}

//取得歸屬部門list 目前只有三個部門 EC,LV,PS可歸屬
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
            content += '<input name="editDepartmentID" type="radio" value="'+obj.id+'" >'+obj.DegreeName+"\t";
            break;
          case "LV"://直播電商部
            content += '<input name="editDepartmentID" type="radio" value="'+obj.id+'" >'+obj.DegreeName+"\t";
            break;
          case "PS"://票卷業務部
            content += '<input name="editDepartmentID" type="radio" value="'+obj.id+'" >'+obj.DegreeName+"\t";
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
function loadCategoryList(parentCategoryId, selectListId, selectedOption=null){

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
      if(selectedOption != null) $(selectListId).val(selectedOption);

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
            $('#editSupplierID').val(ui.item.id);
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

function addProductSet(thisObj=null){
  $('#ProductSetList').show();
  var content = '<tr>';
  content += '<td id="editProductSetId_'+ProductSetId+'" class="tdProductSetId">';
  content += '<span onclick="addProductSet()" style="cursor: pointer">㊉</span> ';
  content += '<span onclick="delProductSet('+ProductSetId+')" style="cursor: pointer">㊀</span>';
  content += '</td>';

  if(thisObj){//載入項目
    content += '<td>';
    //ProductSetId
    content += '<input type="hidden" name="editProductSetID[]" value="'+thisObj.id+'" id="editProductSetId_'+ProductSetId+'_Id"/>';
    //是否有效, 下架將數值改為 0 回傳
    content += '<input type="hidden" name="editProductSetEditIfValid[]" value="1" id="editProductSetId_'+ProductSetId+'_IfValid" />';
    //庫存數量
    content += '<input name="editProductSetInventory[]" class="form-control" id="editProductSetId_'+ProductSetId+'_Inventory" value="'+thisObj.Inventory+'"/>';
    content += '</td>';
    //價格
    content += '<td><input name="editProductSetListPrice[]" class="form-control" id="editProductSetId_'+ProductSetId+'_ListPrice" value="'+thisObj.Price+'"/></td>';
    //確認價格
    content += '<td><input class="form-control" id="editProductSetId_'+ProductSetId+'_CheckPrice" value="'+thisObj.Price+'"/></td>';

  }else{//新增項目
    content += '<td>';
    //ProductSetId
    content += '<input type="hidden" name="editProductSetID[]" value="" id="editProductSetId_'+ProductSetId+'_Id"/>';
    //是否有效, 下架將數值改為 0 回傳
    content += '<input type="hidden" name="editProductSetEditIfValid[]" value="1" id="editProductSetId_'+ProductSetId+'_IfValid" />';
    //庫存數量
    content += '<input name="editProductSetInventory[]" class="form-control" id="editProductSetId_'+ProductSetId+'_Inventory" />';
    content += '</td>';
    //價格
    content += '<td><input name="editProductSetListPrice[]" class="form-control" id="editProductSetId_'+ProductSetId+'_ListPrice" /></td>';
    //確認價格
    content += '<td><input class="form-control" id="editProductSetId_'+ProductSetId+'_CheckPrice" /></td>';
  }
  content += '</tr>';
  $('#ProductSetList').append(content);
  ProductSetId++;
}

function delProductSet(thisId){
  // console.log('thisId:'+thisId);
  var id='#editProductSetId_'+thisId;
  // console.log('id:'+id);
  // $(id).parent().remove();
  var editProductSetID = '#editProductSetId_'+thisId+'_Id';
  // console.log('editProductSetID:'+editProductSetID);
  // console.log($(editProductSetID).val());
  if($('#editForm #ProductSetList .tdProductSetId').length < 2){
    alert('無法刪除, 最少要輸入一筆商品組合資料');
    return false;
  }else{
    if($(editProductSetID).val() == ''){
      //可刪除
      $(id).parent().remove();
      console.log('remove');
    }else{
      if(confirm('確定要將該組合下架嗎?')){
        $(id).parent().hide();
        $(id).removeClass('tdProductSetId');//移出check成員

        var editSetIfValid = '#editProductSetId_'+thisId+'_IfValid';
        $(editSetIfValid).val(0);
        console.log('hide & set IfValid = '+$(editSetIfValid).val());

      }
    }
  }
}

function checkProductSet(){
  //檢查商品組合輸入
    //pass return true;
    //error return message
    //note: jquery .each 裡 return 相當於 continue; return false 相當於 break;

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
      if(validateNumber($(inventoryId).val())){
        // console.log('Inventory check pass');
      } else {
        $(inventoryId).focus();
        returnStr = '組合設定的數量請輸入整數數字';
        return false;
      }
    }

    //Check ProductSet Inventory
    if($(listPriceId).val() == ''){
      $(listPriceId).focus();
      returnStr = '組合設定的波利網路價沒有輸入資料';
      return false;
    }else{
      //Check Number
      if(validateNumber($(listPriceId).val())){
        //  console.log('ListPrice check pass');
      } else {
        $(listPriceId).focus();
        returnStr = '組合設定的波利網路價請輸入整數數字';
        return false;
      }
    }

    //Check ProductSet Inventory
    if($(listPriceCheckId).val() == ''){
      $(listPriceCheckId).focus();
      returnStr = '組合設定的確認價格沒有輸入資料';
      return false;
    }else{
      //Check Number
      if(validateNumber($(listPriceCheckId).val())){
        //  console.log('ListPriceCheck check pass');
      } else {
        $(listPriceCheckId).focus();
        returnStr = '組合設定的確認價格請輸入整數數字';
        return false;
      }
    }

    //Check ProductSet Inventory
    if($(listPriceId).val() != $(listPriceCheckId).val()){
      $(listPriceCheckId).focus();
      returnStr = '組合設定的波利網路價與確認價格沒有相同';
      return false;
    }
  });

  return returnStr;
}

function addProductSpec(thisObj=null){
  $('#ProductSpecList').show();
  var content = '<tr>';
  content += '<td id="editProductSpecId_'+ProductSpecId+'" class="tdProductSpecId">';
  content += '<span onclick="addProductSpec()" style="cursor: pointer">㊉</span> ';
  content += '<span onclick="delProductSpec('+ProductSpecId+')" style="cursor: pointer">㊀</span>';
  content += '</td>';

  if(thisObj){

    content += '<td>';
    content += '<input name="editProductSpecCode[]" class="form-control" value="'+thisObj.ProductCode+'" id="editProductSpecId_'+ProductSpecId+'_Code" /></td>';
    content += '<input type="hidden" name="editProductSpecID[]" value="'+thisObj.id+'" id="editProductSpecId_'+ProductSpecId+'_Id"/>';
    content += '<input type="hidden" name="editProductSpecEditIfValid[]" value="1" id="editProductSpecId_'+ProductSpecId+'_IfValid"/>';
    content += '</td>';
    content += '<td><input name="editProductSpecName1[]" class="form-control" value="'+thisObj.ProductSpecName1+'"  id="editProductSpecId_'+ProductSpecId+'_Name_1" /></td>';
    content += '<td><input name="editProductSpecName2[]" class="form-control" value="'+thisObj.ProductSpecName2+'" id="editProductSpecId_'+ProductSpecId+'_Name_2" /></td>';
    content += '<td><input name="editProductSpecInventory[]" class="form-control" value="'+thisObj.Inventory+'" id="editProductSpecId_'+ProductSpecId+'_Inventory" /></td>';
    content += '<td><input name="editProductSpecSafeInventory[]" class="form-control" value="'+thisObj.SafeInventory+'" id="editProductSpecId_'+ProductSpecId+'_SafeInventory" /></td>';
    content += '<td><input name="editProductSpecSupplierProductNumber[]" class="form-control" value="'+thisObj.SupplierProductNumber+'" id="editProductSpecId_'+ProductSpecId+'_SupplierProductNumber" /></td>';

  }else{

    content += '<td>';
    content += '<input name="editProductSpecCode[]" class="form-control" id="editProductSpecId_'+ProductSpecId+'_Code" /></td>';
    content += '<input type="hidden" name="editProductSpecID[]" value="" id="editProductSpecId_'+ProductSpecId+'_Id"/>';
    content += '<input type="hidden" name="editProductSpecEditIfValid[]" value="1" id="editProductSpecId_'+ProductSpecId+'_IfValid"/>';
    content += '</td>';
    content += '<td><input name="editProductSpecName1[]" class="form-control" id="editProductSpecId_'+ProductSpecId+'_Name_1" /></td>';
    content += '<td><input name="editProductSpecName2[]" class="form-control" id="editProductSpecId_'+ProductSpecId+'_Name_2" /></td>';
    content += '<td><input name="editProductSpecInventory[]" class="form-control" id="editProductSpecId_'+ProductSpecId+'_Inventory" /></td>';
    content += '<td><input name="editProductSpecSafeInventory[]" class="form-control" id="editProductSpecId_'+ProductSpecId+'_SafeInventory" /></td>';
    content += '<td><input name="editProductSpecSupplierProductNumber[]" class="form-control" id="editProductSpecId_'+ProductSpecId+'_SupplierProductNumber" /></td>';

  }

  content += '</tr>';
  $('#ProductSpecList').append(content);
  ProductSpecId++;
}

function delProductSpec(thisId){
  console.log('thisId:'+thisId);
  var id='#editProductSpecId_'+thisId;
  console.log('spec id:' + id);
  var editProductSpecID = '#editProductSpecId_'+thisId+'_Id';
  console.log($(editProductSpecID).val());

  if($('#editForm #ProductSpecList .tdProductSpecId').length < 2){
    alert('無法刪除, 最少要輸入一筆商品規格資料');
    return false;
  }else{
    if($(editProductSpecID).val() == ''){
      //可刪除
      $(id).parent().remove();
      console.log('remove');
    }else{
      if(confirm('確定要將該規格下架嗎?')){
        $(id).parent().hide();
        $(id).removeClass('tdProductSpecId');//移出check成員

        var editSpecIfValid = '#editProductSpecId_'+thisId+'_IfValid';
        $(editSpecIfValid).val(0);
        console.log('hide & set IfValid = '+$(editSpecIfValid).val());

      }
    }
  }
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
    }else{
      console.log('name1 check pass ');
    }

    //Check when Type2 exist
    if($('#editProductSpecType2').val() != ''){
      if($(specName2).val() == ''){
        returnStr = '規格設定的名稱2沒有輸入資料';
        $(specName2).focus();
        return false;
      }else{
        console.log('name2 check pass ');
      }
    }

    //Check ProductSpec Inventory
    if($(inventoryId).val() == ''){
      returnStr = '規格設定的庫存數量沒有輸入資料';
      $(inventoryId).focus();
      return false;
    }else{
      //Check Number
      if(validateNumber($(inventoryId).val())){
          console.log('inventory check pass ');
      } else {
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
      if(validateNumber($(safeInventoryId).val())){
        console.log('safeInventory check pass ');
      } else {
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
          $('#editProductBrandID').val(ui.item.id);
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
