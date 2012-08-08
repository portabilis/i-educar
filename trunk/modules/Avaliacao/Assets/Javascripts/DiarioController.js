// TODO verificar o motivo de preventDefault, eh para nao submeter o formulario ao pressionar enter?
$j('#form_resultado').submit(function(event) {event.preventDefault()});


// variaveis usadas pelo modulo FrontendApi.js
var PAGE_URL_BASE = 'diario';
var API_URL_BASE  = 'diarioApi';

var RESOURCE_NAME  = 'matricula';
var RESOURCES_NAME = 'matriculas';

var POST_LABEL   = '';
var DELETE_LABEL = '';

var SEARCH_ORIENTATION = '<strong>Obs:</strong> Caso n&atilde;o seja listado as op&ccedil;&otilde;es de filtro que voc&ecirc; esperava, solicite ao(&agrave;) secret&aacute;rio(a) da escola para verificar a aloca&ccedil;&atilde;o ou permiss&atilde;o do seu usu&aacute;rio.';

// funcoes usados pelo modulo FrontendApi.js
var onClickSelectAllEvent = false;
var onClickActionEvent    = false;
var onClickDeleteEvent    = false;

// TODO remover funcao, quando passar a usar novo padrao campos seleção
function fixupFieldsWidth() {
  var maxWidth = 0;
  var $fields = $j('#formcadastro select');

  //get maxWidh
  $j.each($fields, function(index, value) {
    $value = $j(value);
    if ($value.width() > maxWidth)
      maxWidth = $value.width(); 
  });

  //set maxWidth
  $j.each($fields, function(index, value) {
    $j(value).width(maxWidth);
  });
};

fixupFieldsWidth();

//url builders

var deleteResourceUrlBuilder = {
  buildUrl : function(urlBase, resourceName, additionalVars) {

    var vars = {
      resource : resourceName,
      oper : 'delete',
      instituicao_id : $j('#ref_cod_instituicao').val(),
      escola_id : $j('#ref_cod_escola').val(),
      curso_id : $j('#ref_cod_curso').val(),
      serie_id : $j('#ref_ref_cod_serie').val(),
      turma_id : $j('#ref_cod_turma').val(),
      ano_escolar : $j('#ano').val(),
      componente_curricular_id : $j('#ref_cod_componente_curricular').val(),
      etapa : $j('#etapa').val()
    };

    return resourceUrlBuilder.buildUrl(urlBase, $j.extend(vars, additionalVars));
  }
};


var postResourceUrlBuilder = {
  buildUrl : function(urlBase, resourceName, additionalVars) {

    var vars = {
      resource : resourceName,
      oper : 'post',
      instituicao_id : $j('#ref_cod_instituicao').val(),
      escola_id : $j('#ref_cod_escola').val(),
      curso_id : $j('#ref_cod_curso').val(),
      serie_id : $j('#ref_ref_cod_serie').val(),
      turma_id : $j('#ref_cod_turma').val(),
      ano_escolar : $j('#ano').val(),
      componente_curricular_id : $j('#ref_cod_componente_curricular').val(),
      etapa : $j('#etapa').val(),
      matricula_id : $j('#etapa').val()
    };

    return resourceUrlBuilder.buildUrl(urlBase, $j.extend(vars, additionalVars));

  }
};


var getResourceUrlBuilder = {
  buildUrl : function(urlBase, resourceName, additionalVars) {

    var vars = {
      resource : resourceName,
      oper : 'get',
      instituicao_id : $j('#ref_cod_instituicao').val(),
      escola_id : $j('#ref_cod_escola').val(),
      curso_id : $j('#ref_cod_curso').val(),
      serie_id : $j('#ref_ref_cod_serie').val(),
      turma_id : $j('#ref_cod_turma').val(),
      ano_escolar : $j('#ano').val(),
      componente_curricular_id : $j('#ref_cod_componente_curricular').val(),
      etapa : $j('#etapa').val()
    };

    return resourceUrlBuilder.buildUrl(urlBase, $j.extend(vars, additionalVars));

  }
};


function changeResource($resourceElement, postFunction, deleteFunction) {
  if ($j.trim($resourceElement.val())  == '')
    deleteFunction($resourceElement);
  else
    postFunction($resourceElement);
};


function setDefaultFaltaIfEmpty(matricula_id) {
  var $element = $j('#falta-matricula-' + matricula_id);
  if ($j.trim($element.val()) == '') {
    $element.val(0);
    $element.change();
  }
}    


