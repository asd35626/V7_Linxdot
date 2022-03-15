function backCheck() {
  if (window.history && window.history.pushState) {
    $(window).on('popstate', function(e) {

      var hashLocation = location.hash;
      var hashSplit = hashLocation.split("#!/");
      var hashName = hashSplit[1];
      // console.log('hashName[0]:'+hashSplit[0]);
      // console.log('hashName[1]:'+hashName);
      if (hashName !== '') {
        var hash = window.location.hash;

        if (hash === '') {
          // alert('上一頁功能已停用');
          //window.location = '/Default';
          history.pushState(null, null, window.location.pathname);
        }
      }
    });
  }
  history.pushState(null, null, window.location.pathname);
}
