function getNotifications() {
  $j.get("/notificacoes/retorna-notificacoes-usuario", function (data) {
    $j.each(data, function( index, value ) {
      notRead = value.read_at == null;
      className = notRead ? 'not-read' : 'read';

      $j('.dropdown-content-notifications').prepend('<a href="' + value.link + '" data-id="' + value.id + '" class="' +className+ '">' + value.text + '</a>');

      if(notRead) {
        $j('.notification-balloon').show();
      } 
    });
  });
}

$j('.dropdown.notifications').click(function() {
  if ($j('.dropdown-content-notifications').is(':visible')) {
      $j('.dropdown-content-notifications').css('display','none');
      $j('.dropdown-content-notifications a.not-read').addClass('read');
      $j('.dropdown-content-notifications a.not-read').removeClass('not-read');

  } else {
      openBoxNotification();
  }
  event.stopPropagation();
});
$j(document).click(function() {
  $j('.dropdown-content-notifications').css('display','none');
  $j('.dropdown-content-notifications a.not-read').addClass('read');
  $j('.dropdown-content-notifications a.not-read').removeClass('not-read');
});

function openBoxNotification() {
  $j('.dropdown-content-notifications').css('display','block');
  $j('.notification-balloon').hide();

  notifications = [];

  $j.each($j('.dropdown-content-notifications').find('a'),function(index, value){
    notifications.push($j(value).data('id'));
  });

  $j.post("/notificacoes/marca-como-lida", {"notifications":notifications});
}