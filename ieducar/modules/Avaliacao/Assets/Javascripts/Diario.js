//$j('#form_resultado').submit(function(event) {event.preventDefault()});

// variaveis usadas pelo modulo Frontend/Process.js
var PAGE_URL_BASE = 'diario';
var API_URL_BASE  = 'diarioApi';

var RESOURCE_NAME  = 'matricula';
var RESOURCES_NAME = 'matriculas';

var REGRA_DIFERENCIADA_TEXT = '* Regra diferenciada para alunos com deficiência';

var POST_LABEL   = '';
var DELETE_LABEL = '';

var SEARCH_ORIENTATION = '';

var nomenclatura_exame = '';

var regra_dependencia = '';

// funcoes usados pelo modulo Frontend/Process.js
var onClickSelectAllEvent = false;
var onClickActionEvent    = false;
var onClickDeleteEvent    = false;

var sentidoTab;
var showBotaoReplicarNotas;

var locked = false;

$(function() {
    navegacaoTab(dataResponse.navegacao_tab);
});

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
      etapa : $j('#etapa').val(),
      matricula_id : $j('#ref_cod_matricula').val()
    };

    return resourceUrlBuilder.buildUrl(urlBase, $j.extend(vars, additionalVars));

  }
};


function changeResource($resourceElement, postFunction, deleteFunction) {
  //Substitui traveções por hífen antes de gravar
  $resourceElement.val($resourceElement.val().replace(/\u2013|\u2014/g, "-"));

  if ($j.trim($resourceElement.val())  == '')
    deleteFunction($resourceElement);
  else
    postFunction($resourceElement);
};


function setDefaultFaltaIfEmpty(matricula_id, componente_curricular_id) {
  var $element = $j('#falta-matricula-' + matricula_id + '-cc-' + componente_curricular_id);
  if ($j.trim($element.val()) == '') {
    $element.val(0);
    $element.change();
  }
}

var lockedAverage = function ($element, callback) {
  var matriculaId = $element.data('matricula_id');
  var ccId = $element.data('componente_curricular_id');
  var campoSituacao = $j('#situacao-matricula-' + matriculaId + '-cc-' + ccId);
  var bloqueado = campoSituacao.data('media_bloqueada');

  if (bloqueado) {
    var additionalVars = {
      matricula_id: matriculaId,
      componente_curricular_id: ccId
    };

    var dialogElmId = 'media-bloqueada';
    var dialogElm = $j('#' + dialogElmId);

    if (dialogElm.length < 1) {
      $j('body')
        .append('<div id="' + dialogElmId + '" style="display: none;">A média final deste aluno/etapa/componente está bloqueada pois foi alterada manualmente. Você gostaria de desbloqueá-la e permitir sua atualização automática?</div>')

      dialogElm = $j('#' + dialogElmId);
    }

    if (dialogElm.is(':ui-dialog')) {
      dialogElm.dialog('destroy');
    }

    dialogElm.dialog({
        width: 600,
        title: 'Atenção!',
        modal: true,
        open: function(event, ui) {
          $j('.ui-dialog-titlebar-close', ui.dialog | ui).hide();
        },
        buttons: [
          {
            text: 'Sim',
            click: function () {
              var options = {
                url: postResourceUrlBuilder.buildUrl(API_URL_BASE, 'media_desbloqueia', additionalVars),
                dataType: 'json',
                success: function (dataResponse) {
                  if (dataResponse.any_error_msg === false) {
                    campoSituacao.data('media_bloqueada', false);
                    callback();
                  } else {
                    alert(dataResponse.msgs[0].msg);
                  }
                },
                error: function () {
                  alert('Não foi possível desbloquear a média. Tente novamente!');
                }
              };

              $j.ajax(options);
              dialogElm.dialog('close');
            }
          }, {
            text: 'Não',
            click: function () {
              callback();
              dialogElm.dialog('close');
            }
          }
        ]
      })
  } else {
    callback();
  }
};

var changeNota = function(event) {
  if (locked) {
    handleLockedMessage();
    return;
  }

  var $element = $j(this);
  lockedAverage($element, function () {
    changeResource($element, postNota, deleteNota);
  });
};


var changeNotaExame = function(event) {
  if (locked) {
    handleLockedMessage();
    return;
  }

  var $element = $j(this);
  setDefaultFaltaIfEmpty($element.data('matricula_id'), $element.data('componente_curricular_id'));
  lockedAverage($element, function () {
    changeResource($element, postNotaExame, deleteNotaExame);
  });
};

var changeNotaRecuperacaoParalela = function(event){
  if (locked) {
    handleLockedMessage();
    return;
  }

  var $element = $j(this);
  setDefaultFaltaIfEmpty($element.data('matricula_id'), $element.data('componente_curricular_id'));
  lockedAverage($element, function () {
    changeResource($element, postNotaRecuperacaoParalela, deleteNotaRecuperacaoParalela);
  });
}

var changeNotaRecuperacaoEspecifica = function(event){
  if (locked) {
    handleLockedMessage();
    return;
  }

  var $element = $j(this);
  setDefaultFaltaIfEmpty($element.data('matricula_id'), $element.data('componente_curricular_id'));
  lockedAverage($element, function () {
    changeResource($element, postNotaRecuperacaoEspecifica, deleteNotaRecuperacaoEspecifica);
  });
}

var changeFalta = function(event) {
  if (locked) {
    handleLockedMessage();
    return;
  }

  $element = $j(this);
  var regra = $element.closest('tr').data('regra');
  changeResource($element, postFalta, deleteFalta);

  // se presenca geral, muda o valor em todas faltas da mesma matricula
  if (regra.tipo_presenca == 'geral') {
    var $fieldsFaltaMatricula = $element.closest('table').find('.falta-matricula-' + $element
                                                               .data('matricula_id') + '-cc')
                                                               .not($element);

    $fieldsFaltaMatricula.val($element.val());
    $fieldsFaltaMatricula.data('old_value', $element.val());
  }
};


var changeParecer = function(event) {
  if (locked) {
    handleLockedMessage();
    return;
  }

  var $element = $j(this);
  var regra = $j(this).closest('tr').data('regra');
  setDefaultFaltaIfEmpty($element.data('matricula_id'), $element.data('componente_curricular_id'));
  changeResource($element, postParecer, deleteParecer);

  // se parecer geral, muda o valor em todos pareceres da mesma matricula
  var parecerGeral = $j.inArray(regra.tipo_parecer_descritivo,
                                ['etapa_geral', 'anual_geral']) > -1;

  if (parecerGeral) {
    var $fieldsParecerMatricula = $element.closest('table').find('.parecer-matricula-' + $element
                                                                 .data('matricula_id') + '-cc')
                                                                 .not($element);

    $fieldsParecerMatricula.val($element.val());
    $fieldsParecerMatricula.data('old_value', $element.val());
  }
};

var changeNotaGeralEtapa = function(event) {
  if (locked) {
    handleLockedMessage();
    return;
  }

  var $element = $j(this);

  lockedAverage($element, function () {
    changeResource($element, postNotaGeral, deleteNotaGeral);

    var $fieldsNotaGeral = $j('.nota-geral-etapa');

    $fieldsNotaGeral.val($element.val());
    $fieldsNotaGeral.data('old_value', $element.val());
  });
};

var changeMedia = function(event) {
  if (locked) {
    handleLockedMessage();
    return;
  }

  var $element = $j(this);
  var matriculaId = $element.data('matricula_id');
  var ccId = $element.data('componente_curricular_id');
  var $situacaoField  = $j('#situacao-matricula-' + matriculaId + '-cc-' + ccId);

  changeResource($element, postMedia, deleteMedia);

  $element.data('old_value', $element.val());

  $situacaoField.data('media_bloqueada', true);
};

var changeSituacao = function(event) {
  var $element = $j(this);

  if($element.val() != 0){
    changeResource($element, postSituacao);
    $element.data('old_value', $element.val());
  }
};

function afterChangeResource($resourceElement) {
  $resourceElement.removeAttr('disabled').siblings('img').remove();

  var resourceElementTabIndex = parseInt($resourceElement.attr('tabindex'));
  var focusedElementTabIndex  = parseInt($j('.tabable:focus').first().attr('tabindex'));
  var lastElementTabIndex     = parseInt($resourceElement.closest('form').find('.tabable').last().attr('tabindex'));

  // percorre os proximos elementos enquanto não chegar no ultimo
  for(var nextTabIndex = resourceElementTabIndex + 1; nextTabIndex < lastElementTabIndex + 1; nextTabIndex++) {
    //var $nextElement = $j($resourceElement.closest('form').find('.tabable:[tabindex="'+nextTabIndex+'"]')).first();
    var $nextElement = $j($resourceElement.closest('form').find('.tabable[tabindex="'+nextTabIndex+'"]')).first();
    var $nextNextElement = $j($resourceElement.closest('form').find('.tabable[tabindex="'+(nextTabIndex+1)+'"]')).first();
  }
}

