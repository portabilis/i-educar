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


function showConfirmationMessage(e) {
  makeDialog({
    content: 'Você está alterando o turno da matrícula desse(a) aluno(a). Deseja continuar?',
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
        $j('#formcadastro').submit();
        $j('#dialog-container').dialog('destroy');
      }
    }, {
      text: 'Cancelar',
      click: function () {
        $j('#dialog-container').dialog('destroy');
      }
    }]
  });
}
