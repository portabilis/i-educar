function startListenChannel(notificationsChannel) {
  window.Echo.channel(notificationsChannel).listen('NotificationEvent', (e) => {
    let notification = e.notification;
    let url = e.url;
    let unread = notification.read_at == null;
    let className = unread ? 'unread' : 'read';
    let dateObj = new Date(notification.created_at);
    let dateString = dateObj.toLocaleString('pt-BR');

    $j('.dropdown-content-notifications .notifications-bar').after(`
      <a href="` + url + `" onclick="markAsRead(this)" data-id="` + notification.id + `" class="` +className+ `" target="_blank">
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
      $j(".dropdown-content-notifications a[data-id='" + notifications[keyRemove] + "']").remove();
    }
    updateNotReadCount();
  });
  updateNotReadCount();
}

function updateNotReadCount() {
  $j.get("/notificacoes/quantidade-nao-lidas", function (data) {
    $j('.btn-mark-all-read .not-read-count').text(data);
  });
};

function getNotifications() {
  $j.get("/notificacoes/retorna-notificacoes-usuario", function (data) {
    $j.each(data, function( index, value ) {
      let unread = value.read_at == null;
      let className = unread ? 'unread' : 'read';
      let dateObj = new Date(value.created_at);
      let dateString = dateObj.toLocaleString('pt-BR');

      $j('.dropdown-content-notifications').append(`
        <a href="` + value.link + `" onclick="markAsRead(this)" data-id="` + value.id + `" class="` +className+ `" target="_blank">
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
  } else {
      openBoxNotification();
  }
  event.stopPropagation();
});

$j(document).click(function() {
  if ($j('.dropdown-content-notifications').is(':visible')) {
    $j('.dropdown-content-notifications').css('display','none');
  }
});

function openBoxNotification() {
  $j('.dropdown-content-notifications').css('display','block');
}

function markAsRead(link, removeParent = false) {
  let notification = [$j(link).attr('data-id')];

  $j.post("/notificacoes/marca-como-lida", {"notifications":notification});

  if (removeParent) {
    $j(link).parent().parent().addClass('read');
    $j(link).parent().parent().removeClass('unread');
    $j(link).parent().parent().find('.text-status').text('Lida');
    return;
  }

  $j(link).addClass('read');
  $j(link).removeClass('unread');
}


$j('.btn-mark-all-read').click(function(){
  $j('.dropdown-content-notifications a.unread').addClass('read');
  $j('.dropdown-content-notifications a.unread').removeClass('unread');
  $j('.notification-balloon').hide();
  $j.post("/notificacoes/marca-todas-como-lidas");
  $j('.btn-mark-all-read .not-read-count').text(0);
});