function postNota($notaFieldElement) {

  var regra = $notaFieldElement.closest('tr').data('regra');
  $notaFieldElement.val($notaFieldElement.val().replace(',', '.'));

  if (validatesIfValueIsNumeric($notaFieldElement.val(), $notaFieldElement.attr('id')) &&
      validatesIfNumericValueIsInRange($notaFieldElement.val(), $notaFieldElement.attr('id'), regra.nota_minima_geral, regra.nota_maxima_geral) &&
      validatesIfDecimalPlacesInRange($notaFieldElement.val(), $notaFieldElement.attr('id'), 0, regra.qtd_casas_decimais)) {

    beforeChangeResource($notaFieldElement);

    var additionalVars = {
      matricula_id             : $notaFieldElement.data('matricula_id'),
      componente_curricular_id : $notaFieldElement.data('componente_curricular_id'),
      nota_original            : $notaFieldElement.val()
    };

    var options = {
      url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'nota', additionalVars),
      dataType : 'json',
      data : {att_value : $notaFieldElement.val()},
      success : function(dataResponse) {
        afterChangeResource($notaFieldElement);
        handleChange(dataResponse);
        checkIfShowNotaRecuperacaoParalelaField($notaFieldElement.val(), dataResponse);
        setDefaultFaltaIfEmpty($notaFieldElement.data('matricula_id'), $notaFieldElement.data('componente_curricular_id'));
      }
    };

    $notaFieldElement.data('old_value', $notaFieldElement.val());
    postResource(options, handleErrorOnPostResource);
  } else {
    $j('#' + $notaFieldElement.attr('id')).addClass('error');
  }
}

function checkIfShowNotaRecuperacaoParalelaField(notaLancada, dataResponse){
  componente_curricular_id = dataResponse.componente_curricular_id;
  matricula_id = dataResponse.matricula_id;
  $jnotaRecuperacaoParalelaField = $j('#nota_recuperacao_paralela-matricula-' + matricula_id + '-cc-' + componente_curricular_id);

  if(!$jnotaRecuperacaoParalelaField.length){
    return false;
  }

  var regra = $jnotaRecuperacaoParalelaField.closest('tr').data('regra');
  var usaRecuperacaoParalelaPorEtapa = (regra.tipo_recuperacao_paralela == 'por_etapa');
  var mediaRecuperacaoParalela = regra.media_recuperacao_paralela;

  if(usaRecuperacaoParalelaPorEtapa){
    if((notaLancada < mediaRecuperacaoParalela) || (mediaRecuperacaoParalela == null)){
      $jnotaRecuperacaoParalelaField.show();
      // Somente seta o foco do campo quando o tab for horizontal,
      // pois se for vertical não faz sentido e atrapalha a navegação
      if(sentidoTab == "1"){
        $jnotaRecuperacaoParalelaField.focus();
      }
    }else{
      $jnotaRecuperacaoParalelaField.hide();
    }
  }
}


function postNotaExame($notaExameFieldElement) {

  var regra = $notaExameFieldElement.closest('tr').data('regra');
  $notaExameFieldElement.val($notaExameFieldElement.val().replace(',', '.'));

  if (validatesIfValueIsNumeric($notaExameFieldElement.val(), $notaExameFieldElement.attr('id')) &&
      validatesIfNumericValueIsInRange($notaExameFieldElement.val(), $notaExameFieldElement.attr('id'), 0, regra.nota_maxima_exame_final) &&
      validatesIfDecimalPlacesInRange($notaExameFieldElement.val(), $notaExameFieldElement.attr('id'), 0, regra.qtd_casas_decimais)) {

    beforeChangeResource($notaExameFieldElement);

    var additionalVars = {
      matricula_id             : $notaExameFieldElement.data('matricula_id'),
      componente_curricular_id : $notaExameFieldElement.data('componente_curricular_id'),
      etapa : 'Rc'
    };

    var options = {
      url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'nota_exame', additionalVars),
      dataType : 'json',
      data : {att_value : $notaExameFieldElement.val()},
      success : function(dataResponse) {
        afterChangeResource($notaExameFieldElement);
        handleChange(dataResponse);
      }
    };

    $notaExameFieldElement.data('old_value', $notaExameFieldElement.val());
    postResource(options, handleErrorOnPostResource);
  } else {
    $j('#' + $notaExameFieldElement.attr('id')).addClass('error');
  }
}


function postNotaRecuperacaoParalela($notaRecuperacaoParalelaElement) {

  var regra = $notaRecuperacaoParalelaElement.closest('tr').data('regra');

  $notaRecuperacaoParalelaElement.val($notaRecuperacaoParalelaElement.val().replace(',', '.'));

  if (validatesIfValueIsNumeric($notaRecuperacaoParalelaElement.val(), $notaRecuperacaoParalelaElement.attr('id')) &&
      validatesIfNumericValueIsInRange($notaRecuperacaoParalelaElement.val(), $notaRecuperacaoParalelaElement.attr('id'), regra.nota_minima_geral, regra.nota_maxima_geral) &&
      validatesIfDecimalPlacesInRange($notaRecuperacaoParalelaElement.val(), $notaRecuperacaoParalelaElement.attr('id'), 0, regra.qtd_casas_decimais)) {

    beforeChangeResource($notaRecuperacaoParalelaElement);

    var additionalVars = {
      matricula_id             : $notaRecuperacaoParalelaElement.data('matricula_id'),
      componente_curricular_id : $notaRecuperacaoParalelaElement.data('componente_curricular_id')
    };

    var options = {
      url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'nota_recuperacao_paralela', additionalVars),
      dataType : 'json',
      data : {att_value : $notaRecuperacaoParalelaElement.val()},
      success : function(dataResponse) {
        afterChangeResource($notaRecuperacaoParalelaElement);
        handleChange(dataResponse);
      }
    };

    $notaRecuperacaoParalelaElement.data('old_value', $notaRecuperacaoParalelaElement.val());
    postResource(options, handleErrorOnPostResource);
  } else {
    $j('#' + $notaRecuperacaoParalelaElement.attr('id')).addClass('error');
  }
}

function postNotaRecuperacaoEspecifica($notaRecuperacaoEspecificaElement) {
  var regra = $notaRecuperacaoEspecificaElement.closest('tr').data('regra');

  $notaRecuperacaoEspecificaElement.val($notaRecuperacaoEspecificaElement.val().replace(',', '.'));

  if (validatesIfValueIsNumeric($notaRecuperacaoEspecificaElement.val(), $notaRecuperacaoEspecificaElement.attr('id')) &&
      validatesIfNumericValueIsInRange($notaRecuperacaoEspecificaElement.val(), $notaRecuperacaoEspecificaElement.attr('id'), 0, regra.tipo_recuperacao_paralela_nota_maxima) &&
      validatesIfDecimalPlacesInRange($notaRecuperacaoEspecificaElement.val(), $notaRecuperacaoEspecificaElement.attr('id'), 0, regra.qtd_casas_decimais)) {

    beforeChangeResource($notaRecuperacaoEspecificaElement);

    var additionalVars = {
      matricula_id             : $notaRecuperacaoEspecificaElement.data('matricula_id'),
      componente_curricular_id : $notaRecuperacaoEspecificaElement.data('componente_curricular_id')
    };

    var options = {
      url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'nota_recuperacao_especifica', additionalVars),
      dataType : 'json',
      data : {att_value : $notaRecuperacaoEspecificaElement.val()},
      success : function(dataResponse) {
        afterChangeResource($notaRecuperacaoEspecificaElement);
        handleChange(dataResponse);
      }
    };

    $notaRecuperacaoEspecificaElement.data('old_value', $notaRecuperacaoEspecificaElement.val());
    postResource(options, handleErrorOnPostResource);
  } else {
    $j('#' + $notaRecuperacaoEspecificaElement.attr('id')).addClass('error');
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

    var additionalVars = {
      matricula_id             : $faltaFieldElement.data('matricula_id'),
      componente_curricular_id : $faltaFieldElement.data('componente_curricular_id')
    };

    var options = {
      url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'falta', additionalVars),
      dataType : 'json',
      data : {att_value : $faltaFieldElement.val()},
      success : function(dataResponse) {
        afterChangeResource($faltaFieldElement);
        handleChange(dataResponse);
      }
    };

    $faltaFieldElement.data('old_value', $faltaFieldElement.val());
    postResource(options, handleErrorOnPostResource);
  } else {
    $j('#' + $faltaFieldElement.attr('id')).addClass('error');

    var regra = $element.closest('tr').data('regra');

    // se presenca geral, muda o valor em todas faltas da mesma matricula
    if (regra.tipo_presenca == 'geral') {
      $j('#' + $faltaFieldElement.attr('id')).closest('table').find('.falta-matricula-' + $element
        .data('matricula_id') + '-cc')
        .not($element).addClass('error').removeClass('success');
    }
  }
}


function getEtapaParecer(regra) {
  if (regra.tipo_parecer_descritivo.split('_')[0] == 'anual')
    var etapaParecer = 'An';
  else
    var etapaParecer = $j('#etapa').val();

  return etapaParecer;
}

function postParecer($parecerFieldElement) {
  var regra = $parecerFieldElement.closest('tr').data('regra');
  var data = {
    matricula_id             : $parecerFieldElement.data('matricula_id'),
    componente_curricular_id : $parecerFieldElement.data('componente_curricular_id'),
    etapa                    : getEtapaParecer(regra),
    ano_escolar              : $j('#ano').val(),
    escola_id                : $j('#ref_cod_escola').val(),
    oper: "post",
    resource: "parecer",
    att_value : $parecerFieldElement.val()
  };

  $j.post( API_URL_BASE, data)
    .done(function(dataResponse) {
      afterChangeResource($parecerFieldElement);
      handleChange(dataResponse);
    })
    .fail(function() {
      errorCallback || handleErrorOnDeleteResource;
    });
}

