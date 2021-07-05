adicionaMaisUmaLinhaNaTabela();
ajustaTabelaDeAlunosUnificados();

$j('#btn_add_tab_add_1').click(function(){
  ajustaTabelaDeAlunosUnificados();
});

function adicionaMaisUmaLinhaNaTabela() {
  tab_add_1.addRow();
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

  $j('#btn_enviar').val('Unificar');