var changeNota = function(event) {
  var $element = $j(this);
  setDefaultFaltaIfEmpty($element.data('matricula_id'));
  changeResource($element, postNota, deleteNota);
};


var changeNotaExame = function(event) {
  var $element = $j(this);
  setDefaultFaltaIfEmpty($element.data('matricula_id'));
  changeResource($element, postNotaExame, deleteNotaExame);
};


var changeFalta = function(event) {
  changeResource($j(this), postFalta, deleteFalta);
};


var changeParecer = function(event) {
  var $element = $j(this);
  setDefaultFaltaIfEmpty($element.data('matricula_id'));
  changeResource($element, postParecer, deleteParecer);
};


function afterChangeResource($resourceElement) {
  $resourceElement.removeAttr('disabled').siblings('img').remove();
  var resourceElementTabIndex = $resourceElement.attr('tabindex');
  var focusedElementTabIndex = $j('*:focus').first().attr('tabindex');
  var lastElementTabIndex = $resourceElement.closest('form').find(':last:[tabindex]').attr('tabindex');

  for(var nextTabIndex = resourceElementTabIndex + 1; nextTabIndex < lastElementTabIndex + 1; nextTabIndex++) {
    var $nextElement = $j($resourceElement.closest('form').find(':[tabindex="'+nextTabIndex+'"]')).first();

    if($nextElement.is(':visible'))
    {
      if(focusedElementTabIndex == resourceElementTabIndex)
        $nextElement.focus();

      break;
    }
  }
}

function postNota($notaFieldElement) {

  $notaFieldElement.val($notaFieldElement.val().replace(',', '.'));

  if (validatesIfValueIsNumeric($notaFieldElement.val(), $notaFieldElement.attr('id')) &&
      validatesIfNumericValueIsInRange($notaFieldElement.val(), $notaFieldElement.attr('id'), 0, 10) && 
      validatesIfValueIsInSet($notaFieldElement.val(), $notaFieldElement.attr('id'), $tableSearchDetails.data('details').opcoes_notas)) {
  
    beforeChangeResource($notaFieldElement);

    var options = {
      url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'nota', {matricula_id : $notaFieldElement.data('matricula_id')}),
      dataType : 'json',
      data : {att_value : $notaFieldElement.val()},
      success : function(dataResponse) {
        afterChangeResource($notaFieldElement);
        handlePost(dataResponse);
      }
    };

    $notaFieldElement.data('old_value', $notaFieldElement.val());
    postResource(options, handleErrorOnPostResource);
  }
}


function postNotaExame($notaExameFieldElement) {

  $notaExameFieldElement.val($notaExameFieldElement.val().replace(',', '.'));

  if (validatesIfValueIsNumeric($notaExameFieldElement.val(), $notaExameFieldElement.attr('id')) &&
      validatesIfNumericValueIsInRange($notaExameFieldElement.val(), $notaExameFieldElement.attr('id'), 0, 10) && 
      validatesIfValueIsInSet($notaExameFieldElement.val(), $notaExameFieldElement.attr('id'), $tableSearchDetails.data('details').opcoes_notas)) {

    beforeChangeResource($notaExameFieldElement);

    var options = {
      url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'nota_exame', {matricula_id : $notaExameFieldElement.data('matricula_id'), etapa : 'Rc'}),
      dataType : 'json',
      data : {att_value : $notaExameFieldElement.val()},
      success : function(dataResponse) {
        afterChangeResource($notaExameFieldElement);
        handlePost(dataResponse);
      }
    };

    $notaExameFieldElement.data('old_value', $notaExameFieldElement.val());
    postResource(options, handleErrorOnPostResource);
  }
}


function postFalta($faltaFieldElement) {

  $faltaFieldElement.val($faltaFieldElement.val().replace(',', '.'));
  
  //falta é persistida como inteiro
  if ($j.isNumeric($faltaFieldElement.val()))
    $faltaFieldElement.val(parseInt($faltaFieldElement.val()).toString());

  if (validatesIfValueIsNumeric($faltaFieldElement.val(), $faltaFieldElement.attr('id')) &&
      validatesIfNumericValueIsInRange($faltaFieldElement.val(), $faltaFieldElement.attr('id'), 0, 100)) {

    beforeChangeResource($faltaFieldElement);

    var options = {
      url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'falta', {matricula_id : $faltaFieldElement.data('matricula_id')}),
      dataType : 'json',
      data : {att_value : $faltaFieldElement.val()},
      success : function(dataResponse) {
        afterChangeResource($faltaFieldElement);
        handlePost(dataResponse);
      }
    };

    $faltaFieldElement.data('old_value', $faltaFieldElement.val());
    postResource(options, handleErrorOnPostResource);
  }
}