function postNotaGeral($notaGeralElementField) {
  var additionalVars = {
    matricula_id             : $notaGeralElementField.data('matricula_id'),
    componente_curricular_id : $notaGeralElementField.data('componente_curricular_id')
  };

  var options = {
    url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'nota_geral', additionalVars),
    dataType : 'json',
    data : {att_value : $notaGeralElementField.val()},
    success : function(dataResponse) {
      afterChangeResource($notaGeralElementField);
      handleChange(dataResponse);
    }
  };

  $notaGeralElementField.data('old_value', $notaGeralElementField.val());
  postResource(options, handleErrorOnPostResource);
}
function postMedia($mediaElementField) {
  var additionalVars = {
    matricula_id             : $mediaElementField.data('matricula_id'),
    componente_curricular_id : $mediaElementField.data('componente_curricular_id'),
    etapa                    : $j('#etapa').val()
  };


  if (validatesIfValueIsNumeric($mediaElementField.val(), $mediaElementField.attr('id'))) {
    var options = {
      url: postResourceUrlBuilder.buildUrl(API_URL_BASE, 'media', additionalVars),
      dataType: 'json',
      data: {att_value: $mediaElementField.val()},
      success: function (dataResponse) {
        afterChangeResource($mediaElementField);
        handleChange(dataResponse);
      }
    };

    $mediaElementField.data('old_value', $mediaElementField.val());
    postResource(options, handleErrorOnPostResource);
  } else {
    $j('#' + $mediaElementField.attr('id')).addClass('error');
  }
}

function deleteMedia($mediaFieldElement){

  var resourceName = 'media';

  var additionalVars = {
    instituicao_id : $j('#ref_cod_instituicao').val(),
    escola_id : $j('#ref_cod_escola').val(),
    curso_id : $j('#ref_cod_curso').val(),
    serie_id : $j('#ref_ref_cod_serie').val(),
    turma_id : $j('#ref_cod_turma').val(),
    ano_escolar : $j('#ano').val(),
    componente_curricular_id : $mediaFieldElement.data('componente_curricular_id'),
    etapa : $j('#etapa').val(),
    matricula_id : $mediaFieldElement.data('matricula_id')
   };

  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, additionalVars),
    dataType : 'json',
    success : function(dataResponse) {
      checkIfShowNotaRecuperacaoParalelaField($mediaFieldElement.val(), dataResponse);
      afterChangeResource($mediaFieldElement);
      handleChange(dataResponse);
    }
  };

  deleteResource(resourceName, $mediaFieldElement, options, handleErrorOnDeleteResource);
}

function postSituacao($situacaoElementField) {

  var additionalVars = {
    matricula_id : $situacaoElementField.data('matricula_id')
  };

  var options = {
    url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'situacao', additionalVars),
    dataType : 'json',
    data : {att_value : $situacaoElementField.val()},
    success : function(dataResponse) {
      afterChangeResource($situacaoElementField);
      handleChange(dataResponse);
    }
  };

  $situacaoElementField.data('old_value', $situacaoElementField.val());
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
    componente_curricular_id : $notaFieldElement.data('componente_curricular_id'),
    etapa : $j('#etapa').val(),
    matricula_id : $notaFieldElement.data('matricula_id')
   };

  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, additionalVars),
    dataType : 'json',
    success : function(dataResponse) {
      checkIfShowNotaRecuperacaoParalelaField($notaFieldElement.val(), dataResponse);
      afterChangeResource($notaFieldElement);
      handleChange(dataResponse);
    }
  };

  deleteResource(resourceName, $notaFieldElement, options, handleErrorOnDeleteResource);
};

function deleteNotaRecuperacaoParalela($notaRecuperacaoParalelaElement) {
  var resourceName = 'nota_recuperacao_paralela';

  var additionalVars = {
    componente_curricular_id : $notaRecuperacaoParalelaElement.data('componente_curricular_id'),
    etapa : $j('#etapa').val(),
    matricula_id : $notaRecuperacaoParalelaElement.data('matricula_id')
   };

  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, additionalVars),
    dataType : 'json',
    success : function(dataResponse) {
      afterChangeResource($notaRecuperacaoParalelaElement);
      handleChange(dataResponse);
    }
  };
  deleteResource(resourceName, $notaRecuperacaoParalelaElement, options, handleErrorOnDeleteResource);
};

function deleteNotaRecuperacaoEspecifica($notaRecuperacaoEspecificaElement) {
  var resourceName = 'nota_recuperacao_especifica';

  var additionalVars = {
    componente_curricular_id : $notaRecuperacaoEspecificaElement.data('componente_curricular_id'),
    etapa : $j('#etapa').val(),
    matricula_id : $notaRecuperacaoEspecificaElement.data('matricula_id')
   };

  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, additionalVars),
    dataType : 'json',
    success : function(dataResponse) {
      afterChangeResource($notaRecuperacaoEspecificaElement);
      handleChange(dataResponse);
    }
  };
  deleteResource(resourceName, $notaRecuperacaoEspecificaElement, options, handleErrorOnDeleteResource);
};


function deleteNotaExame($notaExameFieldElement) {
  var resourceName = 'nota_exame';

  var additionalVars = {
    componente_curricular_id : $notaExameFieldElement.data('componente_curricular_id'),
    etapa : 'Rc',
    matricula_id : $notaExameFieldElement.data('matricula_id')
   };

  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, additionalVars),
    dataType : 'json',
    success : function(dataResponse) {
      afterChangeResource($notaExameFieldElement);
      handleChange(dataResponse);
    }
  };

  deleteResource(resourceName, $notaExameFieldElement, options, handleErrorOnDeleteResource);
};


function deleteFalta($faltaFieldElement) {

  //excluir falta se nota, nota exame e parecer (não existirem ou) estiverem sem valor
  var matriculaId = $faltaFieldElement.data('matricula_id');
  var ccId = $faltaFieldElement.data('componente_curricular_id');

  var $notaField = $j('#nota-matricula-'+ matriculaId + '-cc-' + ccId);
  var $notaExameField = $j('#nota_exame-matricula-'+ matriculaId + '-cc-' + ccId);
  var $parecerField = $j('#parecer-matricula-'+ matriculaId + '-cc-' + ccId);

  if(($notaField.length < 1 || $notaField.val() == '') &&
     ($notaExameField.length < 1 || $notaExameField.val() == '') &&
     ($parecerField.length < 1 || $j.trim($parecerField.val()) == '')
    ) {
    var resourceName = 'falta';

    var additionalVars = {
      componente_curricular_id : $faltaFieldElement.data('componente_curricular_id'),
      matricula_id : $faltaFieldElement.data('matricula_id')
     };

    var options = {
      url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, additionalVars),
      dataType : 'json',
      success : function(dataResponse) {
        afterChangeResource($faltaFieldElement);
        handleChange(dataResponse);
      }
    };

    deleteResource(resourceName, $faltaFieldElement, options, handleErrorOnDeleteResource);
  }
  else{

    $faltaFieldElement.val($faltaFieldElement.data('old_value'));

    handleMessagesDiario([{type : 'error', msg : safeUtf8Decode('Falta não pode ser removida após ter lançado notas ou parecer descritivo, tente definir como 0 (zero).')}], $faltaFieldElement.attr('id'));
  }
}


function deleteParecer($parecerFieldElement) {
  var regra = $parecerFieldElement.closest('tr').data('regra');
  var resourceName = 'parecer';

  var additionalVars = {
    componente_curricular_id : $parecerFieldElement.data('componente_curricular_id'),
    matricula_id             : $parecerFieldElement.data('matricula_id'),
    etapa                    : getEtapaParecer(regra)
   };

  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, additionalVars),
    dataType : 'json',
    success : function(dataResponse) {
      afterChangeResource($parecerFieldElement);
      handleChange(dataResponse);
    }
  };

  deleteResource(resourceName, $parecerFieldElement, options, handleErrorOnDeleteResource);
}

function deleteNotaGeral($notaGeralElementField) {
  resourceName = 'nota_geral';

  var additionalVars = {
    matricula_id             : $notaGeralElementField.data('matricula_id')
   };

  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, resourceName, additionalVars),
    dataType : 'json',
    success : function(dataResponse) {
      afterChangeResource($notaGeralElementField);
      handleChange(dataResponse);
    }
  };

  deleteResource(resourceName, $notaGeralElementField, options, handleErrorOnDeleteResource);
}

//callback handlers

function handleChange(dataResponse) {

  var componenteCurricularId = dataResponse.componente_curricular_id ? dataResponse.componente_curricular_id : "";

  var targetId = dataResponse.resource + '-matricula-' + dataResponse.matricula_id +
                 '-cc-' + componenteCurricularId;

  handleMessagesDiario(dataResponse.msgs, targetId);
  updateResourceRow(dataResponse);
}

var handleMessagesDiario = function(arrayMessage, targetId) {
  var hasError = false;
  var hasSuccess = false;

  arrayMessage = $j.map(arrayMessage, function (item, index) {
    if (item.type == 'success') {
      hasSuccess = true;
      return null;
    }

    if (item.type == 'error') {
      hasError = true;
    }

    return item;
  });

  if (hasSuccess) {
    $j('#' + targetId).addClass('success');
    $j('#' + targetId).removeClass('error');

    if (targetId.includes('falta-matricula')) {
      $j('#' + targetId).closest('table').find('.falta-matricula-' + $element
        .data('matricula_id') + '-cc')
        .not($element).addClass('success').removeClass('error');
    }
  }

  if (hasError) {
    $j('#' + targetId).addClass('error');
    $j('#' + targetId).removeClass('success');

    if (targetId.includes('falta-matricula')) {
      $j('#' + targetId).closest('table').find('.falta-matricula-' + $element
        .data('matricula_id') + '-cc')
        .not($element).addClass('error').removeClass('success');
    }
  }

  messageUtils.handleMessages(arrayMessage, targetId);
};

var regraDiferenciadaId = undefined;

var setRegraDiferenciadaId = function(regras){
  regraDiferenciadaId = undefined;
  if(regras && regras.length > 1){
    $j.each(regras, function(){
      var regraDiferenciadaAtual = this.regra_diferenciada_id;
      if(regraDiferenciadaAtual){
        var regrasDiferenciadas = regras.filter((regra)=>regra.id == regraDiferenciadaAtual);
        if(regrasDiferenciadas.length){
          regraDiferenciadaId = regrasDiferenciadas[0]['id'];
        }
      }
    });
  }
}

