function startListenChannel(notificationsChannel) {
  window.Echo.channel(notificationsChannel).listen('NotificationEvent', (e) => {
    let notification = e.notification;
    let notRead = notification.read_at == null;
    let className = notRead ? 'not-read' : 'read';
    $j('.dropdown-content-notifications').prepend('<a href="' + notification.link + '" data-id="' + notification.id + '" class="' +className+ '">' + notification.text + '</a>');
    $j('.notification-balloon').show();

    let notifications = [];

    $j.each($j('.dropdown-content-notifications').find('a'),function(index, value){
      notifications.push($j(value).data('id'));
    });

    if (notifications.length > 5) {
      let keyRemove = notifications.length - 2;
      $j("a[data-id='" + notifications[keyRemove] + "']").remove();
    }
  });
}

function getNotifications() {
  $j.get("/notificacoes/retorna-notificacoes-usuario", function (data) {
    $j.each(data, function( index, value ) {
      let notRead = value.read_at == null;
      let className = notRead ? 'not-read' : 'read';

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

  let notifications = [];

  $j.each($j('.dropdown-content-notifications').find('a'),function(index, value){
    notifications.push($j(value).data('id'));
  });

  $j.post("/notificacoes/marca-como-lida", {"notifications":notifications});
}
