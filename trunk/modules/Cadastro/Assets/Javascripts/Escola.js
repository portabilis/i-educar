var $submitButton = $j('#btn_enviar');

var submitForm = function(){
  putEscola();
}

var handleGetEscola = function(dataResponse) {
  handleMessages(dataResponse.msgs);

  $j('#escola_inep_id').val(dataResponse.escola_inep_id);
}

var handlePutEscola = function(dataResponse) {
  handleMessages(dataResponse.msgs);

  // submete formulário somente após put (para não interromper requisição ajax)
  acao();
}

var getEscola = function() {
  var data = {
    id : $j('#cod_escola').val()
  };

  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/escola', 'escola'),
    dataType : 'json',
    data     : data,
    success  : handleGetEscola
  };

  getResource(options);
}

var putEscola = function() {
  var data = {
    id             : $j('#cod_escola').val(),
    escola_inep_id : $j('#escola_inep_id').val()
  };

  var options = {
    url      : putResourceUrlBuilder.buildUrl('/module/Api/escola', 'escola'),
    dataType : 'json',
    data     : data,
    success  : handlePutEscola
  };

  putResource(options);
}

getEscola();

// unbind events
$submitButton.removeAttr('onclick');
$j(document.formcadastro).removeAttr('onsubmit');

// bind events
$submitButton.click(submitForm);