function setTableSearchDetails($tableSearchDetails, dataDetails) {
  setRegraDiferenciadaId(dataDetails);

  var componenteCurricularSelected = ($j('#ref_cod_componente_curricular').val() != '');
  showBotaoReplicarNotas = ($j('#mostrar_botao_replicar_todos').val() == "1");

  $j('<caption />').html('<strong>Lan&#231;amento de notas por turma</strong>').appendTo($tableSearchDetails);

  //set headers table
  var $linha = $j('<tr />');

  if (componenteCurricularSelected) {
    $j('<th />').html('&Aacute;rea de Conhecimento').appendTo($linha);
    $j('<th />').html('Componente curricular').appendTo($linha);
  }

  $j('<th />').html('Etapa').appendTo($linha);
  $j('<th />').html('Turma').appendTo($linha);
  $j('<th />').html(safeUtf8Decode('Série')).appendTo($linha);
  $j('<th />').html('Ano').appendTo($linha);
  $j('<th />').html('Escola').appendTo($linha);
  $j('<th />').html('Regra avalia&#231;&#227;o').appendTo($linha);
  $j('<th />').html('Tipo nota').appendTo($linha);
  $j('<th />').html('Tipo presen&#231;a').appendTo($linha);
  $j('<th />').html('Tipo parecer').appendTo($linha);
  $j('<th />').html(safeUtf8Decode('Recuperação paralela')).appendTo($linha);
  $j('<th />').html(safeUtf8Decode('Nota geral por etapa')).appendTo($linha);

  $linha.appendTo($tableSearchDetails);
  $j.each(dataDetails, function(){
    var regra = this;

    var $linha = $j('<tr />').addClass('cellColor');

    if (componenteCurricularSelected) {
      $j('<td />').html(($j('#ref_cod_componente_curricular :selected').parent().attr('label'))).appendTo($linha);
      $j('<td />').html(($j('#ref_cod_componente_curricular :selected').html())).appendTo($linha);
    }

    $j('<td />').html(safeToUpperCase($j('#etapa').children("[selected='selected']").html())).appendTo($linha);
    $j('<td />').html(safeToUpperCase($j('#ref_cod_turma').children("[selected='selected']").html())).appendTo($linha);
    $j('<td />').html(safeToUpperCase($j('#ref_cod_serie').children("[selected='selected']").html())).appendTo($linha);
    $j('<td />').html($j('#ano').val()).appendTo($linha);

    //field escola pode ser diferente de select caso usuario comum
    var $htmlEscolaField = $j('#ref_cod_escola').children("[selected='selected']").html() ||
                           $j('#tr_nm_escola span:last').html();

    $j('<td />').html(safeToUpperCase($htmlEscolaField)).appendTo($linha);
    var descricaoCompletaRegra = regra.id + ' - ' +safeToUpperCase(regra.nome);
    if(regra.id == regraDiferenciadaId){
      descricaoCompletaRegra = `* ${descricaoCompletaRegra}`;
    }
    $j('<td />').html(descricaoCompletaRegra).appendTo($linha);

    //corrige acentuação
    var tipoNota = safeToUpperCase(regra.tipo_nota.replace('_', ' '));
    var tipo_recuperacao_paralela = safeToUpperCase(regra.tipo_recuperacao_paralela.replace('_', ' '));
    var nota_geral_por_etapa = safeToUpperCase(regra.nota_geral_por_etapa.replace('_', ' '));

    if (tipoNota == 'NUMERICA') {
      tipoNota = 'NUMÉRICA';
    }

    if (tipo_recuperacao_paralela == 'ETAPAS ESPECIFICAS') {
      tipo_recuperacao_paralela = 'ETAPAS ESPECÍFICAS';
    }

    if (tipo_recuperacao_paralela == 'NAO UTILIZA') {
      tipo_recuperacao_paralela = 'NÃO UTILIZA';
    }

    if (nota_geral_por_etapa == 'NAO UTILIZA') {
      nota_geral_por_etapa = 'NÃO UTILIZA';
    }

    $j('<td />').html(safeUtf8Decode(tipoNota)).appendTo($linha);
    $j('<td />').html(safeToUpperCase(regra.tipo_presenca.replace('_', ' '))).appendTo($linha);
    $j('<td />').html(safeToUpperCase(regra.tipo_parecer_descritivo.replace('_', ' '))).appendTo($linha);
    $j('<td />').html(tipo_recuperacao_paralela).appendTo($linha);
    $j('<td />').html(nota_geral_por_etapa).appendTo($linha);
    $linha.appendTo($tableSearchDetails);
  });

  if(regraDiferenciadaId){
    var tfootColspan = $linha.find('th').length;
    var $tfoot = $j('<tfoot/>').attr('style', 'position: absolute;margin-top: 30px;');
    $j('<tr/>').append(
      $j('<td/>').attr('style', 'text-align: left;')
                 .attr('colspan', tfootColspan)
                 .text(REGRA_DIFERENCIADA_TEXT)
    ).appendTo($tfoot)

    $tableSearchDetails.append($tfoot);
    $tableSearchDetails.attr('style','border-bottom-width: 0px;')
  }

  $tableSearchDetails.show();

  nomenclatura_exame = dataDetails[0].nomenclatura_exame;
  regra_dependencia = dataDetails[0].regra_dependencia;

  $tableSearchDetails.data('regras', dataDetails);
}

var nextTabIndex = 1;

function setNextTabIndex($element) {
  $element.attr('tabindex', nextTabIndex);
  $element.addClass('tabable');
  nextTabIndex += 1;
}

function handleSearch($resultTable, dataResponse) {

  var regras = $tableSearchDetails.data('regras');
  var useNota                 = regras.filter(function(regra){return regra.tipo_nota != 'nenhum'; }).length > 0;
  var ultimaEtapa             = regras[0]['quantidade_etapas'] == $j('#etapa').val();
  var definirComponentesEtapa = regras.filter(function(regra){return regra.definir_componente_por_etapa; }).length > 0;

  var componenteCurricularSelected = ($j('#ref_cod_componente_curricular').val() != '');

  // resets next tabindex
  var nextTabIndex = 1;

  //set headers
  var $linha = $j('<tr />');
  $j('<th />').html(safeUtf8Decode('Matrícula')).appendTo($linha);
  $j('<th />').attr('colspan', componenteCurricularSelected ? 0 : 5).html('Aluno').appendTo($linha);

  if (componenteCurricularSelected)
    updateComponenteCurricularHeaders($linha, $j('<th />'));

  $linha.appendTo($resultTable);
  //set (result) rows
  $j.each(dataResponse.matriculas, function(index, value) {
    var $linha = $j('<tr />').addClass(componenteCurricularSelected ? '' : 'strong');

    $linha.data('regra', value.regra);

    $j('<td />').html(value.matricula_id).addClass('center').appendTo($linha);

    var descricaoAluno = `${value.aluno_id} - ${safeToUpperCase(value.nome)}`;
    if(value.regra.id == regraDiferenciadaId){
      descricaoAluno = `* ${descricaoAluno}`;
    }
    $j('<td />').html(descricaoAluno)
                .attr('colspan', componenteCurricularSelected ? 0 : 5)
                .appendTo($linha);

    if (value.componentes_curriculares){
      if (componenteCurricularSelected && value.componentes_curriculares.length > 0)
        updateComponenteCurricular($linha, value.matricula_id, value.componentes_curriculares[0], value.regra);
    }else{
      if(value.situacao_deslocamento){
        var $situacaoTdDeslocamento = $j('<td />').addClass('situacao-matricula-cc')
                                  .attr('id', 'situacao-matricula-' + value.matricula_id)
                                  .data('matricula_id', value.matricula_id)
                                  .addClass('center')
                                  .addClass('matricula-situacao-deslocamento')
                                  .html(value.situacao_deslocamento)
                                  .appendTo($linha);

        var colCount = 0;
        $resultTable.find('tr:nth-child(1) th').each(function () {
          colCount++;
        });
        for (var i = 0; i < colCount - 3; i++) {
          $j('<td />').html('-').addClass('center').appendTo($linha);
        };
      }
    }

    $linha.fadeIn('slow').appendTo($resultTable);
    $linha.appendTo($resultTable);

    if (! componenteCurricularSelected && value.componentes_curriculares)
      updateComponenteCurriculares($resultTable, value.matricula_id, value.componentes_curriculares, value.regra);

    if((value.regra.quantidade_etapas == $j('#etapa').val() ) && (value.regra.progressao_manual || value.regra.progressao_manual_ciclo) && !componenteCurricularSelected){
      situacaoFinalField(dataResponse.matricula_id, dataResponse.situacao).appendTo($resultTable);
    }

    if ((!componenteCurricularSelected) && (showBotaoReplicarNotas))
      criaBotaoReplicarNotasPorArea(value.componentes_curriculares);

  });

  // seta colspan [th, td].aluno quando exibe nota exame
  if (useNota &&
      (ultimaEtapa || definirComponentesEtapa)) {
    $resultTable.find('[colspan]:not(.area-conhecimento)').attr('colspan', componenteCurricularSelected ? 1 : 10);
  }

  $resultTable.find('tr:even').addClass('even');

  //set onchange events
  var $notaFields = $resultTable.find('.nota-matricula-cc');
  var $notaExameFields = $resultTable.find('.nota_exame-matricula-cc');
  var $faltaFields = $resultTable.find('.falta-matricula-cc');
  var $parecerFields = $resultTable.find('.parecer-matricula-cc');
  var $notaRecuperacaoParalelaFields = $resultTable.find('.nota-recuperacao-paralela-cc');
  var $notaRecuperacaoEspecificaFields = $resultTable.find('.nota-recuperacao-especifica-matricula-cc');
  var $notaGeralEtapaFields = $resultTable.find('.nota-geral-etapa');
  var $mediaFields = $resultTable.find('.media-cc');
  var $situacaoField = $resultTable.find('.situacao-cc');

  $notaFields.on('change', changeNota);
  $notaExameFields.on('change', changeNotaExame);
  $faltaFields.on('change', changeFalta);
  $parecerFields.on('change', changeParecer);
  $notaRecuperacaoParalelaFields.on('change', changeNotaRecuperacaoParalela);
  $notaRecuperacaoEspecificaFields.on('change', changeNotaRecuperacaoEspecifica);
  $notaGeralEtapaFields.on('change', changeNotaGeralEtapa);
  $mediaFields.on('change', changeMedia);
  $situacaoField.on('change', changeSituacao);

  $resultTable.addClass('styled').find('.tabable:first').focus();
  navegacaoTab(dataResponse.navegacao_tab);
  if(!dataResponse.can_change){
    $j('#form_resultado input').attr('disabled', 'disabled');
    $j('#form_resultado select').attr('disabled', 'disabled');
    $j('#form_resultado textarea').attr('disabled', 'disabled');
  }else{
    $j('#form_resultado input').removeAttr('disabled');
    $j('#form_resultado select').removeAttr('disabled');
    $j('#form_resultado textarea').removeAttr('disabled');
  }

  locked = dataResponse.locked;

  if ((componenteCurricularSelected) && (showBotaoReplicarNotas))
    criaBotaoReplicarNotas();

  $j('.flashMessages').addClass('msg-diario');
}

