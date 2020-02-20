function getNotifications() {
  $j.get("/notificacoes/retorna-notificacoes-usuario", function (data) {
    $j.each(data, function( index, value ) {
      $j('.content-notifications').prepend('<a href="' + value.link + '">' + value.text + '</a>');
    });
  });
}
