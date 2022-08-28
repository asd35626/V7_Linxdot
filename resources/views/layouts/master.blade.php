<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="zh-tw"> <!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow" />
    <!-- Remove Tap Highlight on Windows Phone IE -->
    <meta name="msapplication-tap-highlight" content="no"/>

    <title>@if(env('APP_ENV', '') != 'master') ({{env('APP_ENV', '')}}) @endif Linxdot - Housekeeing</title>
    <link rel="icon" href="/favicon.ico">
    <!-- uikit -->
    <link rel="stylesheet" href="/bower_components/uikit/css/uikit.almost-flat.min.css" media="all">

    <!-- flag icons -->
    <link rel="stylesheet" href="/assets/icons/flags/flags.min.css" media="all">

    <!-- style switcher -->
    <link rel="stylesheet" href="/assets/css/style_switcher.min.css" media="all">

    <!-- altair admin -->
    <link rel="stylesheet" href="/assets/css/main.min.css" media="all">
    <link rel="stylesheet" href="/assets/css/main_v7idea.css">

    <!-- themes -->
    <link rel="stylesheet" href="/assets/css/themes/themes_combined.min.css" media="all">
    <!-- dropify -->
    <link rel="stylesheet" href="/assets/skins/dropify/css/dropify.css">
    <link rel="stylesheet" href="/assets/css/fix.css" media="all">


    <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">
    <style>
        .text-center {
            text-align: center;
        }
    </style>

    <!-- map -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.css' rel='stylesheet' />

    
    @yield('extraCssArea')