function _notaField(matriculaId, componenteCurricularId, klass, id, value, areaConhecimentoId, maxLength, tipoNota, regra) {
  var notaConceitual = regra.tipo_nota == 'numericaconceitual' && tipoNota == 1;
  var notaNumerica   = regra.tipo_nota == 'numericaconceitual' && tipoNota == 2;
  var opcoesNotas    = notaConceitual ? regra.opcoes_notas_conceituais : regra.opcoes_notas;

  if(regra.tipo_nota == 'conceitual' || notaConceitual) {
    var $notaField = $j('<select />')
                     .addClass(klass)
                     .addClass(areaConhecimentoId)
                     .attr('id', id)
                     .data('matricula_id', matriculaId)
                     .data('componente_curricular_id', componenteCurricularId);

    // adiciona opcoes notas ao select
    var $option = $j('<option />').appendTo($notaField);
    for(var i = 0; i < opcoesNotas.length; i++) {
      var $option = $j('<option />').val(opcoesNotas[i].valor_maximo).html(opcoesNotas[i].descricao);

      if (value == opcoesNotas[i].valor_maximo)
        $option.attr('selected', 'selected');

      $option.appendTo($notaField);
    }
  }
  else {
    var $notaField = $j('<input />').addClass(klass)
                                    .attr('id', id)
                                    .attr('maxlength', maxLength)
                                    .attr('size', maxLength)
                                    .val(value)
                                    .data('matricula_id', matriculaId)
                                    .data('componente_curricular_id', componenteCurricularId);
  }

  $notaField.data('old_value', value);
  setNextTabIndex($notaField);

  return $j('<td />').html($notaField).addClass('center');
}

function _mediaField(matriculaId, componenteCurricularId, klass, id, value, areaConhecimentoId, maxLength, tipoNota, regra) {
  var notaConceitual = regra.tipo_nota == 'numericaconceitual' && tipoNota == 1;
  var notaNumerica   = regra.tipo_nota == 'numericaconceitual' && tipoNota == 2;
  var opcoesNotas    = notaConceitual ? regra.opcoes_notas_conceituais : regra.opcoes_notas;

  if(regra.tipo_nota == 'conceitual' || notaConceitual) {
    var $notaField = $j('<select />')
                     .addClass(klass)
                     .addClass(areaConhecimentoId)
                     .attr('id', id)
                     .data('matricula_id', matriculaId)
                     .data('componente_curricular_id', componenteCurricularId);

    // adiciona opcoes notas ao select
    var $option = $j('<option />').appendTo($notaField);
    var selected = false;
    for(var i = 0; i < opcoesNotas.length; i++) {
      var $option = $j('<option />').val(opcoesNotas[i].valor_maximo).html(opcoesNotas[i].descricao);

      valorMinimo = parseFloat(opcoesNotas[i].valor_minimo);
      valorMaximo = parseFloat(opcoesNotas[i].valor_maximo);

      if ((valorMaximo == value) || ((value > valorMinimo && value < valorMaximo) && !selected)){
        $option.attr('selected', 'selected');
        selected = true;
      }

      $option.appendTo($notaField);
    }
  }
  else {
    var $notaField = $j('<input />').addClass(klass)
                                    .attr('id', id)
                                    .attr('maxlength', maxLength)
                                    .attr('size', maxLength)
                                    .val(value)
                                    .data('matricula_id', matriculaId)
                                    .data('componente_curricular_id', componenteCurricularId);
  }

  $notaField.data('old_value', value);
  setNextTabIndex($notaField);

  return $j('<td />').html($notaField).addClass('center');
}

function notaField(matriculaId, componenteCurricularId, value, areaConhecimentoId, maxLength, tipoNota, regra) {
  return _notaField(matriculaId,
                    componenteCurricularId,
                    'nota-matricula-cc',
                    'nota-matricula-' + matriculaId + '-cc-' + componenteCurricularId,
                    value,
                    'area-id-' + areaConhecimentoId,
                    maxLength,
                    tipoNota,
                    regra);
}


function notaExameField(matriculaId, componenteCurricularId, value, maxLength, tipoNota, regra) {
  return _notaField(matriculaId,
                    componenteCurricularId,
                    'nota_exame-matricula-cc',
                    'nota_exame-matricula-' + matriculaId + '-cc-' + componenteCurricularId,
                    value,
                    null,
                    maxLength,
                    tipoNota,
                    regra);
}

function notaGeralEtapaField(matriculaId, componenteCurricularId, value, maxLength){
  var $notaField = $j('<input />').addClass('nota-geral-etapa')
                                    .attr('id', 'nota-geral-etapa-' + matriculaId)
                                    .attr('maxlength', maxLength)
                                    .attr('size', maxLength)
                                    .val(value)
                                    .data('matricula_id', matriculaId)
                                    .data('componente_curricular_id', componenteCurricularId);

  $notaField.data('old_value', value);

  setNextTabIndex($notaField);

  return $j('<td />').html($notaField).addClass('center');
}

function notaNecessariaField(matriculaId, componenteCurricularId, value){
  if (value=='' || value==undefined) value = '-';
  var $notaNecessariaField = $j('<span />').addClass('nn-matricula-cc')
                                   .addClass('nn-matricula-' + matriculaId + '-cc')
                                   .attr('id', 'nn-matricula-' + matriculaId + '-cc-' + componenteCurricularId)
                                   .text(value);
  setNextTabIndex($notaNecessariaField);
  return $j('<td />').html($notaNecessariaField).addClass('center');
}


function faltaField(matriculaId, componenteCurricularId, value) {
  var $faltaField = $j('<input />').addClass('falta-matricula-cc')
                                   .addClass('falta-matricula-' + matriculaId + '-cc')
                                   .attr('id', 'falta-matricula-' + matriculaId + '-cc-' + componenteCurricularId)
                                   .attr('maxlength', '4')
                                   .attr('size', '4')
                                   .val(value)
                                   .data('old_value', value)
                                   .data('matricula_id', matriculaId)
                                   .data('componente_curricular_id', componenteCurricularId);

  setNextTabIndex($faltaField);
  return $j('<td />').html($faltaField).addClass('center');
}

function parecerField(matriculaId, componenteCurricularId, value) {
  var $parecerField = $j('<textarea />').attr('cols', '40')
                                        .attr('rows', '5')
                                        .addClass('parecer-matricula-cc')
                                        .addClass('parecer-matricula-' + matriculaId + '-cc')
                                        .attr('id', 'parecer-matricula-' + matriculaId + '-cc-' + componenteCurricularId)
                                        .val(value)
                                        .data('old_value', value)
                                        .data('matricula_id', matriculaId)
                                        .data('componente_curricular_id', componenteCurricularId);

  setNextTabIndex($parecerField);
  return $j('<td />').addClass('center').html($parecerField);
}

function notaRecuperacaoParalelaField(matriculaId, componenteCurricularId, value, areaConhecimentoId, maxLength, regra) {
  return _notaField(matriculaId,
                    componenteCurricularId,
                    'nota-recuperacao-paralela-cc',
                    'nota_recuperacao_paralela-matricula-' + matriculaId + '-cc-' + componenteCurricularId,
                    value,
                    'area-id-' + areaConhecimentoId,
                    maxLength,
                    undefined,
                    regra);
}

function notaRecuperacaoEspecificaField(matriculaId, componenteCurricularId, value, areaConhecimentoId, maxLength, tipoNota, regra) {
  return _notaField(matriculaId,
                    componenteCurricularId,
                    'nota-recuperacao-especifica-matricula-cc',
                    'nota_recuperacao_especifica-matricula-' + matriculaId + '-cc-' + componenteCurricularId,
                    value,
                    'area-id-' + areaConhecimentoId,
                    maxLength,
                    tipoNota,
                    regra);
}

function mediaField(matriculaId, componenteCurricularId, value, maxLength, tipoNota, regra){
  return _mediaField(matriculaId,
                    componenteCurricularId,
                    'media-cc',
                    'media-matricula-' + matriculaId + '-cc-' + componenteCurricularId,
                    value,
                    0,
                    maxLength,
                    tipoNota,
                    regra);
}

