$j(function () {
  $j('[data-toggle="tooltip"]').tooltip()
})

function trocaDisplay(id) {
  let element = document.getElementById(id);
  element.style.display = (element.style.display == 'none') ? 'inline' : 'none';
}

function popless() {
  let campoServidor = #cod_servidor;
  let campoInstituicao = #ref_cod_instituicao;
  pesquisa_valores_popless('educar_servidor_nivel_cad.php?ref_cod_servidor=' + campoServidor + '&ref_cod_instituicao=' + campoInstituicao, '');
}

function pesquisa_valores_popless(caminho, campo) {
  new_id = DOM_divs.length;
  div = 'div_dinamico_' + new_id;
  if (caminho.indexOf('?') == -1) {
    showExpansivel(1024, 480, '<iframe src="' + caminho + '?campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="100%" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores');
  } else {
    showExpansivel(1024, 480, '<iframe src="' + caminho + '&campo=' + campo + '&div=' + div + '&popless=1" frameborder="0" height="100%" width="100%" marginheight="0" marginwidth="0" name="temp_win_popless"></iframe>', 'Pesquisa de valores');
  }
}


function makeDialog (params) {
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

function modalExcluir(id) {

  let content = 'O histórico de afastamento selecionado será removido. Deseja prosseguir?';

  makeDialog({
      content: content,
      title: 'Atenção!',
      maxWidth: 860,
      width: 860,
      modal: true,
      close: function () {
        $j(this).dialog('destroy');
      },
      buttons: [{
        text: 'Sim',
        click: function () {
          $j.ajax({
            url: '/api/employee-withdrawal/' + id,
            type: 'delete',
            async: false,
            processData: false,
            contentType: false,
            success: function (dataResponse) {
              if (dataResponse.error) {
                messageUtils.error(dataResponse.message);
              } else {
                $j('#'+ id).remove();
                if ($j('#historico_afastamento tr').length === 1) {
                  $j('#tr_historico_afastamento').remove();
                }
                messageUtils.success('Afastamento do servidor removido com sucesso');
                location.reload();
              }
            },
            error: function () {
              messageUtils.error('Não foi possível remover o afastamento do servidor');
            }
          })

          $j(this).dialog('destroy');
        }
      }, {
        text: 'Não',
        click: function () {
          $j(this).dialog('destroy');
        }
      }]
    });
}


