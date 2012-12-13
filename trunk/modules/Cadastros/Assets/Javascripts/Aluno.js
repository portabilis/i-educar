// ajax

resourceOptions.handleGet = function(dataResponse) {
    handleMessages(dataResponse.msgs);

    getPersonDetails(dataResponse.pessoa_id);

    $j('#id').val(dataResponse.id);
    $j('#tipo_responsavel').val(dataResponse.tipo_responsavel).change();
    $j('#religiao_id').val(dataResponse.religiao_id);
    $j('#beneficio_id').val(dataResponse.beneficio_id);
    $j('#tipo_transporte').val(dataResponse.tipo_transporte);
    $j('#alfabetizado').attr('checked', dataResponse.alfabetizado);
};

var clearPersonDetails = function() {
  $j('#pessoa_id').val('');
  $j('#pessoa_nome').val('');
  $j('#pai').val('');
  $j('#mae').val('');
  $j('#responsavel_nome').val('');
  $j('#responsavel_id').val('');
}

var handleGetPersonDetails = function(dataResponse) {
  handleMessages(dataResponse.msgs);

  $j('#pessoa_id').val(dataResponse.id);
  $j('#pessoa_nome').val(dataResponse.id + ' - ' + dataResponse.nome);

  var nomePai = dataResponse.nome_pai;
  var nomeMae = dataResponse.nome_mae;
  var nomeResponsavel = dataResponse.nome_responsavel;

  if (dataResponse.pai_id)
    nomePai = dataResponse.pai_id + ' - ' + nomePai;

  if (dataResponse.mae_id)
    nomeMae = dataResponse.mae_id + ' - ' + nomeMae;

  if (dataResponse.responsavel_id)
    nomeResponsavel = dataResponse.responsavel_id + ' - ' + nomeResponsavel;

  //$j('#rg').val(dataResponse.rg);
  //$j('#cpf').val(dataResponse.cpf);
  $j('#pai').val(nomePai);
  $j('#mae').val(nomeMae);
  $j('#responsavel_nome').val(nomeResponsavel);
  $j('#responsavel_id').val(dataResponse.responsavel_id);

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
  if ($j('#pessoa_nome').val() && $j('#pessoa_id').val())
    getPersonDetails($j('#pessoa_id').val());
  else
    clearPersonDetails();
}


// simple search options

var simpleSearchPessoaOptions = {
  autocompleteOptions : { change : updatePersonDetails, close : updatePersonDetails }
};

var simpleSearchResponsavelOptions = {};


// when page is ready

(function($) {
  $(document).ready(function() {

    var checkTipoResponsavel = function(){
      if ($j('#tipo_responsavel').val() == 'outra_pessoa')
        $j('#responsavel_nome').show();
      else
        $j('#responsavel_nome').hide();
    }

    checkTipoResponsavel();
    $j('#tipo_responsavel').change(checkTipoResponsavel);
  }); // ready
})(jQuery);