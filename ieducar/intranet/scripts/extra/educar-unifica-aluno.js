adicionaMaisUmaLinhaNaTabela();
ajustaTabelaDeAlunosUnificados();

$j('#btn_add_tab_add_1').click(function(){
  ajustaTabelaDeAlunosUnificados();
});

function adicionaMaisUmaLinhaNaTabela() {
  tab_add_1.addRow();
}

function carregaDadosAlunos() {
  let alunos_duplicados = [];
  let message = '';

  $j('input[id^="aluno_duplicado["').each(function(id, input) {
    if (input.value != "") {
      alunos_duplicados.push(input.value.split(' ')[0]);
    }
  });

  let alunos_sem_duplicidade = [...new Set(alunos_duplicados)];

  if (alunos_duplicados.length <= 1) {
    message = 'Informe pelo menos dois alunos para unificar.'
    defaultModal(message);
    return;
  }

  if (alunos_duplicados.length != alunos_sem_duplicidade.length) {
    message = 'Selecione alunos diferentes.'
    defaultModal(message);
    return;
  }

  var url = getResourceUrlBuilder.buildUrl(
    '/module/Api/Aluno',
    'dadosUnificacaoAlunos',
    {
      alunos_ids : alunos_duplicados
    }
  );

  var options = {
    url      : url,
    dataType : 'json',
    success  : function(response) {
      $j('#adicionar_linha').hide();
      listaDadosAlunosUnificados(response);
    }
  };

  getResources(options);
}

function listaDadosAlunosUnificados(response) {
  removeExclusaoDeAlunos();
  disabilitaSearchInputs();
  montaTabelaDadosAluno(response);
}

function montaTabelaDadosAluno(response) {
  $j('tr#lista_dados_alunos_unificados').remove().animate({});
  $j('tr#unifica_aluno_titulo').remove().animate({});

  $j('<tr id="lista_dados_alunos_unificados"></tr>').insertAfter($j('.tableDetalheLinhaSeparador').first().closest('tr')).hide().show('slow');
  $j(`
    <tr id="unifica_aluno_titulo">
      <td colspan="2">
        <h2 class="unifica_pessoa_h2">
          Selecione o aluno que tenha os dados relavantes mais completos.
        </h2>
      </td>
    </tr>
  `).insertAfter($j('.tableDetalheLinhaSeparador').first().closest('tr')).hide().show('slow');

  let html = `
    <td colspan="2">
    <table id="tabela_alunos_unificados">
      <tr class="tr_title">
        <td>Principal</td>
        <td>Código do aluno</td>
        <td>Inep</td>
        <td>Nome</td>
        <td>Nascimento</td>
        <td>CPF</td>
        <td>RG</td>
        <td>Pessoa Mãe</td>
        <td>Dados</td>
        <td>Ação</td>
      </tr>
  `;

  response.alunos.each(function(aluno, id) {
    html += '<tr id="' + aluno.codigo + '" class="linha_listagem">';
    html += '<td><input onclick="validaCheckAlunoPrincipal(this)" type="checkbox" class="check_principal" id="check_principal_' + aluno.codigo + '"</td>';
    html += '<td>'+ aluno.codigo +'</td>';
    html += '<td>'+ aluno.inep +'</td>';
    html += '<td><a target="_new" href="/intranet/educar_aluno_det.php?cod_aluno=' + aluno.codigo + '">'+ aluno.nome +'</a></td>';
    html += '<td>'+ aluno.data_nascimento +'</td>';
    html += '<td>'+ aluno.cpf +'</td>';
    html += '<td>'+ aluno.rg +'</td>';
    html += '<td>'+ aluno.mae_aluno +'</td>';
    html += '<td><a onclick="visualizarDadosAlunos(' + aluno.codido + ')">Visualizar</a></td>';
    html += '<td><a class="link_remove" onclick="removeAluno(' + aluno.codigo + ')">EXCLUIR</a></td>';
    html += '</tr>';
  });

  html += '</table></td>';

  $j('#lista_dados_alunos_unificados').html(html);
}

function validaCheckAlunoPrincipal(element) {

}

function visualizarDadosAlunos(codAluno) {

}

function removeAluno(codAluno) {

}

function removeExclusaoDeAlunos() {
  $j('.tr_tabela_alunos td a').each(function(id, input) {
    input.remove();
  });
}

function disabilitaSearchInputs() {
  $j('input[id^="aluno_duplicado["').prop('disabled', true);
}

function defaultModal(message) {
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

function ajustaTabelaDeAlunosUnificados() {
  $j('a[id^="link_remove["').empty().text('EXCLUIR');
  $j('input[id^="aluno_duplicado["').attr("placeholder", "Informe nome ou código do aluno");
}

  var handleSelect = function(event, ui){
  $j(event.target).val(ui.item.label);
  return false;
};

  var search = function(request, response) {
  var searchPath = '/module/Api/Aluno?oper=get&resource=aluno-search';
  var params     = { query : request.term };

  $j.get(searchPath, params, function(dataResponse) {
  simpleSearch.handleSearch(dataResponse, response);
});
};

  function setAutoComplete() {
  $j.each($j('input[id^="aluno_duplicado"]'), function(index, field) {

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
