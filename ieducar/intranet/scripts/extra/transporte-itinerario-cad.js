

  // autocomplete disciplina fields

  var handleSelect = function(event, ui){
  $j(event.target).val(ui.item.label);
  return false;
};

  var search = function(request, response) {
  var searchPath = '/module/Api/Ponto?oper=get&resource=ponto-search';
  var params     = { query : request.term };

  $j.get(searchPath, params, function(dataResponse) {
  simpleSearch.handleSearch(dataResponse, response);
});
};

  var searchV = function(request, response) {
  var searchPath = '/module/Api/Veiculo?oper=get&resource=veiculo-search';
  var params     = { query : request.term };

  $j.get(searchPath, params, function(dataResponse) {
  simpleSearch.handleSearch(dataResponse, response);
});
};

  function setAutoComplete() {
  $j.each($j('input[id^="ref_cod_ponto_transporte_escolar"]'), function(index, field) {

    $j(field).autocomplete({
      source    : search,
      select    : handleSelect,
      minLength : 1,
      autoFocus : true
    });

  });
  $j.each($j('input[id^="ref_cod_veiculo"]'), function(index, field) {

  $j(field).autocomplete({
  source    : searchV,
  select    : handleSelect,
  minLength : 1,
  autoFocus : true
});

});
}

  setAutoComplete();

  document.onclick = function(event) {
  var targetElement = event.target;
  if ( targetElement.value == " Cancelar " ) {

  var cod_rota = $j('#cod_rota').val();
  location.href="transporte_rota_det.php?cod_rota="+cod_rota;
} else if(targetElement.value == "Excluir todos"){
  var cod_rota = $j('#cod_rota').val();
  if(confirm('Este procedimento irá excluir todos os pontos do itinerário. Tem certeza que deseja continuar?')){
  location.href="transporte_itinerario_del.php?cod_rota="+cod_rota;
}
}
};

  var submitForm = function(event) {
  // Esse formUtils.submit() chama o Editar();
  // Mais à frente bolar uma validação aqui
  /*  var $frequenciaField = $j('#frequencia');
      var frequencia       = $frequenciaField.val();

      if (frequencia.indexOf(',') > -1)
          frequencia = frequencia.replace('.', '').replace(',', '.');

    if (validatesIfNumericValueIsInRange(frequencia, $frequenciaField, 0, 100))*/
  formUtils.submit();
}


  // bind events

  var $addPontosButton = $j('#btn_add_tab_add_1');

  $addPontosButton.click(function(){
  setAutoComplete();
});


  // submit button

  var $submitButton = $j('#btn_enviar');

  $submitButton.removeAttr('onclick');
  $submitButton.click(submitForm);