function getEtapaParecer() {
  if ($tableSearchDetails.data('details').tipo_parecer_descritivo.split('_')[0] == 'anual')
    var etapaParecer = 'An';
  else
    var etapaParecer = $j('#etapa').val();

  return etapaParecer;
}


function postParecer($parecerFieldElement) {

  var options = {
    url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'parecer', {matricula_id : $parecerFieldElement.data('matricula_id'), etapa : getEtapaParecer()}),
    dataType : 'json',
    data : {att_value : $parecerFieldElement.val()},
    success : function(dataResponse) {
      afterChangeResource($parecerFieldElement);
      handlePost(dataResponse);
    }
  };

  $parecerFieldElement.data('old_value', $parecerFieldElement.val());
  postResource(options, handleErrorOnPostResource);
}


function confirmDelete(resourceName) {
  return confirm(safeUtf8Decode('Confirma exclusão ' + resourceName.replace('_',' ') + '?'));
}


function deleteResource(resourceName, $resourceElement, options, handleErrorOnDeleteResource) {
  if (confirmDelete(resourceName)) {
    beforeChangeResource($resourceElement);
    $resourceElement.data('old_value', '');
    $j.ajax(options).error(handleErrorOnDeleteResource);
  }
  else
  {
    afterChangeResource($resourceElement);
    $resourceElement.val($resourceElement.data('old_value'));
  }
}


function deleteNota($notaFieldElement) {
  var resourceName = 'nota';

  var additionalVars = { 
    instituicao_id : $j('#ref_cod_instituicao').val(),
    escola_id : $j('#ref_cod_escola').val(),
    curso_id : $j('#ref_cod_curso').val(),
    serie_id : $j('#ref_ref_cod_serie').val(),
    turma_id : $j('#ref_cod_turma').val(),
    ano_escolar : $j('#ano').val(),
    componente_curricular_id : $j('#ref_cod_componente_curricular').val(),
    etapa : $j('#etapa').val(),
    matricula_id : $notaFieldElement.data('matricula_id')
   };

  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, additionalVars),
    dataType : 'json',
    success : function(dataResponse) {
      afterChangeResource($notaFieldElement);
      handleDelete(dataResponse);
    }
  };

  deleteResource(resourceName, $notaFieldElement, options, handleErrorOnDeleteResource);
};


function deleteNotaExame($notaExameFieldElement) {
  var resourceName = 'nota_exame';

  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, {matricula_id : $notaExameFieldElement.data('matricula_id'), etapa : 'Rc'}),
    dataType : 'json',
    success : function(dataResponse) {
      afterChangeResource($notaExameFieldElement);
      handleDelete(dataResponse);
    }
  };

  deleteResource(resourceName, $notaExameFieldElement, options, handleErrorOnDeleteResource);
};


function deleteFalta($faltaFieldElement) {
    
  //excluir falta se nota, nota exame e parecer (não existirem ou) estiverem sem valor
  var matricula_id = $faltaFieldElement.data('matricula_id');

  var $notaField = $j('#nota-matricula-'+matricula_id);
  var $notaExameField = $j('#nota-exame-matricula-'+matricula_id);
  var $parecerField = $j('#parecer-matricula-'+matricula_id);

  if(($notaField.length < 1 || $notaField.val() == '') &&
     ($notaExameField.length < 1 || $notaExameField.val() == '') &&
     ($parecerField.length < 1 || $j.trim($parecerField.val()) == '')
    ) {      
    var resourceName = 'falta';

    var options = {
      url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, {matricula_id : $faltaFieldElement.data('matricula_id')}),
      dataType : 'json',
      success : function(dataResponse) {
        afterChangeResource($faltaFieldElement);
        handleDelete(dataResponse);
      }
    };

    deleteResource(resourceName, $faltaFieldElement, options, handleErrorOnDeleteResource);
  }
  else{

    $faltaFieldElement.val($faltaFieldElement.data('old_value'));

    handleMessages([{type : 'error', msg : safeUtf8Decode('Falta não pode ser removida após ter lançado notas ou parecer descritivo, tente definir como 0 (zero).')}], $faltaFieldElement.attr('id'));
  }
}


function deleteParecer($parecerFieldElement) {
  var resourceName = 'parecer';

  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, {matricula_id : $parecerFieldElement.data('matricula_id'), etapa : getEtapaParecer()}),
    dataType : 'json',
    success : function(dataResponse) {
      afterChangeResource($parecerFieldElement);
      handleDelete(dataResponse);
    }
  };

  deleteResource(resourceName, $parecerFieldElement, options, handleErrorOnDeleteResource);
}


