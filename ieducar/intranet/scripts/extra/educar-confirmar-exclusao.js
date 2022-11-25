
function adicionaCheckboxConfirmacao() {
  $j('<tr id="tr_confirma_dados_unificacao"></tr>').insertBefore($j('.linhaBotoes'));

  let htmlCheckbox = '<td colspan="2">'
  htmlCheckbox += '<input onchange="confirmaAnalise()" id="check_confirma_dados_unificacao" type="checkbox" />';
  htmlCheckbox += '<label for="check_confirma_dados_unificacao">Esta rotina excluirá todas as informações do diário do aluno posteriormente a data de movimentação, assinale se concorda.</label>';
  htmlCheckbox += '</td>';

  $j('#tr_confirma_dados_unificacao').html(htmlCheckbox);
}

function confirmaAnalise() {
  let checked = $j('#check_confirma_dados_unificacao').is(':checked');

  if (checked) {
    habilitaBotaoUnificar();
    return;
  }

  if (checked) {
    desabilitaBotaoUnificar();
    defaultModal('Você precisa definir um(a) aluno(a) como principal.');
    desabilitaConfirmarDadosUnificar();
    return;
  }

  desabilitaBotaoUnificar();
  desabilitaConfirmarDadosUnificar();
}

function desabilitaConfirmarDadosUnificar() {
  $j('#check_confirma_dados_unificacao').prop('checked', false);
}


function habilitaBotaoUnificar() {
  $j('.botaolistagem').prop('disabled', false);
  $j('.botaolistagem').addClass('btn-green');
  $j('.botaolistagem').removeClass('btn-disabled');
}

function desabilitaBotaoUnificar() {
  $j('.botaolistagem').prop('disabled', true);
  $j('.botaolistagem').removeClass('btn-green');
  $j('.botaolistagem').addClass('btn-disabled');
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
    makeDialog({
      content: 'É necessário ao menos 2 alunos para a unificação, ao confirmar o processo vai ser reiniciado. Deseja prosseguir?',
      title: 'Atenção!',
      maxWidth: 860,
      width: 860,
      close: function () {
        $j('#dialog-container').dialog('destroy');
      },
      buttons: [{
        text: 'Confirmar',
        click: function () {
          recarregar();
          $j('#dialog-container').dialog('destroy');
        }
      }, {
        text: 'Cancelar',
        click: function () {
          $j('#dialog-container').dialog('destroy');
        }
      }]
    });
    return;
  }

  desabilitaConfirmarDadosUnificar();
  desabilitaBotaoUnificar();
  removeTr(codAluno);
}

function removeTr(codAluno) {
  let trClose = $j('#' + codAluno);
  trClose.fadeOut(400, function() {
    trClose.remove();
  });
}

function adicionaBotoesInicias() {
  let htmlBotao = '<input type="button" class="botaolistagem" onclick="voltarParaLista();" value="Voltar" autocomplete="off">';
      htmlBotao += '<input type="button" id="btn_enviar" name="botaolistagem" onClick="carregaDadosAlunos();" value="Carregar dados" autoComplete="off">';
  $j('.linhaBotoes td').html(htmlBotao);
}

function adicionaBotoes() {
  let htmlBotao = '<input type="button" class="botaolistagem" onclick="voltarParaLista();" value="Voltar" autocomplete="off">';
      htmlBotao += '<input type="button" class="botaolistagem" onclick="recarregar();" value="Cancelar" autocomplete="off">';
      htmlBotao += '<input id="unifica_pessoa" type="button" class="botaolistagem" onclick="showConfirmationMessage();" value="Unificar alunos da lista" autocomplete="off">';
  $j('.linhaBotoes td').html(htmlBotao);
}

function voltarParaLista() {
  document.location.href = '/unificacao-aluno'
}

function recarregar() {
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
    title: 'Dados escolares do(a) aluno(a) ' + nomeAluno,
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
  html += '<td>Série</td>';
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
  $j('input[id^="aluno_duplicado["').attr("placeholder", "Informe nome ou código do(a) aluno(a)");
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





