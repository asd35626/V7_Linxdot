{{-- <!doctype html> --}}
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow" />
    <!-- Remove Tap Highlight on Windows Phone IE -->
    <meta name="msapplication-tap-highlight" content="no">

    <title>@if(env('APP_ENV', '') != 'master') ({{env('APP_ENV', '')}}) @endif Linxdot - Housekeeing</title>

    <link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500' rel='stylesheet' type='text/css'>

    <!-- uikit -->
    <link rel="stylesheet" href="/bower_components/uikit/css/uikit.almost-flat.min.css">

    <!-- altair admin login page -->
    <link rel="stylesheet" href="/assets/css/login_page.min.css">
    <link rel="stylesheet" href="/assets/css/login_v7idea.css">
</head>

<body class="login_page">
    <div class="login_page_wrapper">
        <div class="md-card" id="login_card">
            <div class="md-card-content large-padding" id="login_form">
                <div class="login_heading">
                    <div class="user_avatar"></div>
                </div>
                <form>
                    <div class="md-input-wrapper md-input-filled">
                        <!-- 帳號 -->
                        <input class="md-input label-fixed" type="text" id="loginName" name="loginName">
                        <label class="label" for="input">account</label>
                    </div>
                    <div class="md-input-wrapper md-input-filled">
                        <!-- 密碼 -->
                        <input class="md-input label-fixed" type="password" id="loginPassword" name="loginPassword">
                        <label class="label" for="input">password</label>
                    </div>
                     <div class="md-input-wrapper md-input-filled">
                        <!-- 驗證碼 -->
                        <input class="md-input label-fixed" type="text" id="authcode" name="authcode">
                        <label class="label" for="input"> type the numbers below</label>
                        <br>
                        <img id="captcha_img" border="1" src="/captcha.php?r=echo rand(); ?>" style="width: 150px;">
                        <button type="button" class="md-btn md-btn-flat md-btn-flat-primary md-btn-wave waves-effect waves-button" onclick="ChangeCaptcha()">renew?</button>
                    </div>
                    <div class="uk-margin-medium-top">
                        <a href="#" id="submit" class="md-btn md-btn-primary md-btn-block md-btn-large">login</a>
                    </div>
                    <div class="uk-margin-top">
                        <span class="icheck-inline">
                            <input type="checkbox" name="login_page_stay_signed" id="login_page_stay_signed" data-md-icheck="" checked="" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;">
                            <label for="login_page_stay_signed" class="inline-label">remember me</label>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- common functions -->
    <script src="/assets/js/common.min.js"></script>
    <!-- uikit functions -->
    <script src="/assets/js/uikit_custom.min.js"></script>
    <!-- altair core functions -->
    <script src="/assets/js/altair_admin_common.min.js"></script>

    <!-- FCM Cloud Message -->
    <script src="https://www.gstatic.com/firebasejs/4.10.1/firebase.js"></script>

    <!-- altair login page functions -->
    <script src="/assets/js/pages/login.js?<?php echo time();?>"></script>
    <!-- jquery cookies. -->
    <!-- <script src="/assets/plugins/jquery-cookie/jquery.cookie.js"></script> -->
    <script src="/js/js.cookie.js"></script>
    <!-- util -->
    <script src="/assets/js/bcrypt.js-master/src/bcrypt/util/base64.js"></script>
    <script src="/assets/js/bcrypt.js-master/src/bcrypt/util/utf8.js"></script>
    <script src="/assets/js/bcrypt.js-master/src/bcrypt/util.js"></script>
    <script src="/assets/js/bcrypt.js-master/src/bcrypt/impl.js"></script>
    <script src="/assets/js/bcrypt.js-master/src/bcrypt.js"></script>
    <script src="/assets/js/md5.js"></script>
    <script>
        // check for theme
        if (typeof(Storage) !== "undefined") {
            var root = document.getElementsByTagName( 'html' )[0],
                theme = localStorage.getItem("altair_theme");
            if(theme == 'app_theme_dark' || root.classList.contains('app_theme_dark')) {
                root.className += ' app_theme_dark';
            }
        }

        $(document).ready(function() {
            $('#temp').text("{{env('APP_ENV')}}");
            $('#loginPassword').keypress(function(event) {
                if (event.which == 13) {
                    $('#submit').click();
                }
            });
            $('#authcode').keypress(function(event) {
                if (event.which == 13) {
                    $('#submit').click();
                }
            });
        });

        function ChangeCaptcha() {
            document.getElementById('captcha_img').src='/captcha.php?r='+Math.random()
        }
    </script>
</body>
</html>
