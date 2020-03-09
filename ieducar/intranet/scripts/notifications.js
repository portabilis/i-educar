function startListenChannel(notificationsChannel) {
  window.Echo.channel(notificationsChannel).listen('NotificationEvent', (e) => {
    let notification = e.notification;
    let unread = notification.read_at == null;
    let className = unread ? 'unread' : 'read';
    let dateObj = new Date(notification.created_at);
    let dateString = dateObj.toLocaleString('pt-BR');

    $j('.dropdown-content-notifications .notifications-bar').after(`
      <a href="` + notification.link + `" data-id="` + notification.id + `" class="` +className+ `">
        <p>` + notification.text  + `</p>
        <p class="date-notification"> ` + dateString + `</p>
      </a>`);

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
      let unread = value.read_at == null;
      let className = unread ? 'unread' : 'read';
      let dateObj = new Date(value.created_at);
      let dateString = dateObj.toLocaleString('pt-BR');

      $j('.dropdown-content-notifications').append(`
        <a href="` + value.link + `" data-id="` + value.id + `" class="` +className+ `">
          <p>` + value.text  + `</p>
          <p class="date-notification"> ` + dateString + `</p>
        </a>`);

      if(unread) {
        $j('.notification-balloon').show();
      }
    });

  });
}

$j('.dropdown.notifications').click(function() {
  if ($j('.dropdown-content-notifications').is(':visible')) {
      $j('.dropdown-content-notifications').css('display','none');
      $j('.dropdown-content-notifications a.unread').addClass('read');
      $j('.dropdown-content-notifications a.unread').removeClass('unread');
      $j('.notification-balloon').hide();
  } else {
      openBoxNotification();
  }
  event.stopPropagation();
});

$j(document).click(function() {
  if ($j('.dropdown-content-notifications').is(':visible')) {
    $j('.dropdown-content-notifications').css('display','none');
    $j('.dropdown-content-notifications a.unread').addClass('read');
    $j('.dropdown-content-notifications a.unread').removeClass('unread');
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