//callback handlers
function handleDelete(dataResponse) {
  var targetId = dataResponse.att + '-matricula-' + dataResponse.matricula_id;
  handleMessages(dataResponse.msgs, targetId);
  updateResourceRow(dataResponse);
}


function handlePost(dataResponse) {
  var targetId = dataResponse.att + '-matricula-' + dataResponse.matricula_id;
  handleMessages(dataResponse.msgs, targetId);
  updateResourceRow(dataResponse);
}


function setTableSearchDetails($tableSearchDetails, dataDetails) {
  $j('<caption />').html('<strong>Lan&#231;amento de notas por turma</strong>').appendTo($tableSearchDetails);

  //set headers table
  var $linha = $j('<tr />');
  $j('<th />').html('Etapa').appendTo($linha);
  $j('<th />').html('Comp. Curricular').appendTo($linha);
  $j('<th />').html('Turma').appendTo($linha);
  $j('<th />').html('Série').appendTo($linha);
  $j('<th />').html('Ano').appendTo($linha);
  $j('<th />').html('Escola').appendTo($linha);
  $j('<th />').html('Regra avalia&#231;&#227;o').appendTo($linha);
  $j('<th />').html('Tipo nota').appendTo($linha);
  $j('<th />').html('Tipo presen&#231;a').appendTo($linha);
  $j('<th />').html('Tipo parecer').appendTo($linha);

  $linha.appendTo($tableSearchDetails);

  var $linha = $j('<tr />').addClass('even');
  $j('<td />').html(safeToUpperCase($j('#etapa').children("[selected='selected']").html())).appendTo($linha);
  $j('<td />').html(safeToUpperCase($j('#ref_cod_componente_curricular').children("[selected='selected']").html())).appendTo($linha);
  $j('<td />').html(safeToUpperCase($j('#ref_cod_turma').children("[selected='selected']").html())).appendTo($linha);
  $j('<td />').html(safeToUpperCase($j('#ref_ref_cod_serie').children("[selected='selected']").html())).appendTo($linha);
  $j('<td />').html($j('#ano').val()).appendTo($linha);

  //field escola pode ser diferente de select caso usuario comum 
  var $htmlEscolaField = $j('#ref_cod_escola').children("[selected='selected']").html() ||
                         $j('#tr_nm_escola span:last').html();

  $j('<td />').html(safeToUpperCase($htmlEscolaField)).appendTo($linha);
  $j('<td />').html(dataDetails.id + ' - ' +safeToUpperCase(dataDetails.nome)).appendTo($linha);
 
  //corrige acentuação
  var tipoNota = dataDetails.tipo_nota.replace('_', ' ');
  if (tipoNota == 'numerica')
    tipoNota = 'numérica';
  $j('<td />').html(safeToUpperCase(tipoNota)).appendTo($linha);

  $j('<td />').html(safeToUpperCase(dataDetails.tipo_presenca.replace('_', ' '))).appendTo($linha);
  $j('<td />').html(safeToUpperCase(dataDetails.tipo_parecer_descritivo.replace('_', ' '))).appendTo($linha);

  $linha.appendTo($tableSearchDetails);
  $tableSearchDetails.show();

  //dataDetails.opcoes_notas = safeSortArray(dataDetails.opcoes_notas);
  $tableSearchDetails.data('details', dataDetails);
}


