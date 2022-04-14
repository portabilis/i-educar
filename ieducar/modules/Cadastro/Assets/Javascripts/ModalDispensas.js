ModalDispensas = {
  links: $j('.mostra-consulta'),
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

    this.dialog = this.dialogContainer.dialog({
      autoOpen: false,
      closeOnEscape: false,
      draggable: false,
      width: 820,
      modal: true,
      resizable: true,
      title: 'Listagem de dispensas',
      open: function (event, ui) {
        $j(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
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
      this.getContainerMessage()
    );

    msgContainer.append(this.getDispensasTable(data));
    msgContainer.append(this.getActionButtons());

    this.dialog.dialog('option', 'position', {my: 'center', at: 'top', of: window});

    if (!this.dialogContainer.dialog('isOpen')) {
      this.dialogContainer.dialog('open');
    }

    $j('#dialog-container-dispensas').parent().css({position: 'fixed'}).css({top: '25px'})
  },
  getContainerMessage: function () {
    let msg = '<h4>Não foi possível remover o componente pois existem registros de dispensa vinculadas a ele</h4>';

    msg += '<small>Clique no aluno para visualizar a dispensa:</small><br><br>';

    return msg;
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

    buttonsTd.append('<input type="button" class="botao" value="Voltar" onclick="ModalDispensas.close()">');

    buttonsTr.append(buttonsTd);
    buttonsTable.append(buttonsTr);

    return buttonsTable;
  },
  getDispensasTable: function (data) {
    let div = $j('<div>').attr({
      'id': 'div-dispensas'
    });

    $j.each(data, function (key, dispensas) {
      let botaoVerMais = false;
      let details = $j('<details>');
      details.append(
        $j('<summary>').append(dispensas.nomeComponente)
      );

      let table = $j('<table>').attr({
        'class': 'tablelistagem',
        'cellspacing': '1',
        'cellpadding': '4',
        'border': '0',
        'style': 'width: 100%;'
      });

      if (dispensas.dispensas.length > 15) {
        botaoVerMais = true;
      }

      $j.each(dispensas.dispensas, function (key, matricula) {
        let tr = $j('<tr>');

        let linkDispensas = $j('<a>').attr({
          href: '/intranet/educar_dispensa_disciplina_lst.php?ref_cod_matricula=' + matricula.idMatricula,
          target: '_blank'
        });

        linkDispensas.append('(' + matricula.anoMatricula + ')' + ' - ' + matricula.nomeAluno);

        tr.append($j('<td>').attr({
          class: 'formmdtd'
        }).append(
          linkDispensas
        ));

        table.append(tr);

        return key < 15;
      });

      if (botaoVerMais) {
        let requestData = {
          ref_cod_instituicao: $j('#ref_cod_instituicao').val(),
          ref_cod_escola:  $j('#ref_cod_escola_').val(),
          ref_cod_curso:  $j('#ref_cod_curso_').val(),
          ref_cod_serie:  $j('#ref_cod_serie_').val(),
          ref_cod_componente_curricular: dispensas.idComponente
        };

        let linkVerMais = null;
        if ($j('#permissao_consulta_dispensas').val() == 1){
          linkVerMais = $j('<a>').attr({
            href: '/consulta-dispensas?' + $j.param(requestData),
            target: '_blank'
          });
        } else {
          linkVerMais = $j('<a>').attr({
            href: '#',
            onclick: 'handleMessages([{type : \'error\', msg : \'Você não tem permissão para acessar esse recurso.\'}]);'
          });
        }

        linkVerMais.append('Ver mais');

        let tr = $j('<tr>');

        tr.append($j('<td>').attr({
          class: 'formmdtd'
        }).append(
          linkVerMais
        ));

        table.append(tr);
      }

      details.append(table);

      div.append(details);
    });

    return div;
  },
  close: function () {
    this.dialogContainer.dialog('close');
  },
  init: function (idContainer, data) {
    this.idContainer = idContainer;

    this.createDialog();
    this.makeHtml(data);
  }
};
