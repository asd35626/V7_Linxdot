function logout() {
  var token = Cookies.get('authToken');
  $.ajax({
      url: "/api/v1/UserProcessTickets/" + token,
      type: 'DELETE',
      success: function(response) {
          Cookies.remove('authToken');
          window.location.replace("/Admin/Login");
      },
      error: function(xhr, ajaxOptions, thrownError) {
          // window.location.replace("/admin/login");
          // 			alert(xhr.status);
          // 			alert(xhr.statusText);
          // 			alert(xhr.responseText);
      },
      cache: false,
      contentType: false,
      processData: false
  });
}
