ModalAlunos = {
  dialogContainer: undefined,
  dialog: undefined,
  idContainer: null,
  createDialog: function () {
    this.dialogContainer = $j('#dialog-container-' + this.idContainer);

    if (this.dialogContainer.length < 1) {
      $j('body').append(
        '<div id="dialog-container-' + this.idContainer + '" style="max-height: 80vh; width: 820px; overflow: auto;">' +
        '<div class="msg"></div>' +
        '<div class="table"></div>' +
        '</div>'
      );

      this.dialogContainer = $j('#dialog-container-' + this.idContainer);
    }

    title = 'Alunos(s) rematriculado(s)';
    if (this.idContainer == 'alunos_com_saida') {
      title = 'Aluno(s) com data de saída'
    }

    if (this.idContainer == 'alunos_sem_inep') {
      title = 'Aluno(s) sem INEP'
    }

    this.dialog = this.dialogContainer.dialog({
      autoOpen: false,
      closeOnEscape: false,
      draggable: false,
      width: 820,
      modal: true,
      resizable: true,
      title: title,
      zindex: 100000,
      open: function (event, ui) {
        $j(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
        $j(".ui-dialog").css('z-index', 100000);
      }
    });
  },
  makeHtml: function (data) {
    let dialog = this.dialogContainer;
    let tableContainer = dialog.find('.table');
    let msgContainer = dialog.find('.msg');

    tableContainer
      .empty()
      .hide();

    msgContainer.html(
      this.getListaAlunos(data)
    );

    msgContainer.append(this.getActionButtons());

    this.dialog.dialog('option', 'position', {my: 'center', at: 'top', of: window});

    if (!this.dialogContainer.dialog('isOpen')) {
      this.dialogContainer.dialog('open');
    }

    $j('#dialog-container-alunos').parent().css({position: 'fixed'}).css({top: '25px'})
  },
  getListaAlunos: function (data) {
    let ul = $j('<ul>');
    $j.each(data.split(','), function (key, aluno) {
      let li = $j('<li>').append(aluno);

      ul.append(li)
    });

    return ul;
  },
  showMsg: function (msg) {
    let dialog = this.dialogContainer;
    let tableContainer = dialog.find('.table');
    let msgContainer = dialog.find('.msg');

    tableContainer.hide();
    msgContainer.html(msg).show();
  },
  getActionButtons() {
    let buttonsTable = $j('<table>').attr({
      'class': 'tablecadastro',
      'width': '100%',
      'border': '0',
      'cellpadding': '2',
      'cellspacing': '0'
    });

    let buttonsTr = $j('<tr>').attr({
      'class': 'linhaBotoes',
      'id': 'modal-actionbtb'
    });

    let buttonsTd = $j('<td>').attr({
      'colspan': '2',
      'align': 'center'
    });

    buttonsTd.append('<input type="button" class="botao" value="Fechar" onclick="ModalAlunos.close()">');

    buttonsTr.append(buttonsTd);
    buttonsTable.append(buttonsTr);

    return buttonsTable;
  },
  getDispensasTable: function (data) {
    let div = $j('<div>').attr({
      'id': 'div-alunos'
    });

    return div;
  },
  close: function () {
    this.dialogContainer.dialog('close');
    $j('.ui-dialog').remove()
  },
  init: function (idContainer) {
    let data = this.urldecode($j('#' + idContainer).val());
    this.idContainer = idContainer;

    this.createDialog();
    this.makeHtml(data);
  },
  urldecode(text) {
    return decodeURIComponent(text.replace(/\+/g, ' '));
  }
};
