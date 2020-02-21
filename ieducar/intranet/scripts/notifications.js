function getNotifications() {
  $j.get("/notificacoes/retorna-notificacoes-usuario", function (data) {
    $j.each(data, function( index, value ) {
      notRead = value.read_at == null;
      className = notRead ? 'not-read' : 'read';

      $j('.dropdown-content-notifications').append('<a href="' + value.link + '" data-id="' + value.id + '" class="' +className+ '">' + value.text + '</a>');

      if(notRead) {
        $j('.notification-balloon').show();
      }
    });

    $j('.dropdown-content-notifications').append(' <a href="/notificacoes" class="btn-all-notifications">Ver todas</a>');
  });
}

$j('.dropdown.notifications').click(function() {
  if ($j('.dropdown-content-notifications').is(':visible')) {
      $j('.dropdown-content-notifications').css('display','none');
      $j('.dropdown-content-notifications a.not-read').addClass('read');
      $j('.dropdown-content-notifications a.not-read').removeClass('not-read');
      $j('.notification-balloon').hide();

  } else {
      openBoxNotification();
  }
  event.stopPropagation();
});
$j(document).click(function() {
  if ($j('.dropdown-content-notifications').is(':visible')) {
    $j('.dropdown-content-notifications').css('display','none');
    $j('.dropdown-content-notifications a.not-read').addClass('read');
    $j('.dropdown-content-notifications a.not-read').removeClass('not-read');
    $j('.notification-balloon').hide();
  }
});

function openBoxNotification() {
  $j('.dropdown-content-notifications').css('display','block');

  notifications = [];

  $j.each($j('.dropdown-content-notifications').find('a'),function(index, value){
    notifications.push($j(value).data('id'));
  });

  $j.post("/notificacoes/marca-como-lida", {"notifications":notifications});
}
