ajustaTabelaDePessoasUnificadas();

$j('#btn_add_tab_add_1').click(function(){
  ajustaTabelaDePessoasUnificadas();
});

function ajustaTabelaDePessoasUnificadas() {
  $j('a[id^="link_remove["').empty().text('EXCLUIR');
  $j('input[id^="pessoa_duplicada["').attr("placeholder", "Informe nome, código, CPF ou RG da pessoa");
}

function carregaDadosPessoas() {
  let pessoas_duplicadas = [];

  $j('input[id^="pessoa_duplicada["').each(function(id, input) {
    pessoas_duplicadas.push(input.value.split(' ')[0]);
  });

  var url = getResourceUrlBuilder.buildUrl(
    '/module/Api/Pessoa',
    'dadosUnificacaoPessoa',
    {
      pessoas_ids : pessoas_duplicadas
    }
  );

  var options = {
    url      : url,
    dataType : 'json',
    success  : function(response) {
      listaDadosPessoasUnificadas(response);
    }
  };

  getResources(options);
}

function listaDadosPessoasUnificadas(response) {
  $j(`
    <tr>
      <td colspan="2">
        <h2 class="unifica_pessoa_h2">
          Seleciona a pessoa que tenha preferencialmente vínculo(s) e com dados relavantes mais completos.
        </h2>
      </td>
    </tr>
  `).insertBefore($j('.linhaBotoes'));
  $j('<tr id="lista_dados_pessoas_unificadas"></tr>').insertBefore($j('.linhaBotoes'));

  let html = `
    <td colspan="2">
    <table id="tabela_pessoas_unificadas">
      <tr class="tr_title">
        <td>Principal</td>
        <td>Vinculo</td>
        <td>Nome</td>
        <td>Nascimento</td>
        <td>Sexo</td>
        <td>CPF</td>
        <td>RG</td>
        <td>Pessoa Mãe</td>
        <td>Ação</td>
      </tr>
  `;

  response.pessoas.each(function(value, id) {
    html += '<tr id="' + value.idpes + '" class="linha_listagem">';
    html += '<td><input type="checkbox" id="' + value.idpes + '"</td>';
    html += '<td>'+ value.vinculo +'</td>';
    html += '<td>'+ value.nome +'</td>';
    html += '<td>'+ value.data_nascimento +'</td>';
    html += '<td>'+ value.sexo +'</td>';
    html += '<td>'+ value.cpf +'</td>';
    html += '<td>'+ value.rg +'</td>';
    html += '<td>'+ value.pessoa_mae +'</td>';
    html += '<td><a class="link_remove" onclick="removePessoa(' + value.idpes + ')">EXCLUIR</a></td>';
    html += '</tr>';
  });

  html += '</table></td>';

  $j('#lista_dados_pessoas_unificadas').html(html);
  $j('<tr><td class="tableDetalheLinhaSeparador" colspan="2"></td></tr>').insertAfter($j('#lista_dados_pessoas_unificadas'));
}

function removePessoa(idpes) {
  trClose = $j('#' + idpes);
  trClose.fadeOut(400, function() {
    trClose.remove();  
  });
}

var handleSelect = function(event, ui){
  $j(event.target).val(ui.item.label);
  return false;
};

  var search = function(request, response) {
  var searchPath = '/module/Api/Pessoa?oper=get&resource=pessoa-search';
  var params     = { query : request.term };

  $j.get(searchPath, params, function(dataResponse) {
  simpleSearch.handleSearch(dataResponse, response);
});
};

  function setAutoComplete() {
  $j.each($j('input[id^="pessoa_duplicada"]'), function(index, field) {

    $j(field).autocomplete({
      source    : search,
      select    : handleSelect,
      minLength : 1,
      autoFocus : true
    });

  });
}

  setAutoComplete();

  // bind events

  var $addPontosButton = $j('#btn_add_tab_add_1');

  $addPontosButton.click(function(){
  setAutoComplete();
});

  $j('#btn_enviar').val('Carregar dados');

  function showConfirmationMessage() {
  makeDialog({
    content: 'O processo de unificação de pessoas não poderá ser desfeito. Deseja continuar?',
    title: 'Atenção!',
    maxWidth: 860,
    width: 860,
    close: function () {
      $j('#dialog-container').dialog('destroy');
    },
    buttons: [{
      text: 'Confirmar',
      click: function () {
        acao();
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

  function makeDialog(params) {
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

