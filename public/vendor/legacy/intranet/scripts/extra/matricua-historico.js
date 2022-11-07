// Data de saída
window.onload = function () {

  const makeDialog = function (params) {
    let container = $j('#dialog-container');

    if (container.length < 1) {
      $j('body').append('<div id="dialog-container" style="width: 400px;"></div>');
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

  const defaultModal = function (message) {
    makeDialog({
      content: message,
      title: 'Atenção!',
      maxWidth: 400,
      width: 400,
      close: function () {
        $j('#dialog-container').dialog('destroy');
      },
      buttons: [{
        text: 'Ok',
        click: function () {
          $j('#dialog-container').dialog('destroy');
        }
      },]
    });
  }

  const remanejado = document.getElementById('remanejado');
  const deletationDate = document.getElementById('data_exclusao');

  const validationDateExclutionRule = function () {
    const deletationDateValue = deletationDate.value;

    if (remanejado.checked && typeof deletationDateValue === 'string' && deletationDateValue === '') {
      defaultModal('A data de saída é obrigatória quando é marcado o remanejamento.')
      return false;
    }
    acao();
  }

  const submitValitation = document.getElementById('btn_enviar');

  submitValitation.onclick = function () {
    validationDateExclutionRule();
  }

  const valitadeDateExclution = function () {

    if (remanejado.checked === true) {
      makeRequired('data_exclusao');

      document.getElementById('transferido').disabled = true;
      document.getElementById('reclassificado').disabled = true;
      document.getElementById('abandono').disabled = true;
      document.getElementById('falecido').disabled = true;

      return;
    }

    document.getElementById('transferido').disabled = false;
    document.getElementById('reclassificado').disabled = false;
    document.getElementById('abandono').disabled = false;
    document.getElementById('falecido').disabled = false;

    makeUnrequired('data_exclusao');
  };

  remanejado.addEventListener('change', valitadeDateExclution)

  valitadeDateExclution();
}




