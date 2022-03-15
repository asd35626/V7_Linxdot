$(function() {
    // login_page
    altair_login_page.init();
});

// variables
var $login_card = $('#login_card'),
    $login_form = $('#login_form'),
    $login_help = $('#login_help'),
    $register_form = $('#register_form'),
    $login_password_reset = $('#login_password_reset');

altair_login_page = {
    init: function () {
        // show login form (hide other forms)
        var login_form_show = function() {
            $login_form
                .show()
                .siblings()
                .hide();
        };

        // show register form (hide other forms)
        var register_form_show = function() {
            $register_form
                .show()
                .siblings()
                .hide();
        };

        // show login help (hide other forms)
        var login_help_show = function() {
            $login_help
                .show()
                .siblings()
                .hide();
        };

        // show password reset form (hide other forms)
        var password_reset_show = function() {
            $login_password_reset
                .show()
                .siblings()
                .hide();
        };

        $('#login_help_show').on('click',function(e) {
            e.preventDefault();
            // card animation & complete callback: login_help_show
            altair_md.card_show_hide($login_card,undefined,login_help_show,undefined);
        });

        $('#signup_form_show').on('click',function(e) {
            e.preventDefault();
            $(this).fadeOut('280');
            // card animation & complete callback: register_form_show
            altair_md.card_show_hide($login_card,undefined,register_form_show,undefined);
        });

        $('.back_to_login').on('click',function(e) {
            e.preventDefault();
            $('#signup_form_show').fadeIn('280');
            // card animation & complete callback: login_form_show
            altair_md.card_show_hide($login_card,undefined,login_form_show,undefined);
        });

        $('#password_reset_show').on('click',function(e) {
            e.preventDefault();
            // card animation & complete callback: password_reset_show
            altair_md.card_show_hide($login_card,undefined,password_reset_show,undefined);
        });


    }
};

$(document).ready(function(e){
  // console.log(Cookies.get('BackendMemberNo'));
  // 填入記住的帳號
  if(Cookies.get('BackendMemberNo') == undefined) ;
  else $('#loginName').val(Cookies.get('BackendMemberNo'));
    //var loginform = $('#loginform');
    //loginform.on('submit',function(e){
    $('#submit').click(function(){
        console.log('submit button is click!');
        getKey();
    });
  });

  function getKey(){
    var loginName = $('#loginName').val();
    var loginPassword = $('#loginPassword').val();

    // console.log('loginName:'+$('#loginName').val());
    // console.log('loginPassword:'+$('#loginPassword').val());
    // console.log('try get key.....Start');
    $.ajax({
      type: "POST",
      url:"../api/v1/UserProcessKey",
      async: false,
      data:{
        'loginName' : loginName,
      },
      headers:{
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response){
        if(response.status == 1){
          // console.log('try get key.....Fail');
          alert(response.message);
        }else{
          login(loginName, loginPassword, response.data);
        }
      },
      error : function(xhr, ajaxOptions, thrownError){
        //console.log('try get key.....Error');
        switch (xhr.status) {
            case 422:
              if(check()){
                // grecaptcha.reset();
                alert("Error(422)");
              }
            break;
            default:
                // grecaptcha.reset();
                alert('server error:' + xhr.status );
        }
      }
    });
    // console.log('try get key.....Finish');
  }

  function login(loginName,loginPassword, keyName){
    // console.log('try login.....Start');
    // console.log('loginName:'+loginName);
    // console.log('loginPassword:'+loginPassword);
    // console.log('keyName:'+keyName);
    var authcode = $("#authcode").val();
    if(loginPassword == ''){
      alert('請輸入密碼');
      return false;
    }

    var pwd = md5(loginPassword)+keyName;
    // console.log('hash:'+bcrypt.hashSync(pwd));
    
    $.ajax({
      type: "POST",
      url:"../api/v1/UserProcessTickets",
      async: false,
      data:{
        loginName : loginName,
        loginPassword : bcrypt.hashSync(pwd),
        authcode: authcode,
      },
      success: function(response){
        if(response.status == 0){
          alert(response.message);
        }else{
          Cookies.remove('authToken');
          Cookies.set('authToken', response.userProcessTicket, { expires: 3 });
          Cookies.remove('BackendMemberNo');
          //勾選記住帳號
          if($("input[name='login_page_stay_signed']").prop('checked')){
            Cookies.set('BackendMemberNo', $('#loginName').val(), { expires: 30 });//存入Cookie
          }
          
          window.location.replace("/Default");
        }
      },
      error : function(xhr, ajaxOptions, thrownError){
        //console.log('try login.....Error');
        switch (xhr.status) {
            case 422:
              if(check()){
                alert("Error(422)");
              }
            break;
            default:
                alert('server error' + xhr.status );
        }
      }
    });
  }