</head>
<body class="sidebar_main_open sidebar_main_swipe page_heading_active">
    <!-- main header -->
    <header id="header_main">
        <div class="header_main_content">
            <nav class="uk-navbar">

                <!-- main sidebar switch -->
                <a href="#" id="sidebar_main_toggle" class="sSwitch sSwitch_left">
                    <span class="sSwitchIcon"></span>
                </a>

                <!-- secondary sidebar switch -->
                <a href="#" id="sidebar_secondary_toggle" class="sSwitch sSwitch_right sidebar_secondary_check">
                    <span class="sSwitchIcon"></span>
                </a>

                <div class="uk-navbar-flip">
                    <ul class="uk-navbar-nav user_actions">
                        <li style="padding-top: 10%;color: white" id="account"></li>
                        <li data-uk-dropdown="{mode:'click',pos:'bottom-right'}">
                            <a href="#" class="user_action_image">
                                <!-- <img class="md-user-image" src="/assets/img/avatars/avatar_11_tn.png" alt=""> -->
                                <!-- <img class="md-user-image" src="/assets/img/avatars/user.png" alt=""> -->
                                <span class="material-icons md-user-image" style="font-size:34px;padding-top:25%">account_circle</span>
                            </a>
                            <div class="uk-dropdown uk-dropdown-small">
                                <ul class="uk-nav js-uk-prevent">
                                    <li><a href="/Profile/PasswordSetting">change password</a></li>
                                    <li><a style="cursor:pointer" onclick="logout();">logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        <div class="header_main_search_form">
            <i class="md-icon header_main_search_close material-icons">&#xE5CD;</i>
            <form class="uk-form uk-autocomplete" data-uk-autocomplete="{source:'data/search_data.json'}">
                <input type="text" class="header_main_search_input">
                <button class="header_main_search_btn uk-button-link"><i class="md-icon material-icons">&#xE8B6;</i></button>
                <!--<script type="//text/autocomplete"></script> -->
                <!-- 當需要實作時，需要在原始檔案找回來 -->
            </form>
        </div>
    </header>
    <!-- main header end -->

    <!-- main sidebar -->
    <aside id="sidebar_main">
        <div class="scroll-wrapper scrollbar-inner" style="position: relative;">
            <div class="scrollbar-inner scroll-content" style="height: auto; margin-bottom: 0px; margin-right: 0px; max-height: 625px;">
                <div class="sidebar_main_header">
                    <div class="sidebar_logo">
                        <a href="/Default" class="sSidebar_hide sidebar_logo_large" style="margin-left:5px;">
                            <img class="logo_regular" src="/assets/img/logo-white.svg" alt="" height="" width="230"/>
                            {{-- <img class="logo_light" src="/assets/img/logo_main_white.png" alt="" height="31" width="150"/> --}}
                        </a>
                    </div>
                </div>
                <div class="menu_section">
                    <ul id="menuArea">
                        <!-- 放選單的地方 -->
                    </ul>
                </div>
            </div>
            <div class="scroll-element scroll-x">
                <div class="scroll-element_outer">
                    <div class="scroll-element_size"></div>
                    <div class="scroll-element_track"></div>
                    <div class="scroll-bar" style="width: 96px;"></div>
                </div>
            </div>
            <div class="scroll-element scroll-y"><div class="scroll-element_outer">
                <div class="scroll-element_size"></div>
                <div class="scroll-element_track"></div>
                <div class="scroll-bar" style="height: 96px;"></div>
            </div>
        </div>
    </aside>
    <!-- main sidebar end -->

    <div id="page_content">
        <div class="uk-sticky-placeholder" style="height: 80px; margin: 0px;">
            <div id="page_heading" data-uk-sticky="{ top: 0, media: 19200 }" class="uk-sticky-init uk-active">
                <!-- 麵包屑導航 -->
                <div class="heading_actions"></div>
                <h1 id="pageTitle">@yield('pageTitle')</h1>
                <div id="breadcrumbArea">@yield('breadcrumbArea')</div>
            </div>
            <div id="page_content_inner">
                    {{-- 主要內容將放在這邊 --}}
                    @yield('content')
                    {{-- 有關於新增的表單區域放在這裡 --}}
                    @yield('content_add')
                    {{-- 有關於編輯表單區域放在這裡 --}}
                    @yield('content_edit')
                    {{-- 有關於列表區域放在這裡 --}}
                    @yield('content_list')
            </div>
        </div>
    </div>
    <!-- google web fonts -->
    <script>
        WebFontConfig = {
            google: {
                families: [
                    'Source+Code+Pro:400,700:latin',
                    'Roboto:400,300,500,700,400italic:latin'
                ]
            }
        };
        (function() {
            var wf = document.createElement('script');
            wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
        })();
    </script>

    <!-- common functions -->
    <script src="/assets/js/common.min.js"></script>
    <!-- uikit functions -->
    <script src="/assets/js/uikit_custom.min.js"></script>
    <!-- altair common functions/helpers -->
    <script src="/assets/js/altair_admin_common.min.js"></script>

    <!-- page specific plugins -->
    <!-- handlebars.js -->
    <script src="/bower_components/handlebars/handlebars.js"></script>
    <script src="/assets/js/custom/handlebars_helpers.min.js"></script>
    <!-- parsley (validation) -->
    <script>
    // load parsley config (altair_admin_common.js)
    altair_forms.parsley_validation_config();
    </script>
    <script src="/bower_components/parsleyjs/dist/parsley.min.js"></script>
    <script src="/js/V7Idea/logout.js"></script>
    <script src="/js/js.cookie.js"></script>
    <script src="/js/V7Idea/Pages/page_default.js?{!! time(); !!}"></script>
    <script>
        $(function() {
            if(isHighDensity()) {
                $.getScript( "/assets/js/custom/dense.min.js", function(data) {
                    // enable hires images
                    // altair_helpers.retina_images();
                });
            }
            if(Modernizr.touch) {
                // fastClick (touch devices)
                FastClick.attach(document.body);
            }
        });
        $window.load(function() {
            // ie fixes
            altair_helpers.ie_fix();
        });
    </script>

    <!-- 可以改畫面顏色的小東西 -->
    <!-- <div id="style_switcher">
        <div id="style_switcher_toggle"><i class="material-icons">&#xE8B8;</i></div>
        <div class="uk-margin-medium-bottom">
            <h4 class="heading_c uk-margin-bottom">Colors</h4>
            <ul class="switcher_app_themes" id="theme_switcher">
                <li class="app_style_default active_theme" data-app-theme="">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_a" data-app-theme="app_theme_a">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_b" data-app-theme="app_theme_b">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_c" data-app-theme="app_theme_c">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_d" data-app-theme="app_theme_d">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_e" data-app-theme="app_theme_e">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_f" data-app-theme="app_theme_f">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_g" data-app-theme="app_theme_g">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_h" data-app-theme="app_theme_h">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_i" data-app-theme="app_theme_i">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
                <li class="switcher_theme_dark" data-app-theme="app_theme_dark">
                    <span class="app_color_main"></span>
                    <span class="app_color_accent"></span>
                </li>
            </ul>
        </div>
        <div class="uk-visible-large uk-margin-medium-bottom">
            <h4 class="heading_c">Sidebar</h4>
            <p>
                <input type="checkbox" name="style_sidebar_mini" id="style_sidebar_mini" data-md-icheck />
                <label for="style_sidebar_mini" class="inline-label">Mini Sidebar</label>
            </p>
            <p>
                <input type="checkbox" name="style_sidebar_slim" id="style_sidebar_slim" data-md-icheck />
                <label for="style_sidebar_slim" class="inline-label">Slim Sidebar</label>
            </p>
        </div>
        <div class="uk-visible-large uk-margin-medium-bottom">
            <h4 class="heading_c">Layout</h4>
            <p>
                <input type="checkbox" name="style_layout_boxed" id="style_layout_boxed" data-md-icheck />
                <label for="style_layout_boxed" class="inline-label">Boxed layout</label>
            </p>
        </div>
        <div class="uk-visible-large">
            <h4 class="heading_c">Main menu accordion</h4>
            <p>
                <input type="checkbox" name="accordion_mode_main_menu" id="accordion_mode_main_menu" data-md-icheck />
                <label for="accordion_mode_main_menu" class="inline-label">Accordion mode</label>
            </p>
        </div>
    </div> -->

    @yield('scriptArea')

    <!-- Cookie Consent by PrivacyPolicies.com https://www.PrivacyPolicies.com -->
    <script type="text/javascript" src="//www.privacypolicies.com/public/cookie-consent/4.0.0/cookie-consent.js" charset="UTF-8"></script>
    <script type="text/javascript" charset="UTF-8">
    document.addEventListener('DOMContentLoaded', function () {
    cookieconsent.run({"notice_banner_type":"simple","consent_type":"implied","palette":"dark","language":"en","page_load_consent_levels":["strictly-necessary","functionality","tracking","targeting"],"notice_banner_reject_button_hide":false,"preferences_center_close_button_hide":true,"page_refresh_confirmation_buttons":false,"website_name":"Linxdot Housekeeping"});
    });
    </script>

    <noscript>Cookie Consent by <a href="https://www.privacypolicies.com/" rel="noopener">Privacy Policies website</a></noscript>
    <!-- End Cookie Consent by PrivacyPolicies.com https://www.PrivacyPolicies.com -->

    <!-- Below is the link that users can use to open Preferences Center to change their preferences. Do not modify the ID parameter. Place it where appropriate, style it as needed. -->

    <a href="#" id="open_preferences_center">Update cookies preferences</a>

</body>
</html>
