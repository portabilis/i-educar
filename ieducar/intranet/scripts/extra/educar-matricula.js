function makeDialog(params) {
  params.closeOnEscape = false;
  params.draggable = false;
  params.modal = true;

  var container = $j('#dialog-container');

  if (container.length < 1) {
    $j('body').append('<div id="dialog-container" style="width: 500px;"></div>');
    container = $j('#dialog-container');
  }

  if (container.hasClass('ui-dialog-content')) {
    container.dialog('destroy');
  }

  container.empty();
  container.html(params.content);

  delete params['content'];

  container.dialog(params);
}

function showConfirmationMessage(redirect) {
  makeDialog({
    content: 'Prosseguir com o cancelamento de matrícula resultará na perda dos dados vinculados a mesma, como lançamentos de notas e frequências.',
    title: 'Atenção!',
    maxWidth: 600,
    width: 600,
    close: function () {
      $j('#dialog-container').dialog('destroy');
    },
    buttons: [{
      text: 'Continuar',
      click: function () {
        $j('#formcadastro').removeAttr('onsubmit');
        $j('#dialog-container').dialog('destroy');
        go(redirect);
      }
    }, {
      text: 'Cancelar',
      click: function () {
        $j('#dialog-container').dialog('destroy');
      }
    }]
  });
}