function getNotaGeralMaxLength(regra){
  return regra.nota_maxima_geral.toString().length + regra.qtd_casas_decimais + 1;
}

function getNotaExameFinalMaxLength(regra){
  return regra.nota_maxima_exame_final.toString().length + regra.qtd_casas_decimais + 1;
}

function getNotaRecuperacaoEspecificaMaxLength(regra){
  return regra.tipo_recuperacao_paralela_nota_maxima.toString().length + regra.qtd_casas_decimais + 1;
}


function updateComponenteCurricular($targetElement, matriculaId, cc, regra) {
  var usaRecuperacaoParalelaPorEtapa = (regra.tipo_recuperacao_paralela == 'por_etapa');
  var useNota                = regra.tipo_nota != 'nenhum';
  var useParecer             = regra.tipo_parecer_descritivo != 'nenhum';
  var usaNotaGeralPorEtapa = regra.nota_geral_por_etapa == 'SIM';
  var habilitaCampoEtapaEspecifica = regra.tipo_recuperacao_paralela == 'etapas_especificas' && regra.habilita_campo_etapa_especifica;
  var progressaoManual = regra.progressao_manual;
  var progressaoManualCiclo = regra.progressao_manual_ciclo;
  var progressaoContinuada = regra.progressao_continuada;
  var mediaRecuperacaoParalela = regra.media_recuperacao_paralela;
  var ultimaEtapa = regra.quantidade_etapas == $j('#etapa').val();
  var definirComponentesEtapa = regra.definir_componente_por_etapa;
  var ultimaEtapaComponente = cc.ultima_etapa ==  $j('#etapa').val();
  var mediaBloqueada = cc.media_bloqueada;

  var $emptyTd = $j('<td/>').addClass('center');

  var $situacaoTd = $j('<td />').addClass('situacao-matricula-cc')
                                .attr('id', 'situacao-matricula-' + matriculaId + '-cc-' + cc.id)
                                .data('matricula_id', matriculaId)
                                .data('componente_curricular_id', cc.id)
                                .data('media_bloqueada', mediaBloqueada)
                                .addClass('center')
                                .html(cc.situacao)
                                .appendTo($targetElement);

  colorizeSituacaoTd($situacaoTd, cc.situacao);

  var consideraNotaOriginal = usaRecuperacaoParalelaPorEtapa || habilitaCampoEtapaEspecifica;

  if(useNota){
    notaField(matriculaId, cc.id, consideraNotaOriginal ? cc.nota_original : cc.nota_atual, cc.area_id, getNotaGeralMaxLength(regra), cc.tipo_nota, regra).appendTo($targetElement);
  }else if(hUseNota){
    $emptyTd.clone().appendTo($targetElement);
  }

  if(useNota && usaRecuperacaoParalelaPorEtapa){
    hasNotaRecuperacaoParalela = (cc.nota_recuperacao_paralela != '');
    hasMediaRecuperacaoParalela = (mediaRecuperacaoParalela != null);
    hasNotaAtual = !!cc.nota_atual;

    $notaRecuperacaoParalelaField = notaRecuperacaoParalelaField(matriculaId, cc.id, cc.nota_recuperacao_paralela, cc.area_id, getNotaGeralMaxLength(regra), regra);
    $notaRecuperacaoParalelaField.appendTo($targetElement);

    shouldShowNotaRecuperacaoParalela = (((hasNotaRecuperacaoParalela) ||
                                         (cc.nota_original < mediaRecuperacaoParalela) ||
                                         (!hasMediaRecuperacaoParalela)) &&
                                         (hasNotaAtual));

    if(!shouldShowNotaRecuperacaoParalela){
      $notaRecuperacaoParalelaField.children().hide();
    }
  }else if(hUseNota && hUsaRecuperacaoParalelaPorEtapa){
    $emptyTd.clone().appendTo($targetElement);
  }

  if(useNota && habilitaCampoEtapaEspecifica){
    $notaRecuperacaoEspecificaField = notaRecuperacaoEspecificaField(matriculaId, cc.id, cc.nota_recuperacao_especifica, cc.area_id, getNotaRecuperacaoEspecificaMaxLength(regra), cc.tipo_nota, regra);
    $notaRecuperacaoEspecificaField.appendTo($targetElement);

    var shouldShowNotaRecuperacaoEspecifica = cc.should_show_recuperacao_especifica;

    if(!shouldShowNotaRecuperacaoEspecifica){
      $notaRecuperacaoEspecificaField.children().hide();
    }
  }else if(hUseNota && hHabilitaCampoEtapaEspecifica){
    $emptyTd.clone().appendTo($targetElement);
  }

  // mostra nota exame, média final e situação do aluno caso estiver selecionado a ultima etapa
  if(useNota && (ultimaEtapa || (definirComponentesEtapa && !progressaoContinuada))){
    var $fieldNotaExame = notaExameField(matriculaId, cc.id, cc.nota_exame, getNotaExameFinalMaxLength(regra), cc.tipo_nota, regra);

    var $fieldNN = notaNecessariaField(matriculaId, cc.id, (cc.nota_necessaria_exame || '-'));

    if (!formulaCalculoMediaRecuperacao) {
      $fieldNotaExame.hide();
      $fieldNN.hide();
    }

    if (cc.nota_exame == '' && safeToLowerCase(cc.situacao) != 'em exame'){
      $fieldNotaExame.children().hide();
      $fieldNN.children().text('-');
    }

    if(ultimaEtapa || ultimaEtapaComponente) {
      $fieldNotaExame.appendTo($targetElement);
    }else {
      $emptyTd.clone().appendTo($targetElement);
    }

    /* Adiciona campo com nota necessária, exeto em casos de componentes
       específicos por etapa */
    if (!definirComponentesEtapa) {
      $fieldNN.appendTo($targetElement);
    }else if(!hDefinirComponentesEtapa){
      $emptyTd.clone().appendTo($targetElement);
    }

    if(progressaoManual || progressaoManualCiclo){
      if(regra.tipo_nota == 'numerica'){
        var $fieldMedia = mediaField(matriculaId, cc.id, cc.media_arredondada, getNotaGeralMaxLength(regra), cc.tipo_nota, regra);
      }else{
        var $fieldMedia = mediaField(matriculaId, cc.id, cc.media, getNotaGeralMaxLength(regra), cc.tipo_nota, regra);
      }

      $fieldMedia.appendTo($targetElement);
    }else if(hAlgumaProgressaoManual || hAlgumaProgressaoManualCiclo){
      $emptyTd.clone().appendTo($targetElement);
    }
  }else if((hUseNota && (hUltimaEtapa || (hDefinirComponentesEtapa && !hProgressaoContinuada)))){
    $emptyTd.clone().appendTo($targetElement);
    if(!hDefinirComponentesEtapa){
      $emptyTd.clone().appendTo($targetElement);
    }
    if(hAlgumaProgressaoManual || hAlgumaProgressaoManualCiclo){
      $emptyTd.clone().appendTo($targetElement);
    }
  }

  if(usaNotaGeralPorEtapa){
    notaGeralEtapaField(matriculaId, cc.id, cc.nota_geral_etapa, 5).appendTo($targetElement);
  } else if(hUsaNotaGeralPorEtapa){
    $emptyTd.clone().appendTo($targetElement);
  }

  faltaField(matriculaId, cc.id, cc.falta_atual).appendTo($targetElement);

  if (useParecer){
    parecerField(matriculaId, cc.id, cc.parecer_atual).appendTo($targetElement);
  }else if(hUseParecer){
    $emptyTd.clone().appendTo($targetElement);
  }
}

var hHabilitaCampoEtapaEspecifica;
var hUsaNotaGeralPorEtapa;
var hUsaRecuperacaoParalelaPorEtapa;
var hUseNota;
var hUseParecer;
var hUltimaEtapa;
var hDefinirComponentesEtapa;
var hProgressaoManual;
var hProgressaoManualCiclo;
var hProgressaoContinuada;
var hAlgumaProgressaoManual;
var hAlgumaProgressaoManualCiclo;
var formulaCalculoMediaRecuperacao;

