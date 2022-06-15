//default page:

var navMenu = "#menuArea";

$.ajaxPrefilter(function (options, originalOptions, jqXHR) {
    options.async = true;
});

$(document).ready(function(){
    generateMenu();
});

// 產生選單;
function generateMenu() {
    // 檢查Token
    var id = Cookies.get('authToken');
    if(id == undefined){
      Cookies.remove('authToken');
      window.location.replace("/Admin/Login");
    }else{
      $.ajax({
        url: "/api/v1/AdminDefaultPermission/" + id,
        type: 'GET',
        headers: {
            'Authorization': Cookies.get('authToken')
        },
        success: function (response) {
            var navMenuContent = "";
            var userProfileContent = "";
            
            if(response.status == 0){
            var mainList = response.data.data;

            navMenuContent = getContent(mainList, response.data.UserType);

            reGenerateLeftMenu(navMenuContent);
            //set MemberName
            $('#account').text('Hello，'+response.data.MemberName);
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
                alert('沒有存取權限');
                window.location.replace("/Admin/Login");
        },
        cache: false,
        contentType: false,
        processData: false
      });
    }

}

function reGenerateLeftMenu(data) {
    console.log('reGenerateLeftMenu init()!');

    var oldData = $(navMenu).html();

    $(navMenu).html(data + oldData);
    // main sidebar
    altair_main_sidebar.init();
    // secondary sidebar
    altair_secondary_sidebar.init();
    $sidebar_main_toggle.on('click', function(e) {
        console.log('sidebar_main_toggle is clicked!');
        e.preventDefault();
        ( $body.hasClass('sidebar_main_active') || ($body.hasClass('sidebar_main_open') && $window.width() >= 1220) ) ? altair_main_sidebar.hide_sidebar() : altair_main_sidebar.show_sidebar();
    });
}

function pageInit() {
    var $switcher = $('#style_switcher'),
        $switcher_toggle = $('#style_switcher_toggle'),
        $theme_switcher = $('#theme_switcher'),
        $mini_sidebar_toggle = $('#style_sidebar_mini'),
        $slim_sidebar_toggle = $('#style_sidebar_slim'),
        $boxed_layout_toggle = $('#style_layout_boxed'),
        $accordion_mode_toggle = $('#accordion_mode_main_menu'),
        $html = $('html'),
        $body = $('body');

    $switcher_toggle.click(function(e) {
        e.preventDefault();
        $switcher.toggleClass('switcher_active');
    });

    $theme_switcher.children('li').click(function(e) {
        e.preventDefault();
        var $this = $(this),
            this_theme = $this.attr('data-app-theme');

        $theme_switcher.children('li').removeClass('active_theme');
        $(this).addClass('active_theme');
        $html
            .removeClass('app_theme_a app_theme_b app_theme_c app_theme_d app_theme_e app_theme_f app_theme_g app_theme_h app_theme_i app_theme_dark')
            .addClass(this_theme);

        if(this_theme == '') {
            localStorage.removeItem('altair_theme');
            $('#kendoCSS').attr('href','bower_components/kendo-ui/styles/kendo.material.min.css');
        } else {
            localStorage.setItem("altair_theme", this_theme);
            if(this_theme == 'app_theme_dark') {
                $('#kendoCSS').attr('href','bower_components/kendo-ui/styles/kendo.materialblack.min.css')
            } else {
                $('#kendoCSS').attr('href','bower_components/kendo-ui/styles/kendo.material.min.css');
            }
        }
    });

    // hide style switcher
    $document.on('click keyup', function(e) {
        if( $switcher.hasClass('switcher_active') ) {
            if (
                ( !$(e.target).closest($switcher).length )
                || ( e.keyCode == 27 )
            ) {
                $switcher.removeClass('switcher_active');
            }
        }
    });

    // get theme from local storage
    if(localStorage.getItem("altair_theme") !== null) {
        $theme_switcher.children('li[data-app-theme='+localStorage.getItem("altair_theme")+']').click();
    }

    // toggle mini sidebar
    // change input's state to checked if mini sidebar is active
    if((localStorage.getItem("altair_sidebar_mini") !== null && localStorage.getItem("altair_sidebar_mini") == '1') || $body.hasClass('sidebar_mini')) {
        $mini_sidebar_toggle.iCheck('check');
    }

    $mini_sidebar_toggle
        .on('ifChecked', function(event){
            $switcher.removeClass('switcher_active');
            localStorage.setItem("altair_sidebar_mini", '1');
            localStorage.removeItem('altair_sidebar_slim');
            location.reload(true);
        })
        .on('ifUnchecked', function(event){
            $switcher.removeClass('switcher_active');
            localStorage.removeItem('altair_sidebar_mini');
            location.reload(true);
        });

    // toggle slim sidebar
    // change input's state to checked if mini sidebar is active
    if((localStorage.getItem("altair_sidebar_slim") !== null && localStorage.getItem("altair_sidebar_slim") == '1') || $body.hasClass('sidebar_slim')) {
        $slim_sidebar_toggle.iCheck('check');
    }

    $slim_sidebar_toggle
        .on('ifChecked', function(event){
            $switcher.removeClass('switcher_active');
            localStorage.setItem("altair_sidebar_slim", '1');
            localStorage.removeItem('altair_sidebar_mini');
            location.reload(true);
        })
        .on('ifUnchecked', function(event){
            $switcher.removeClass('switcher_active');
            localStorage.removeItem('altair_sidebar_slim');
            location.reload(true);
        });

    // toggle boxed layout
    if((localStorage.getItem("altair_layout") !== null && localStorage.getItem("altair_layout") == 'boxed') || $body.hasClass('boxed_layout')) {
        $boxed_layout_toggle.iCheck('check');
        $body.addClass('boxed_layout');
        $(window).resize();
    }

    $boxed_layout_toggle
        .on('ifChecked', function(event){
            $switcher.removeClass('switcher_active');
            localStorage.setItem("altair_layout", 'boxed');
            location.reload(true);
        })
        .on('ifUnchecked', function(event){
            $switcher.removeClass('switcher_active');
            localStorage.removeItem('altair_layout');
            location.reload(true);
        });

    // main menu accordion mode
        if($sidebar_main.hasClass('accordion_mode')) {
            $accordion_mode_toggle.iCheck('check');
        }

        $accordion_mode_toggle
            .on('ifChecked', function(){
                $sidebar_main.addClass('accordion_mode');
            })
            .on('ifUnchecked', function(){
                $sidebar_main.removeClass('accordion_mode');
            });
}

function getContent(data, UserType){
    console.log('UserType:'+UserType);
    navMenuContent = '';
    if(UserType == 50){
        $.each(data, function(index, obj){
            var Code = '&#xE871;';
            if (obj.Code != undefined && obj.Code != '') {
                Code = obj.Code;
            }

            navMenuContent += '<li title="' + obj.Name + '">' +
                                      '<a href="' + obj.Url + '">' +
                                        '<span class="menu_icon"><i class="material-icons">' + Code + '</i></span>' +
                                        '<span class="menu_title">' + obj.Name + '</span>' +
                                      '</a>';

            if(obj.SubList.length > 0){
                navMenuContent += '<ul>';
                $.each(obj.SubList, function(key, subObj){
                    if(subObj.IfAccess){
                        navMenuContent += "<li>";
                        navMenuContent += "<a href=\"" +subObj.Url + "\">" + subObj.Name + "</a>";
                        navMenuContent += "</li>";
                    }
                });
                navMenuContent += "</ul>";
            }
            navMenuContent += "</li>";
        });
    }else if(UserType == 20){
        $.each(data, function(index, obj){
            if(obj.SubList.length > 0){
                $.each(obj.SubList, function(key, subObj){
                    if(subObj.IfAccess){
                        navMenuContent += "<li>";
                        navMenuContent += "<a href=\"" +subObj.Url + "\">" + subObj.Name + "</a>";
                        navMenuContent += "</li>";
                    }
                });
                navMenuContent += "</ul>";
                navMenuContent += "</li>";
            }
        });
    }else{
        $.each(data, function(index, obj){
            if(obj.SubList.length > 0){
                var Code = '&#xE871;';
                if (obj.Code != undefined && obj.Code != '') {
                    Code = obj.Code;
                }

                navMenuContent += '<li title="' + obj.Name + '">' +
                                          '<a href="' + obj.Url + '">' +
                                            '<span class="menu_icon"><i class="material-icons">' + Code + '</i></span>' +
                                            '<span class="menu_title">' + obj.Name + '</span>' +
                                          '</a>';
                navMenuContent += '<ul>';

                $.each(obj.SubList, function(key, subObj){
                    if(subObj.IfAccess){
                        navMenuContent += "<li>";
                        navMenuContent += "<a href=\"" +subObj.Url + "\">" + subObj.Name + "</a>";
                        navMenuContent += "</li>";
                    }
                });
                navMenuContent += "</ul>";
                navMenuContent += "</li>";
            }
        });
    }
    
    return navMenuContent;
}
$(function(){pageInit();});