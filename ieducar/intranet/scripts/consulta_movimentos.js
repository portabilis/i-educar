$j(function () {
  let consulta = {
    links: $j('.mostra-consulta'),
    dialogContainer: $j('#dialog-container'),
    dialog: undefined,
    createDialog: function() {
      if (this.dialogContainer.length < 1) {
        $j('body').append(
          '<div id="dialog-container" style="max-height: 80vh; width: 520px; overflow: auto;">' +
            '<div class="msg"></div>' +
            '<div class="table"></div>' +
          '</div>'
        );

        this.dialogContainer = $j('#dialog-container');
      }

      this.dialog = this.dialogContainer.dialog({
        autoOpen: false,
        closeOnEscape: true,
        draggable: false,
        width: 520,
        modal: true,
        resizable: true,
        title: 'Alunos'
      });
    },
    makeTable: function (data) {
      let dialog = this.dialogContainer;
      let tableContainer = dialog.find('.table');
      let msgContainer = dialog.find('.msg');

      tableContainer
        .empty()
        .hide();

      msgContainer.hide();

      let html = '<table class="tablelistagem" style="width: 100%;" cellspacing="1" cellpadding="4" border="0">';

      html += '<tr><td class="formdktd">&nbsp;</td><td class="formdktd">Nome</td><td class="formdktd">Turma</td></tr>';

      $j.each(data, function (i, v) {
        html += '<tr>';
        html += '<td class="formmdtd"><a href="/intranet/educar_matricula_det.php?cod_matricula=' + v.cod_matricula +'">' + (i + 1) + '</a></td>';
        html += '<td class="formmdtd"><a href="/intranet/educar_matricula_det.php?cod_matricula=' + v.cod_matricula +'">' + v.nome.toUpperCase() + '</a></td>';
        html += '<td class="formmdtd"><a href="/intranet/educar_matricula_det.php?cod_matricula=' + v.cod_matricula +'">' + v.nm_turma + '</a></td>';
        html += '</tr>';
      });

      html += '</table>';

      tableContainer
        .html(html)
        .show();

      this.dialog.dialog('option', 'position', {my: 'center', at:'center', of: window});
    },
    showMsg: function (msg) {
      let dialog = this.dialogContainer;
      let tableContainer = dialog.find('.table');
      let msgContainer = dialog.find('.msg');

      tableContainer.hide();
      msgContainer.html(msg).show();
    },
    bind: function () {
      let that = this;

      this.links.on('click', function (e) {
        e.preventDefault();

        let $this = $j(this);
        let api = $this.data('api');
        let params = $this.data('params');

        params.tipo = $this.data('tipo');
        params.resource = 'alunos';
        params.oper = 'get';

        that.request(api, params);

        return false;
      });
    },
    request: function (api, params) {
      let url  = '/module/Api/' + api;
      let that = this;

      if (!this.dialogContainer.dialog('isOpen')) {
        this.dialogContainer.dialog('open');
      }

      this.showMsg('Aguarde');

      $j.getJSON(url, params, function (d) {
        if (d.alunos.length < 1) {
          that.showMsg('Nenhum dado retornado.');
        } else {
          that.makeTable(d.alunos);
        }
      });
    },
    init: function () {
      this.createDialog();
      this.bind();
    }
  };

  consulta.init();
});