function updateComponenteCurricularHeaders($targetElement, $tagElement) {
  var regras = $tableSearchDetails.data('regras');
  hHabilitaCampoEtapaEspecifica = regras.filter(function(regra){return regra.tipo_recuperacao_paralela == 'etapas_especificas' && regra.habilita_campo_etapa_especifica; }).length > 0;
  hUsaNotaGeralPorEtapa = regras.filter(function(regra){return regra.nota_geral_por_etapa == 'SIM'; }).length > 0;
  hUsaRecuperacaoParalelaPorEtapa = regras.filter(function(regra){return regra.tipo_recuperacao_paralela == 'por_etapa'; }).length > 0;
  hUseNota                 = regras.filter(function(regra){return regra.tipo_nota != 'nenhum'; }).length > 0;
  hUseParecer              = regras.filter(function(regra){return regra.tipo_parecer_descritivo != 'nenhum'; }).length > 0;
  hUltimaEtapa             = regras[0]['quantidade_etapas'] == $j('#etapa').val();
  hDefinirComponentesEtapa = regras.filter(function(regra){return regra.definir_componente_por_etapa; }).length > 0;
  hProgressaoManual = regras.filter(function(regra){return regra.progressao_manual; }).length == regras.length;
  hProgressaoManualCiclo = regras.filter(function(regra){return regra.progressao_manual_ciclo; }).length == regras.length;
  hProgressaoContinuada = regras.filter(function(regra){return regra.progressao_continuada; }).length == regras.length;
  hAlgumaProgressaoManual = regras.filter(function(regra){return regra.progressao_manual; }).length;

  hAlgumaProgressaoManualCiclo = regras.filter(function(regra){return regra.progressao_manual_ciclo; }).length;
  formulaCalculoMediaRecuperacao = regras.filter(function(regra){return regra.formula_recuperacao_final; }).length > 0;

  $tagElement.clone().addClass('center').html(safeUtf8Decode('Situação')).appendTo($targetElement);

  if (hUseNota) {
    $tagElement.clone().addClass('center').html('Nota').appendTo($targetElement);

    if(hUsaRecuperacaoParalelaPorEtapa){
      $tagElement.clone().addClass('center').html(safeUtf8Decode('Recuperação paralela')).appendTo($targetElement);
    }
    if(hHabilitaCampoEtapaEspecifica){
      var tipoRecuperacaoParalelaNome = regras.filter(function(regra){return regra.tipo_recuperacao_paralela == 'etapas_especificas' && regra.habilita_campo_etapa_especifica; })[0]['tipo_recuperacao_paralela_nome'];
      $tagElement.clone().addClass('center').html(safeUtf8Decode(tipoRecuperacaoParalelaNome)).appendTo($targetElement);
    }
    if (hUltimaEtapa || (hDefinirComponentesEtapa && !hProgressaoContinuada)){
      if (formulaCalculoMediaRecuperacao) {
        $tagElement.clone().addClass('center').html('Nota ' + nomenclatura_exame).appendTo($targetElement);
        if (!hDefinirComponentesEtapa) {
          $tagElement.clone().addClass('center').html(safeUtf8Decode('Nota necessária no ' + nomenclatura_exame)).appendTo($targetElement);
        }
      }
      if(hAlgumaProgressaoManual || hAlgumaProgressaoManualCiclo){
        $tagElement.clone().addClass('center').html(safeUtf8Decode('Média final')).appendTo($targetElement);
      }
    }
  }
  if(hUsaNotaGeralPorEtapa){
    $tagElement.clone().addClass('center').html('Nota geral da etapa').appendTo($targetElement);
  }
  $tagElement.clone().addClass('center').html('Falta').appendTo($targetElement);

  if (hUseParecer)
    $tagElement.clone().addClass('center').html('Parecer descritivo').appendTo($targetElement);
}

function updateComponenteCurriculares($targetElement, matriculaId, componentesCurriculares, regra) {
  var useNota                = regra.tipo_nota != 'nenhum';
  var useParecer             = regra.tipo_parecer_descritivo != 'nenhum';

  var areas = new Array();

  var setaDireita = "<img src=\"imagens/mytdt/seta-preta-direita.png\" align=\"top\" class=\"area-conhecimento-seta seta-direita\" />";
  var setaBaixo   = "<img src=\"imagens/mytdt/seta-branca-baixo.png\" align=\"top\" class=\"area-conhecimento-seta seta-baixo\" />";

  $j.each(componentesCurriculares, function(index, cc) {

    if (areas.indexOf(cc.area_id) == -1) {
      areas.push(cc.area_id);

      //primeiro o header para calcular o colspan
      var $ccHeader = $j('<tr />').addClass('strong').addClass('tr-componente-curricular').data('areaid', cc.area_id);
      $j('<td />').addClass('center').html('Componente curricular').appendTo($ccHeader);
      updateComponenteCurricularHeaders($ccHeader, $j('<td />'));

      //pegando o colspan
      var areaColspan = $j('td', $ccHeader).length;

      var $areaRow = $j('<tr />').addClass('tr-area-conhecimento').data('areaid', cc.area_id);
      var conteudo = setaDireita + setaBaixo + " " + cc.area_nome;
      $j('<td />').addClass('area-conhecimento').attr('colspan', areaColspan).html(conteudo).appendTo($areaRow);

      //por fim adicionando primeiro a área depois o heade
      $areaRow.appendTo($targetElement);
      $ccHeader.appendTo($targetElement);
     }

    var $ccRow = $j('<tr />').addClass('tr-componente-curricular').data('areaid', cc.area_id);
    $ccRow.data('regra', regra);

    $j('<td />').addClass('center').html(cc.nome).appendTo($ccRow);
    updateComponenteCurricular($ccRow, matriculaId, cc, regra);

    $ccRow.appendTo($targetElement);
  });

  $j('.tr-area-conhecimento').bind('click', function() {

    $j('td', this).toggleClass('area-conhecimento-destaque');

    var fechado = $j('.seta-baixo', this).is(':hidden');
    if (fechado) {
      $j('.seta-baixo', this).css('display', 'inline');
      $j('.seta-direita', this).css('display', 'none');
    } else {
      $j('.seta-baixo', this).css('display', 'none');
      $j('.seta-direita', this).css('display', 'inline');
    }

    var id = $j(this).data('areaid');
    $j('.tr-componente-curricular').each(function() {
      if ($j(this).data('areaid') == id) {
        if ($j(this).is(':hidden')) {
          $j(this).slideRow('down');
        } else {
          $j(this).slideRow('up');
        }
      }
    });
  });
  $j('.tr-area-conhecimento').first().trigger('click');
}


function updateResourceRow(dataResponse) {
  var matriculaId     = dataResponse.matricula_id;
  var ccId            = dataResponse.componente_curricular_id;

  var $situacaoField  = $j('#situacao-matricula-' + matriculaId + '-cc-' + ccId);
  var $fieldNotaExame = $j('#nota_exame-matricula-' + matriculaId + '-cc-' + ccId);
  var $fieldNotaEspecifica = $j('#nota_recuperacao_especifica-matricula-' + matriculaId + '-cc-' + ccId);
  var $fieldNN = $j('#nn-matricula-' + matriculaId + '-cc-' + ccId);
  var $fieldMedia = $j('#media-matricula-' + matriculaId + '-cc-' + ccId);

  var regra = $situacaoField.closest('tr').data('regra');

  var habilitaCampoEtapaEspecifica = regra.tipo_recuperacao_paralela == 'etapas_especificas' && regra.habilita_campo_etapa_especifica;
  var usaNotaGeralPorEtapa = regra.nota_geral_por_etapa == 'SIM';

  if(usaNotaGeralPorEtapa){
    $situacaoField = $j('.situacao-matricula-cc');
  }
  $situacaoField.html(dataResponse.situacao);
  colorizeSituacaoTd($situacaoField.closest('td'), dataResponse.situacao);

  if(!$fieldNotaExame.is(':visible') &&
    ($fieldNotaExame.val() != '' || safeToLowerCase(dataResponse.situacao) == 'em exame')) {

    $fieldNotaExame.show();

    if(sentidoTab=="1"){
      $fieldNotaExame.focus();
    }

    $fieldNN.text(dataResponse.nota_necessaria_exame || '-');
  }
  else if($fieldNotaExame.val() == '' && safeToLowerCase(dataResponse.situacao) != 'em exame'){
    $fieldNotaExame.hide();
    $fieldNN.text('-');
  }else
    $fieldNN.text(dataResponse.nota_necessaria_exame || '-');

  if(dataResponse.resource != 'nota_exame' && dataResponse.resource != 'nota_recuperacao_especifica'){
    if(habilitaCampoEtapaEspecifica && dataResponse.should_show_recuperacao_especifica ){
      $fieldNotaEspecifica.show();
      $fieldNotaEspecifica.focus();
    }else
      $fieldNotaEspecifica.hide();
  }
  changeMediaValue($fieldMedia.attr('id'), dataResponse.media, dataResponse.media_arredondada, regra);
}

function changeMediaValue(elementId, nota, notaArredondada, regra){

  if(nota != undefined){
    if(regra.tipo_nota == 'conceitual'){
        var valorSelecionado = 0;
        var opcoesNotas = regra.opcoes_notas;
        for(var i = 0 ; i < opcoesNotas.length; i++){

          valorMinimo = parseFloat(opcoesNotas[i].valor_minimo);
          valorMaximo = parseFloat(opcoesNotas[i].valor_maximo);

          if (nota >= valorMinimo && nota <= valorMaximo) {
            valorSelecionado = valorMaximo;
          }
        };
        $j('#' + elementId).val(valorSelecionado);

    }else{
      $j('#' + elementId).val(notaArredondada);
    }
  }
}

function situacaoFinalField($matriculaId, $situacao){

  var $selectSituacao  = $j('<select />').attr('id', 'situacao' + '-matricula-' + $matriculaId + '-cc-').addClass('situacao-cc').data('matricula_id', $matriculaId);
  var $optionDefault                = $j('<option />').html('').val(0).attr('selected', 'selected');
  var $optionAprovado               = $j('<option />').html('Aprovado').val(1);
  var $optionRetido                 = $j('<option />').html('Retido').val(2);
  var $optionAprovadoPeloConselho   = $j('<option />').html('Aprovado pelo conselho').val(13);
  var $optionAprovadoComDependencia = $j('<option />').html('Aprovado com dependência').val(12);

  $optionDefault.appendTo($selectSituacao);
  $optionAprovado.appendTo($selectSituacao);
  $optionRetido.appendTo($selectSituacao);
  $optionAprovadoPeloConselho.appendTo($selectSituacao);
  if (regra_dependencia) {
    $optionAprovadoComDependencia.appendTo($selectSituacao);
  }

  var $element = $j('<tr />').addClass('center resultado-final');
  $j('<td />').addClass('center resultado-final').html(safeUtf8Decode('Situação final')).appendTo($element);
  $j('<td />').addClass('resultado-final-esquerda').attr('colspan', '6').html($selectSituacao).appendTo($element);

  return $element;
}

function colorizeSituacaoTd(tdElement, situacao) {
  if (safeToLowerCase(situacao) == 'retido')
    $j(tdElement).addClass('error');
  else
    $j(tdElement).removeClass('error');
}