function handleSearch($resultTable, dataResponse) { 
  var useNota    = $tableSearchDetails.data('details').tipo_nota != 'nenhum';
  var useParecer = $tableSearchDetails.data('details').tipo_parecer_descritivo != 'nenhum';

  //set headers
  var $linha = $j('<tr />');
  $j('<th />').html('Matrícula').appendTo($linha);
  $j('<th />').html('Aluno').appendTo($linha);
  $j('<th />').html('Situa&#231;&#227;o').appendTo($linha);

  if(useNota) {
    $j('<th />').html('Nota').appendTo($linha);

    if ($tableSearchDetails.data('details').quantidade_etapas == $j('#etapa').val())
      $j('<th />').html('Nota exame').appendTo($linha);
  }

  $j('<th />').html('Falta').appendTo($linha);

  if(useParecer)
    $j('<th />').html('Parecer').appendTo($linha);

  $linha.appendTo($resultTable);

  var nextTabIndex = 1;
  var setNextTabIndex = function($element) {
    $element.attr('tabindex', nextTabIndex);
    nextTabIndex += 1;
  };

  //set (result) rows
  $j.each(dataResponse.matriculas, function(index, value) {

    var $linha = $j('<tr />');
    
    $j('<td />').html(value.matricula_id).addClass('center').appendTo($linha);
    $j('<td />').html(value.aluno_id + ' - ' +safeToUpperCase(value.nome)).appendTo($linha);
    $j('<td />').addClass('situacao-matricula').attr('id', 'situacao-matricula-' + value.matricula_id).data('matricula_id', value.matricula_id).addClass('center').html(value.situacao).appendTo($linha);

    //nota
    var getFieldNota = function(notaAtual, klass, id) {

      var opcoesNotas = $tableSearchDetails.data('details').opcoes_notas;

      if($tableSearchDetails.data('details').tipo_nota == 'conceitual') {
        var $notaField = $j('<select />').addClass(klass).attr('id', id).data('matricula_id', value.matricula_id);

        //adiciona options
        var $option = $j('<option />').appendTo($notaField);
        for(key in opcoesNotas) {
          var $option = $j('<option />').val(key).html(opcoesNotas[key]);

          if (notaAtual == key)
            $option.attr('selected', 'selected');

          $option.appendTo($notaField);
        }
      }
      else {
        var $notaField = $j('<input />').addClass(klass).attr('id', id).val(notaAtual).attr('maxlength', '4').attr('size', '4').data('matricula_id', value.matricula_id);
      }

      $notaField.data('old_value', $notaField.val());
      setNextTabIndex($notaField);
      return $notaField;
    }

    if(useNota) {
      $j('<td />').html(getFieldNota(value.nota_atual, 'nota-matricula', 'nota-matricula-' + value.matricula_id)).addClass('center').appendTo($linha);

      if ($tableSearchDetails.data('details').quantidade_etapas == $j('#etapa').val()) {

        var $fieldNotaExame = getFieldNota(value.nota_exame, 'nota-exame-matricula', 'nota-exame-matricula-' + value.matricula_id);
        $j('<td />').html($fieldNotaExame).addClass('center').appendTo($linha);

        if (value.nota_exame == '' && value.situacao.toLowerCase() != 'em exame')
          $fieldNotaExame.hide();
      }
    }
    
    //falta
    var $faltaField = $j('<input />').addClass('falta-matricula').attr('id', 'falta-matricula-' + value.matricula_id).val(value.falta_atual).attr('maxlength', '4').attr('size', '4').data('matricula_id', value.matricula_id);
    $faltaField.data('old_value', $faltaField.val());
    setNextTabIndex($faltaField);
    $j('<td />').html($faltaField).addClass('center').appendTo($linha);

    //parecer
    if(useParecer) {
      var $parecerField = $j('<textarea />').attr('cols', '40').attr('rows', '5').addClass('parecer-matricula').attr('id', 'parecer-matricula-' + value.matricula_id).val(safeUtf8Decode(value.parecer_atual)).data('matricula_id', value.matricula_id);
      $parecerField.data('old_value', $parecerField.val());
      setNextTabIndex($parecerField);
      $j('<td />').addClass('center').html($parecerField).appendTo($linha);
    }

    $linha.fadeIn('slow').appendTo($resultTable);
  });//fim each matriculas

  $resultTable.find('tr:even').addClass('even');

  //set onchange events
  var $notaFields = $resultTable.find('.nota-matricula');
  var $notaExameFields = $resultTable.find('.nota-exame-matricula');
  var $faltaFields = $resultTable.find('.falta-matricula');
  var $parecerFields = $resultTable.find('.parecer-matricula');
  $notaFields.on('change', changeNota);
  $notaExameFields.on('change', changeNotaExame);
  $faltaFields.on('change', changeFalta);
  $parecerFields.on('change', changeParecer);

  $resultTable.addClass('styled').find('input:first').focus();
}


function updateResourceRow(dataResponse) {
  $j('#situacao-matricula-' + dataResponse.matricula_id).html(dataResponse.situacao);
  $fieldNotaExame = $j('#nota-exame-matricula-' + dataResponse.matricula_id);

  if (! $fieldNotaExame.is(':visible') && 
     ($fieldNotaExame.val() != '' || dataResponse.situacao.toLowerCase() == 'em exame')) {
    $fieldNotaExame.show();
    $fieldNotaExame.focus();
  }
  else if($fieldNotaExame.val() == '' && dataResponse.situacao.toLowerCase() != 'em exame')
    $fieldNotaExame.hide();
}
