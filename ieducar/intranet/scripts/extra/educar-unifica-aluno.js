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
