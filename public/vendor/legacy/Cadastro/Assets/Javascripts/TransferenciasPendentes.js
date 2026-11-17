$j(function () {
  const dialog = $j('#pending-transfers');
  const list = $j('#pending-transfers-list');
  const bell = $j('.dropbtn.notifications');
  const badge = $j('<button>', {
    type: 'button',
    class: 'pending-transfer-count',
    title: 'Visualizar transferências internas pendentes',
  }).hide().appendTo(bell);
  const storageKey = dialog.attr('data-storage-key');
  let lastAlertedId = 0;
  let latestUnconfirmedId = 0;
  try {
    lastAlertedId = Number(window.sessionStorage.getItem(storageKey)) || 0;
  } catch (error) {
    // O aviso continua funcionando quando o navegador bloqueia o armazenamento.
  }

  function rememberAlert() {
    lastAlertedId = Math.max(lastAlertedId, latestUnconfirmedId);
    try {
      window.sessionStorage.setItem(storageKey, String(lastAlertedId));
    } catch (error) {
      // Mantém a escolha em memória quando sessionStorage não está disponível.
    }
  }
  let loading = false;
  let currentUrl = dialog.attr('data-url');
  let suppressAutoOpen = !!(window.performance && window.performance.getEntriesByType &&
    window.performance.getEntriesByType('navigation').some(function (entry) {
      return entry.type === 'back_forward';
    }));

  dialog.dialog({
    autoOpen: false,
    modal: true,
    title: 'Alunos aguardando recebimento',
    width: Math.min(900, $j(window).width() - 32),
    maxHeight: Math.floor($j(window).height() * 0.85),
    open: rememberAlert,
    close: rememberAlert,
    buttons: {
      'Visualizar mais tarde': function () {
        dialog.dialog('close');
      },
    },
  });

  function addDetail(details, label, value) {
    $j('<dt>').text(label).appendTo(details);
    $j('<dd>').text(value || 'Não informado').appendTo(details);
  }

  function render(data) {
    latestUnconfirmedId = Number(data.latest_unconfirmed_id) || 0;
    list.empty();
    $j('#pending-transfers-error').text('');
    $j('#pending-transfers-summary').text(
      data.total ? 'Transferências pendentes: ' + data.total + '. Exibindo até 20 por vez.' : 'Nenhuma transferência pendente.'
    );
    data.transfers.forEach(function (transfer) {
      const row = $j('<article>').appendTo(list);
      $j('<h3>').text(transfer.student).appendTo(row);
      const details = $j('<dl>').appendTo(row);
      addDetail(details, 'Data de nascimento', transfer.birth_date);
      addDetail(details, 'Escola de origem', transfer.origin_school);
      addDetail(details, 'Escola de destino', transfer.destination_school);
      addDetail(details, 'Série/Ano de origem', transfer.grade);
      addDetail(details, 'Data da transferência', transfer.transfer_date);

      const form = $j('<form>', {method: 'post', action: transfer.confirm_url}).appendTo(row);
      $j('<input>', {type: 'hidden', name: '_token', value: $j('meta[name="csrf-token"]').attr('content')}).appendTo(form);
      $j('<button>', {type: 'submit', class: 'btn-green pending-transfer-confirm'}).text(transfer.acknowledged ? 'Continuar matrícula / Enturmar' : 'Confirmar recebimento / Enturmar').appendTo(form);
      form.on('submit', function () {
        rememberAlert();
        form.find('button').prop('disabled', true).text('Abrindo matrícula…');
      });
    });

    badge.toggle(data.total > 0).text(data.total).attr('aria-label', data.total + ' transferências internas pendentes');
    bell.find('img').toggleClass('pending-transfer-pulse', data.total > 0);
    if (dialog.dialog('isOpen')) {
      rememberAlert();
    } else if (!suppressAutoOpen && latestUnconfirmedId > lastAlertedId) {
      dialog.dialog('open');
    }
    suppressAutoOpen = false;
    $j('#pending-transfers-previous').prop('hidden', !data.previous_url).data('url', data.previous_url);
    $j('#pending-transfers-next').prop('hidden', !data.next_url).data('url', data.next_url);
  }

  function refresh(url, force) {
    if (loading || (dialog.dialog('isOpen') && !force)) {
      return;
    }
    loading = true;
    const requestedUrl = typeof url === 'string' ? url : currentUrl;
    $j.ajax({url: requestedUrl, dataType: 'json'})
      .done(function (data) {
        currentUrl = requestedUrl;
        render(data);
      })
      .fail(function () {
        $j('#pending-transfers-error').text('Não foi possível consultar as transferências. Tente novamente.');
      })
      .always(function () { loading = false; });
  }

  badge.on('click', function (event) {
    event.preventDefault();
    event.stopPropagation();
    dialog.dialog('open');
  });
  $j('#pending-transfers-previous, #pending-transfers-next').on('click', function () {
    refresh($j(this).data('url'), true);
  });
  window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
      suppressAutoOpen = true;
      loading = false;
      dialog.dialog('close');
      refresh(undefined, true);
    }
  });
  if (window.Echo && window.useEcho) {
    window.Echo.channel(dialog.attr('data-channel')).listen('NotificationEvent', function () { refresh(undefined, true); });
  }
  refresh();
});
