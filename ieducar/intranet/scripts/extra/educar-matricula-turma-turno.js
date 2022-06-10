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
    content: 'Você está alterando o turno do(a) aluno(a). Deseja continuar?',
    title: 'Atenção!',
    maxWidth: 600,
    width: 600,
    close: function () {
      $j('#dialog-container').dialog('destroy');
    },
    buttons: [{
      text: 'Confirmar',
      click: function () {
        $j('#formcadastro').removeAttr('onsubmit');
        $j('#formcadastro').submit();
        $j('#dialog-container').dialog('destroy');
        //windowUtils.redirect('/intranet/educar_matricula_det.php?cod_matricula='+$j('#formcadastro input[name="cod_matricula"]').val());
      }
    }, {
      text: 'Cancelar',
      click: function () {
        $j('#dialog-container').dialog('destroy');
      }
    }]
  });
}
