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
  modalAvisoComplementaDadosAluno();
  removeExclusaoDeAlunos();
  disabilitaSearchInputs();
  montaTabelaDadosAluno(response);
  adicionaBotoes();
  adicionaCheckboxConfirmacao();
  uniqueCheck();
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
        <td>Dados escolares</td>
        <td>Ação</td>
      </tr>
  `;

  response.alunos.each(function(aluno, id) {
    html += '<tr id="' + aluno.codigo + '" class="linha_listagem">';
    html += '<td><input onclick="validaCheckAlunoPrincipal(this)" type="checkbox" class="check_principal" id="check_principal_' + aluno.codigo + '"</td>';
    html += '<td><a target="_new" href="/module/Cadastro/aluno?id=' + aluno.codigo + '">'+ aluno.codigo +'</a></td>';
    html += '<td><a target="_new" href="/module/Cadastro/aluno?id=' + aluno.codigo + '">'+ aluno.inep +'</a></td>';
    html += '<td><a target="_new" href="/module/Cadastro/aluno?id=' + aluno.codigo + '">'+ aluno.nome +'</a></td>';
    html += '<td><a target="_new" href="/module/Cadastro/aluno?id=' + aluno.codigo + '">'+ aluno.data_nascimento +'</a></td>';
    html += '<td><a target="_new" href="/module/Cadastro/aluno?id=' + aluno.codigo + '">'+ aluno.cpf +'</a></td>';
    html += '<td><a target="_new" href="/module/Cadastro/aluno?id=' + aluno.codigo + '">'+ aluno.rg +'</a></td>';
    html += '<td><a target="_new" href="/module/Cadastro/aluno?id=' + aluno.codigo + '">'+ aluno.mae_aluno +'</a></td>';
    html += '<td><a onclick="visualizarDadosAlunos(' + aluno.codigo + ', \'' + aluno.nome + '\')">Visualizar</a></td>';
    html += '<td><a class="link_remove" onclick="removeAluno(' + aluno.codigo + ')">EXCLUIR</a></td>';
    html += '</tr>';
  });

  html += '</table></td>';

  $j('#lista_dados_alunos_unificados').html(html);
}

function adicionaCheckboxConfirmacao() {
  $j('<tr id="tr_confirma_dados_unificacao"></tr>').insertBefore($j('.linhaBotoes'));

  let htmlCheckbox = '<td colspan="2">'
  htmlCheckbox += '<input onchange="confirmaAnalise()" id="check_confirma_dados_unificacao" type="checkbox" />';
  htmlCheckbox += '<label for="check_confirma_dados_unificacao">Confirmo a análise de que são a mesma pessoa, levando <br> em conta a possibilidade de gêmeos cadastrados.</label>';
  htmlCheckbox += '</td>';

  $j('#tr_confirma_dados_unificacao').html(htmlCheckbox);
}

function confirmaAnalise() {
  let checked = $j('#check_confirma_dados_unificacao').is(':checked');

  if (existeAlunoPrincipal() && checked) {
    habilitaBotaoUnificar();
    return;
  }

  if (checked) {
    desabilitaBotaoUnificar();
    defaultModal('Você precisa definir um aluno como principal.');
    $j('#check_confirma_dados_unificacao').prop('checked', false);
    return;
  }

  desabilitaBotaoUnificar();
  $j('#check_confirma_dados_unificacao').prop('checked', false);
}

function existeAlunoPrincipal() {
  let existeAlunoPrincipal = false;
  const checkbox = document.querySelectorAll('input.check_principal')
  checkbox.forEach(element => {
    if (element.checked == true) {
      existeAlunoPrincipal = true;
    }
  });

  return existeAlunoPrincipal;
}

function habilitaBotaoUnificar() {
  $j('#unifica_pessoa').prop('disabled', false);
  $j('#unifica_pessoa').addClass('btn-green');
  $j('#unifica_pessoa').removeClass('btn-disabled');
}

function desabilitaBotaoUnificar() {
  $j('#unifica_pessoa').prop('disabled', true);
  $j('#unifica_pessoa').removeClass('btn-green');
  $j('#unifica_pessoa').addClass('btn-disabled');
}

function validaCheckAlunoPrincipal(element) {

}

function uniqueCheck() {
  const checkbox = document.querySelectorAll('input.check_principal')
  checkbox.forEach(element => {
    element.addEventListener('click', handleClick.bind(event,checkbox));
  });
}

function handleClick(checkbox, event) {
  checkbox.forEach(element => {
    confirmaAnalise();
    if (event.currentTarget.id !== element.id) {
      element.checked = false;
    }
  });
}

function visualizarDadosAlunos(codAluno, nomeAluno) {
  var url = getResourceUrlBuilder.buildUrl(
    '/module/Api/Aluno',
    'dadosMatriculasHistoricosAlunos',
    {
      aluno_id : codAluno
    }
  );

  var options = {
    url      : url,
    dataType : 'json',
    success  : function(response) {
      modalMatriculasEHistoricos(response, nomeAluno);
    }
  };

  getResources(options);
}

function removeAluno(codAluno) {
  if ($j('#tabela_alunos_unificados tr').length === 3) {
    defaultModal('É necessário ao menos 2 alunos para a unificação.');
    return;
  }
  removeTr(codAluno);
}

function removeTr(codAluno) {
  let trClose = $j('#' + codAluno);
  trClose.fadeOut(400, function() {
    trClose.remove();
  });
}

function adicionaBotoes() {
  let htmlBotao = '<input type="button" class="botaolistagem" onclick="voltar();" value="Voltar" autocomplete="off">';
  htmlBotao += '<input id="unifica_pessoa" type="button" class="botaolistagem" onclick="showConfirmationMessage();" value="Unificar pessoas da lista" autocomplete="off">';
  $j('.linhaBotoes td').html(htmlBotao);
}

function voltar() {
  document.location.reload(true);
}

function removeExclusaoDeAlunos() {
  $j('.tr_tabela_alunos td a').each(function(id, input) {
    input.remove();
  });
}

function disabilitaSearchInputs() {
  $j('input[id^="aluno_duplicado["').prop('disabled', true);
}

function modalMatriculasEHistoricos(response, nomeAluno) {
  let content = contentMatriculasEHistoricos(response);
  makeDialog({
    content: content,
    title: 'Dados escolares do aluno ' + nomeAluno,
    width: '90%',
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

function contentMatriculasEHistoricos(response) {
  let html = '';
  html += htmlTabelaMatriculas(response.matriculas);
  html += htmlTabelaHistoricos(response.historicos);

  return html;
}

function htmlTabelaMatriculas(matriculas) {
  let html = '<h4>Matrículas</h4>';
  html += '<table class="tabela-modal-dados-aluno">';
  html += '<tr class="tabela-modal-dados-aluno-titulo">';
  html += '<td>Ano</td>';
  html += '<td>Escola</td>';
  html += '<td>Curso</td>';
  html += '<td>Serie</td>';
  html += '<td>Turma</td>';
  html += '</tr>';

  matriculas.each(function(matricula, id) {
    html += '<tr class="linha_listagem">';
    html += '<td>' + matricula.ano + '</td>';
    html += '<td>' + matricula.escola + '</td>';
    html += '<td>' + matricula.curso + '</td>';
    html += '<td>' + matricula.serie + '</td>';
    html += '<td>' + matricula.turma + '</td>';
    html += '</tr>';
  });

  html += '</table>';

  return html;
}

function htmlTabelaHistoricos(historicos) {
  let html = '<h4>Históricos escolares</h4>';
  html += '<table class="tabela-modal-dados-aluno">';
  html += '<tr class="tabela-modal-dados-aluno-titulo">';
  html += '<td>Ano</td>';
  html += '<td>Escola</td>';
  html += '<td>Curso</td>';
  html += '<td>Serie</td>';
  html += '<td>Situação</td>';
  html += '</tr>';

  historicos.each(function(historico, id) {
    html += '<tr class="linha_listagem">';
    html += '<td>' + historico.ano + '</td>';
    html += '<td>' + historico.escola + '</td>';
    html += '<td>' + historico.curso + '</td>';
    html += '<td>' + historico.serie + '</td>';
    html += '<td>' + historico.situacao + '</td>';
    html += '</tr>';
  });

  html += '</table>';

  return html;
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

function modalAvisoComplementaDadosAluno() {
  makeDialog({
    content: `Para complementar os dados do aluno que selecionou como principal, 
    é necessário fazê-lo manualmente editando os dados do mesmo antes da Unificação de Alunos. 
    <b>Caso não faça essa complementação, os dados dos alunos não selecionadas como principal serão perdidos, 
    exceto matrículas e dados de histórico.<b>`,
    title: 'Atenção!',
    maxWidth: 860,
    width: 860,
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