function canSearch(){

  if ($j('#ref_cod_matricula').val() == '' &&  $j('#ref_cod_componente_curricular').val() == '') {
    alert(safeUtf8Decode('Selecione um Componente curricular e/ou uma Matrícula'));
    return false;
  }

  return true;
}

function myNextValid($selectElement) {
  var a = $selectElement.find('option:selected');
  if (a.next('option').length == 0) {
    b = a.parent();
    if (b.prop('tagName').toUpperCase() == 'SELECT') {
      return null;
    } else {
      return b.next().children('option:first');
    }
  } else {
    return a.next('option');
  }
}

function selectNextOption($selectElement){
  var $nextOption = myNextValid($selectElement);

  if ($nextOption.val() != undefined) {
    $selectElement.val($nextOption.val());

    clearSearchResult();
    $j('#botao_busca').click();
  }

  else {
    alert(safeUtf8Decode('Você chegou à última opção.'));
    showSearchForm();
  }
}

function nextComponenteCurricular(){
  selectNextOption($j('#ref_cod_componente_curricular'));
}

function nextMatricula(){
  selectNextOption($j('#ref_cod_matricula'));
}

function nextEtapa(){
  selectNextOption($j('#etapa'));
}

function showNextSelectionButton() {
  var $ccField        = $j('#ref_cod_componente_curricular');
  var $matriculaField = $j('#ref_cod_matricula');
  var $etapaField = $j('#etapa');

  if ($ccField.val() != '') {
    $j("<a href='#'>Pr&#243;ximo componente curricular</a>").bind('click', nextComponenteCurricular)
                                .attr('style', 'text-decoration: underline')
                                .appendTo($navActions);
  }

  if ($matriculaField.val() != '') {
    $j("<a href='#'>Pr&#243;xima matr&#237;cula</a>").bind('click', nextMatricula)
                                .attr('style', 'text-decoration: underline')
                                .appendTo($navActions);
  }

  if ($etapaField.val() != '') {
    $j("<a href='#'>Próxima etapa</a>").bind('click', nextEtapa)
                                .attr('style', 'text-decoration: underline')
                                .appendTo($navActions);
  }
}

function navegacaoTab(sentido){
    //se for no sentido vertical
    if(sentido=="2"){
        i = 1;

      $j(document).find('.nota-matricula-cc').each(function(){
        $j(this).attr('tabindex', i);
        i++;
      });
      $j(document).find('.nota-recuperacao-paralela-cc').each(function(){
        $j(this).attr('tabindex', i);
        i++;
      });
      $j(document).find('.nota_exame-matricula-cc').each(function(){
        $j(this).attr('tabindex', i);
        i++;
      });
      $j(document).find('.nn-matricula-cc').each(function(){
        $j(this).attr('tabindex', i);
        i++;
      });
      $j(document).find('.falta-matricula-cc').each(function(){
        $j(this).attr('tabindex', i);
        i++;
      });
      $j(document).find('.parecer-matricula-cc').each(function(){
        $j(this).attr('tabindex', i);
        i++;
      });
    }

    sentidoTab = sentido;
}

function criaBotaoReplicarNotasPorArea(componentesCurriculares){

  var uniqueAreaConhecimento = [];

  $j.each(componentesCurriculares, function(index, element){
    if ($j.inArray(element.area_id, uniqueAreaConhecimento) == -1) uniqueAreaConhecimento.push(element.area_id);
  });

  $j.each(uniqueAreaConhecimento, function(index, value) {

    $j('<button/>').html('Replicar a todos')
                   .attr('type','button')
                   .attr('id','replicar-todas-notas-' + value)
                   .addClass('submit')
                   .appendTo($j('<p/>').insertAfter($j('.area-id-' + value)
                                       .first()))
                   .unbind();
    $j('#replicar-todas-notas-' + value).on('click', function(){
      if(confirm(safeUtf8Decode("Você deseja realmente modificar todos os conceitos desta área de conhecimento?"))){
        var notaPadrao = $j('.area-id-' + value).first().val();
        $j('.area-id-' + value).each(function(){
          var regra = $j(this).closest('tr').data('regra');
          if(regra.id != regraDiferenciadaId){
            $j(this).val(notaPadrao).trigger('change');
          }
        });
      }
    });
  });
}
function criaBotaoReplicarNotas(){
  if($j('.nota-matricula-cc').length > 1){
    $j('<button/>').html('Replicar a todos')
                   .attr('type','button')
                   .attr('id','replicar-todas-notas')
                   .addClass('submit')
                   .appendTo($j('<p/>').insertAfter($j('.nota-matricula-cc')
                                       .first()))
                   .unbind();
    $j('#replicar-todas-notas').on('click', function(){
      if(confirm(safeUtf8Decode("Você deseja realmente modificar todos os conceitos desta área de conhecimento?"))){
          var notaPadrao = $j('.nota-matricula-cc').first().val();
          $j('.nota-matricula-cc').each(function(){
            var regra = $j(this).closest('tr').data('regra');
            if(regra.id != regraDiferenciadaId){
              $j(this).val(notaPadrao).trigger('change');
            }
          });
        }
    });
  }
}


(function($) {
  var sR = {
      defaults: {
          slideSpeed: 400,
          easing: false,
          callback: false
      },
      thisCallArgs: {
          slideSpeed: 400,
          easing: false,
          callback: false
      },
      methods: {
          up: function (arg1,arg2,arg3) {
              if(typeof arg1 == 'object') {
                  for(p in arg1) {
                      sR.thisCallArgs.eval(p) = arg1[p];
                  }
              }else if(typeof arg1 != 'undefined' && (typeof arg1 == 'number' || arg1 == 'slow' || arg1 == 'fast')) {
                  sR.thisCallArgs.slideSpeed = arg1;
              }else{
                  sR.thisCallArgs.slideSpeed = sR.defaults.slideSpeed;
              }

              if(typeof arg2 == 'string'){
                  sR.thisCallArgs.easing = arg2;
              }else if(typeof arg2 == 'function'){
                  sR.thisCallArgs.callback = arg2;
              }else if(typeof arg2 == 'undefined') {
                  sR.thisCallArgs.easing = sR.defaults.easing;
              }
              if(typeof arg3 == 'function') {
                  sR.thisCallArgs.callback = arg3;
              }else if(typeof arg3 == 'undefined' && typeof arg2 != 'function'){
                  sR.thisCallArgs.callback = sR.defaults.callback;
              }
              var $cells = $(this).find('td');
              $cells.wrapInner('<div class="slideRowUp" />');
              var currentPadding = $cells.css('padding');
              $cellContentWrappers = $(this).find('.slideRowUp');
              $cellContentWrappers.slideUp(sR.thisCallArgs.slideSpeed,sR.thisCallArgs.easing).parent().animate({
                                                                                                                  paddingTop: '0px',
                                                                                                                  paddingBottom: '0px'},{
                                                                                                                  complete: function () {
                                                                                                                      $(this).children('.slideRowUp').replaceWith($(this).children('.slideRowUp').contents());
                                                                                                                      $(this).parent().css({'display':'none'});
                                                                                                                      $(this).css({'padding': currentPadding});
                                                                                                                  }});
              var wait = setInterval(function () {
                  if($cellContentWrappers.is(':animated') === false) {
                      clearInterval(wait);
                      if(typeof sR.thisCallArgs.callback == 'function') {
                          sR.thisCallArgs.callback.call(this);
                      }
                  }
              }, 100);
              return $(this);
          },
          down: function (arg1,arg2,arg3) {
              if(typeof arg1 == 'object') {
                  for(p in arg1) {
                      sR.thisCallArgs.eval(p) = arg1[p];
                  }
              }else if(typeof arg1 != 'undefined' && (typeof arg1 == 'number' || arg1 == 'slow' || arg1 == 'fast')) {
                  sR.thisCallArgs.slideSpeed = arg1;
              }else{
                  sR.thisCallArgs.slideSpeed = sR.defaults.slideSpeed;
              }

              if(typeof arg2 == 'string'){
                  sR.thisCallArgs.easing = arg2;
              }else if(typeof arg2 == 'function'){
                  sR.thisCallArgs.callback = arg2;
              }else if(typeof arg2 == 'undefined') {
                  sR.thisCallArgs.easing = sR.defaults.easing;
              }
              if(typeof arg3 == 'function') {
                  sR.thisCallArgs.callback = arg3;
              }else if(typeof arg3 == 'undefined' && typeof arg2 != 'function'){
                  sR.thisCallArgs.callback = sR.defaults.callback;
              }
              var $cells = $(this).find('td');
              $cells.wrapInner('<div class="slideRowDown" style="display:none;" />');
              $cellContentWrappers = $cells.find('.slideRowDown');
              $(this).show();
              $cellContentWrappers.slideDown(sR.thisCallArgs.slideSpeed, sR.thisCallArgs.easing, function() { $(this).replaceWith( $(this).contents()); });

              var wait = setInterval(function () {
                  if($cellContentWrappers.is(':animated') === false) {
                      clearInterval(wait);
                      if(typeof sR.thisCallArgs.callback == 'function') {
                          sR.thisCallArgs.callback.call(this);
                      }
                  }
              }, 100);
              return $(this);
          }
      }
  };

  $.fn.slideRow = function(method,arg1,arg2,arg3) {
      if(typeof method != 'undefined') {
          if(sR.methods[method]) {
              return sR.methods[method].apply(this, Array.prototype.slice.call(arguments,1));
          }
      }
  };
})(jQuery);

function handleLockedMessage() {
  handleMessages([{type : 'error', msg : 'Não é permitido realizar esta alteração fora do período de lançamento de notas/faltas.'}]);
}
