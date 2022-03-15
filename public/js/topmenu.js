
$(document).ready(function() {
    if (document.getElementById('navmenu')) {
        $("#navmenu-h li,#navmenu-v li").hover(
        function() { $(this).addClass("iehover"); },
        function() { $(this).removeClass("iehover"); }
        );
  }
});