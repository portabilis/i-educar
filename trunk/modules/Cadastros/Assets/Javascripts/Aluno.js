resourceOptions.name = 'aluno';
resourceOptions.new  = true;

resourceOptions.handlePost = function(dataResponse) {
  console.log('#TODO handle post');
  console.log(dataResponse);
}

// TODO move *person* logic to /modudes/Cadastros/Assets/Javascripts/Person.js

var handleGetPersonDetails = function(dataResponse) {
  handleMessages(dataResponse.msgs);

  console.log(dataResponse);

  var nomePai = dataResponse.nome_pai;
  var nomeMae = dataResponse.nome_mae;
  var nomeResponsavel = dataResponse.nome_responsavel;

  if (dataResponse.pai_id)
    nomePai = dataResponse.pai_id + ' - ' + nomePai;

  if (dataResponse.mae_id)
    nomeMae = dataResponse.mae_id + ' - ' + nomeMae;

  if (dataResponse.responsavel_id)
    nomeResponsavel = dataResponse.responsavel_id + ' - ' + nomeResponsavel;

  $j('#aluno_rg').val(dataResponse.rg);
  $j('#aluno_cpf').val(dataResponse.cpf);
  $j('#aluno_pai').val(nomePai);
  $j('#aluno_mae').val(nomeMae);
  $j('#aluno_responsavel').val(nomeResponsavel);

  //$j('#aluno_foto').val(dataResponse.url_foto);
}

var getPersonDetails = function(personId) {
  var additionalVars = {
    id : personId,
  };

  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/pessoa', 'pessoa', additionalVars),
    dataType : 'json',
    data     : {},
    success  : handleGetPersonDetails,
  };

  getResource(options);
}

var updatePersonDetails = function() {
  getPersonDetails($j('#pessoa_id').val());
}

var simpleSearchPessoaOptions = {
  autocompleteOptions : { close : updatePersonDetails }
};