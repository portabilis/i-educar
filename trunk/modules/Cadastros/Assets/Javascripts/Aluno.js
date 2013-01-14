// before page is ready

var $nomeField      = $j('#pessoa_nome');

var $resourceNotice = $j('<span>').html('')
                                  .addClass('notice resource-notice')
                                  .hide()
                                  .appendTo($nomeField.parent());


// ajax

resourceOptions.handleGet = function(dataResponse) {
  handleMessages(dataResponse.msgs);

  getPersonDetails(dataResponse.pessoa_id);

  $j('#id').val(dataResponse.id);
  $j('#inep_id').val(dataResponse.inep_id);
  $j('#tipo_responsavel').val(dataResponse.tipo_responsavel).change();
  $j('#religiao_id').val(dataResponse.religiao_id);
  $j('#beneficio_id').val(dataResponse.beneficio_id);
  $j('#tipo_transporte').val(dataResponse.tipo_transporte);
  $j('#alfabetizado').attr('checked', dataResponse.alfabetizado);
};

var handleGetPersonDetails = function(dataResponse) {
  handleMessages(dataResponse.msgs);

  $resourceNotice.hide();


  // verifica se já existe um aluno para a pessoa

  var alunoId = dataResponse.aluno_id;

  if (alunoId && alunoId != resource.id()) {
    $resourceNotice.html(stringUtils.toUtf8('Já existe o aluno '+ alunoId +' cadastrado para esta pessoa. ' ))
                   .slideDown('fast');

    $j('<a>').addClass('decorated')
             .attr('href', resource.url(alunoId))
             .attr('target', '__blank')
             .html('Visualizar cadastro.')
             .appendTo($resourceNotice);
  }

  else {
    $j('#pessoa_id').val(dataResponse.id);
    $nomeField.val(dataResponse.id + ' - ' + dataResponse.nome);

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

    // deficiencias

    $deficiencias = $j('#deficiencias');

    $j.each(dataResponse.deficiencias, function(id, nome) {
      $deficiencias.children("[value=" + id + "]").attr('selected', '');
    });

    $deficiencias.trigger('liszt:updated');

    // # TODO show aluno photo
    //$j('#aluno_foto').val(dataResponse.url_foto);
  }
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

var clearPersonDetails = function() {
  $j('#pessoa_id').val('');
  //$j('#pessoa_nome').val('');
  $j('#pai').val('');
  $j('#mae').val('');
  $j('#responsavel_nome').val('');
  $j('#responsavel_id').val('');
}

// simple search options

var simpleSearchPessoaOptions = {
  autocompleteOptions : { close : updatePersonDetails /*, change : updatePersonDetails*/ }
};


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