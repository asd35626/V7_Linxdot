function createMainMenu(thisId){
  $.ajax({
    url: "/api/v1/AdminDefaultPermission/" + thisId,
    type: 'GET',
    headers: {
        'Authorization': Cookies.get('authToken')
    },
    success: function (response) {
      var navMenu = "#navmenu-h";
      var userProfile = "#userProfile";
      var navMenuContent = "";
      var userProfileContent = "";
      //mainList
      navMenuContent += "<li><a href=\"/Default\">首頁</a></li>";
      if(response.status == 1){
        var mainList = response.data.data;
        $.each(mainList, function(index, obj){
          if(obj.SubList.length > 0){
            navMenuContent += "<li>";
            navMenuContent += "<a href=\"#\">" + obj.Name + "</a>";
            // if(obj.SubList.length > 0){
              navMenuContent += "<ul>";
              $.each(obj.SubList, function(key, subObj){
                if(subObj.IfAccess){
                  navMenuContent += "<li>";
                  navMenuContent += "<a href=\"" +subObj.Url + "\">" + subObj.Name + "</a>";
                  navMenuContent += "</li>";
                }
              });
              navMenuContent += "</ul>";
            // }
            navMenuContent += "</li>";
          }
        });

        $(navMenu).html(navMenuContent);
        //user info
        userProfileContent += "登入者：" + response.data.MemberNo +"(" + response.data.TypeName + ")";
        userProfileContent += "<font color=\"#FFFFFF\"> | </font>" ;
        userProfileContent += "身份：" + response.data.DegreeName;
        userProfileContent += "<font color=\"#FFFFFF\"> | </font> ";
        userProfileContent += "<a href=\"javascript:;\" class=\"yellowlink\" onclick=\"logout();\">登出</a>"

        $(userProfile).html(userProfileContent);
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
