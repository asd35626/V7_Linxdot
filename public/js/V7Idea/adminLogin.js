$(document).ready(function(e){
  //var loginform = $('#loginform');
  //loginform.on('submit',function(e){
  $('#submit').click(function(){
    if(check()){
      $.ajax({
        type: "POST",
        url:"../api/v1/UserProcessTickets",
        async: false,
        data:{
          loginName : $('#loginName').val(),
          loginPassword : $('#loginPassword').val(),
          // captcha : grecaptcha.getResponse()
        },
        success: function(response){
          if(response.status == 0){
            alert("輸入資料不正確, 無法登入");
            reset();
          }else{
            Cookies.remove('authToken');
            Cookies.set('authToken', response.userProcessTicket, { expires: 3 });
            window.location.replace("/Default");
          }
        },
        error : function(xhr, ajaxOptions, thrownError){
          switch (xhr.status) {
              case 422:
                if(check()){
                  // grecaptcha.reset();
                  alert("請勾選驗證碼");
                }
              break;
              default:
                  // grecaptcha.reset();
                  alert('server error');
          }
        }
      });
    }
  });

  $('#reset').click(function(){
    reset();
  })
});

function reset(){
  $('#loginName').val('');
  $('#loginPassword').val('');
  grecaptcha.reset();
}

function check(){
  console.log(grecaptcha.getResponse());
  if($('#loginName').val() == ""){
    alert("請輸入帳號");
  }else if($('#loginPassword').val() == ""){
    alert("請輸入密碼");
  }else if(grecaptcha.getResponse() == ""){
    alert("請勾選驗證");
  }else{
    return true;
  }
  return false;
}
