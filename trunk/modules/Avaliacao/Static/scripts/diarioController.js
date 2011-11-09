var $j = jQuery.noConflict();

(function($) {

  $(function(){

    var $formFilter = $('#formcadastro');
    var $resultTable = $('#form_resultado .tablelistagem').addClass('styled');
    var $submitButton = $('#botao_busca');

    //initial fixups
    var diarioUrlBase = 'diario';
    var diarioAjaxUrlBase = 'diarioAjax';
    var $tableDadosDiario = $('<table />').attr('id', 'dados-diario').addClass('styled').addClass('horizontal-expand').addClass('center');

    $feedbackMessages = $('<div />').attr('id', 'feedback-messages').prependTo($formFilter.parent());
    $navActions = $('<p />').attr('id', 'nav-actions').prependTo($formFilter.parent()); 
    $tableDadosDiario.prependTo($formFilter.parent()).hide();

    //global ajax events
    $('.change-visibility-on-ajax-change')
      .ajaxStart(function() { $(this).show(); })
      .ajaxStop(function() { $(this).hide(); });
    $('.change-status-on-ajax-change')
      .ajaxStart(function() { $(this).attr('disabled', 'disabled'); })
      .ajaxStop(function() { $(this).removeAttr('disabled'); });

    //ao submeter form carregar matriculas, notas, faltas e pareceres
    //ao mudar nota, falta ou parecer enviar postar, e tratar retorno

    var resourceUrlBuilder = {
      buildUrl : function(urlBase, vars){

        _vars = '';
        for(varName in vars){
          _vars += '&'+varName+'='+vars[varName];
        }

        return urlBase + '?' + _vars;
      }
    };


    var deleteResourceUrlBuilder = {
      buildUrl : function(urlBase, resourceName, additionalVars){

        var vars = {
          att : resourceName,
          oper : 'delete',
          instituicao_id : $j('#ref_cod_instituicao').val(),
          escola_id : $j('#ref_cod_escola').val(),
          curso_id : $j('#ref_cod_curso').val(),
          serie_id : $j('#ref_ref_cod_serie').val(),
          turma_id : $j('#ref_cod_turma').val(),
          ano_escolar : $j('#ano_escolar').val(),
          componente_curricular_id : $j('#ref_cod_componente_curricular').val(),
          etapa : $j('#etapa').val()
        };

        return resourceUrlBuilder.buildUrl(urlBase, $.extend(vars, additionalVars));

      }
    };


    var postResourceUrlBuilder = {
      buildUrl : function(urlBase, resourceName, additionalVars){

        var vars = {
          att : resourceName,
          oper : 'post',
          instituicao_id : $j('#ref_cod_instituicao').val(),
          escola_id : $j('#ref_cod_escola').val(),
          curso_id : $j('#ref_cod_curso').val(),
          serie_id : $j('#ref_ref_cod_serie').val(),
          turma_id : $j('#ref_cod_turma').val(),
          ano_escolar : $j('#ano_escolar').val(),
          componente_curricular_id : $j('#ref_cod_componente_curricular').val(),
          etapa : $j('#etapa').val(),
          matricula_id : $j('#etapa').val()
        };

        return resourceUrlBuilder.buildUrl(urlBase, $.extend(vars, additionalVars));

      }
    };


    var getResourceUrlBuilder = {
      buildUrl : function(urlBase, resourceName, additionalVars){

        var vars = {
          att : resourceName,
          oper : 'get',
          instituicao_id : $j('#ref_cod_instituicao').val(),
          escola_id : $j('#ref_cod_escola').val(),
          curso_id : $j('#ref_cod_curso').val(),
          serie_id : $j('#ref_ref_cod_serie').val(),
          turma_id : $j('#ref_cod_turma').val(),
          ano_escolar : $j('#ano_escolar').val(),
          componente_curricular_id : $j('#ref_cod_componente_curricular').val(),
          etapa : $j('#etapa').val()
        };

        return resourceUrlBuilder.buildUrl(urlBase, $.extend(vars, additionalVars));

      }
    };


    var matriculasSearchOptions = {
      url : '',
      dataType : 'json',
      success : handleMatriculasSearch
    };


    function changeResource($resourceElement, postFunction, deleteFunction){
      beforeChangeResource($resourceElement);

      if ($.trim($resourceElement.val())  == '')
        deleteFunction($resourceElement);
      else
        postFunction($resourceElement);
    };


    var changeNota = function(event){
      changeResource($(this), postNota, deleteNota);
    };


    var changeFalta = function(event){
      changeResource($(this), postFalta, deleteFalta);
    };


    var changeParecer = function(event){
      changeResource($(this), postParecer, deleteParecer);
    };


    function beforeChangeResource($resourceElement){
      $resourceElement.attr('disabled', 'disabled');
      if ($resourceElement.siblings('img').length < 1);
        $('<img alt="loading..." src="/modules/Avaliacao/Static/images/loading.gif" />').appendTo($resourceElement.parent());
    }


    function afterChangeResource($resourceElement){
      $resourceElement.removeAttr('disabled').siblings('img').remove();
      //$resourceElement.siblings().first().focus();

      //set focus in next field or textarea
      if ($resourceElement.attr('class') == 'nota-matricula')
        $('#falta-matricula-' + $resourceElement.data('matricula_id')).focus();
      else if ($resourceElement.attr('class') == 'falta-matricula')
        $('#parecer-matricula-' + $resourceElement.data('matricula_id')).focus();
      else if ($resourceElement.attr('class') == 'parecer-matricula')
      {
        $firstField = $j('#parecer-matricula-' + $resourceElement.data('matricula_id')).closest('tr').next().find('input:first');
        if ($firstField)
          $firstField.focus();
        else
          $j('#parecer-matricula-' + $resourceElement.data('matricula_id')).closest('tr').next().find('textarea:first').focus();
      }
    }


    function postResource(options, errorCallback, completeCallback){
      $.ajax(options).error(errorCallback).complete(completeCallback);
    }


    function postNota($notaFieldElement){

      var options = {
        url : postResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, 'nota', {matricula_id : $notaFieldElement.data('matricula_id'),}),
        dataType : 'json',
        data : {att_value : $notaFieldElement.val()},
        success : function(dataResponse){
          afterChangeResource($notaFieldElement);
          handlePost(dataResponse);
        }
      };

      postResource(options, handleErrorPost, handleCompletePostNota);
    }


    function postFalta($faltaFieldElement){
      var options = {
        url : postResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, 'falta', {matricula_id : $faltaFieldElement.data('matricula_id'),}),
        dataType : 'json',
        data : {att_value : $faltaFieldElement.val()},
        success : function(dataResponse){
          afterChangeResource($faltaFieldElement);
          handlePost(dataResponse);
        }
      };

      postResource(options, handleErrorPost, handleCompletePostFalta);
    }


    function getEtapaParecer(){
      if ($tableDadosDiario.data.regra_avaliacao.tipo_parecer_descritivo.split('_')[0] == 'anual')
        var etapaParecer = 'An';
      else
        var etapaParecer = $j('#etapa').val();

      return etapaParecer;
    }
  

    function postParecer($parecerFieldElement){

      var options = {
        url : postResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, 'parecer', {matricula_id : $parecerFieldElement.data('matricula_id'), etapa : getEtapaParecer()}),
        dataType : 'json',
        data : {att_value : $parecerFieldElement.val()},
        success : function(dataResponse){
          afterChangeResource($parecerFieldElement);
          handlePost(dataResponse);
        }
      };

      console.log(options.url);
      postResource(options, handleErrorPost, handleCompletePostParecer);
    }

    
    function confirmDelete(resourceName){
      return confirm('Confirma exclusão ' + resourceName + '?');
    }


    function deleteResource(resourceName, $resourceElement, options, handleErrorDeleteResource){
      if (confirmDelete(resourceName))
      {
        $.ajax(options).error(handleErrorDeleteResource).complete(function(){console.log('delete completado...')});
      }
      else
      {
        console.log('#todo call getResource url');  
      }
    }


    function deleteNota($notaFieldElement){

      var resourceName = 'nota';

      var options = {
        url : deleteResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, resourceName, {matricula_id : $notaFieldElement.data('matricula_id'),}),
        dataType : 'json',
        success : function(dataResponse){
          afterChangeResource($notaFieldElement);
          handleDelete(dataResponse);
        }
      };

      deleteResource(resourceName, $notaFieldElement, options, handleErrorDeleteResource);
    };


    function deleteFalta($faltaFieldElement){
      var resourceName = 'falta';

      var options = {
        url : deleteResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, resourceName, {matricula_id : $faltaFieldElement.data('matricula_id'),}),
        dataType : 'json',
        success : function(dataResponse){
          afterChangeResource($faltaFieldElement);
          handleDelete(dataResponse);
        }
      };

      deleteResource(resourceName, $faltaFieldElement, options, handleErrorDeleteResource);
    }


    function deleteParecer($parecerFieldElement){
      var resourceName = 'parecer';

      var options = {
        url : deleteResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, resourceName, {matricula_id : $parecerFieldElement.data('matricula_id'), etapa : getEtapaParecer()}),
        dataType : 'json',
        success : function(dataResponse){
          afterChangeResource($parecerFieldElement);
          handleDelete(dataResponse);
        }
      };

      deleteResource(resourceName, $parecerFieldElement, options, handleErrorDeleteResource);
    }


    //update fields functions

    function updateFieldSituacaoMatricula(matricula_id, situacao){
      $('#situacao-matricula-' + matricula_id).html(situacao);
    }


    //callback handlers
    function handleErrorDeleteResource(request){
      console.log('#todo handleErrorDeleteResource');
      console.log(request);
    }

    function handleErrorPost(request){
      console.log('#todo handleError');
      console.log(request);
    }

    function handleGetNota(nota){
      console.log('#todo handleGetNota');
      console.log(nota);
    }


    function handleGetFalta(falta){
      console.log('#todo handleGetFalta');
    }


    function handleGetParecer(parecer){
      console.log('#todo handleGetParecer');
    }


    function handlePost(dataResponse){
      //#TODO pintar campo de verde caso não tenha msgs de erro
      //#TODO pintar campo de vermelho caso tenha msgs de erro

      var targetId = dataResponse.att + '-matricula-' + dataResponse.matricula_id;
      handleMessages(dataResponse.msgs, targetId);
      updateFieldSituacaoMatricula(dataResponse.matricula_id, dataResponse.situacao);
    }


    function handleCompletePostNota(){
      console.log('#todo post nota completado...')
    };



    function handleCompletePostFalta(){
      console.log('#todo post falta completado...')
    };


    function handleCompletePostParecer(){
      console.log('#todo post parecer completado...')
    };


    function handleDelete(dataResponse){
      var targetId = dataResponse.att + '-matricula-' + dataResponse.matricula_id;
      handleMessages(dataResponse.msgs, targetId);
      updateFieldSituacaoMatricula(dataResponse.matricula_id, dataResponse.situacao);
    }

    function handleMessages(messages, targetId){

      var hasErrorMessages = false;
      var hasSuccessMessages = false;

      for (var i = 0; i < messages.length; i++){

        if (messages[i].type != 'error')
          var delay = 10000;
        else
          var delay = 60000;

        $('<p />').addClass(messages[i].type).html(messages[i].msg).appendTo($feedbackMessages).delay(delay).fadeOut(function(){/*$('#'+$(this).data('target_id')).removeClass('success');*/ $(this).remove()}).data('target_id', targetId);

        if (! hasErrorMessages && messages[i].type == 'error')
          hasErrorMessages = true;
        else if(! hasSuccessMessages && messages[i].type == 'success')
          hasSuccessMessages = true;
      }

      if (targetId && hasErrorMessages)
        $('#'+targetId).addClass('error').removeClass('success');
      else if(targetId && hasSuccessMessages)
        $('#'+targetId).addClass('success').removeClass('error');
      else
        $('#'+targetId).removeClass('success').removeClass('error');
    }

    
    function setTableDadosDiario(regraAvaliacao){
      $('<caption />').html('<strong>Informações sobre o lançamento de notas</strong>').appendTo($tableDadosDiario);

      //set headers table
      var $linha = $('<tr />');
      $('<th />').html('Etapa').appendTo($linha);
      $('<th />').html('Comp. Curricular').appendTo($linha);
      $('<th />').html('Turma').appendTo($linha);
      $('<th />').html('Serie').appendTo($linha);
      $('<th />').html('Ano').appendTo($linha);
      $('<th />').html('Escola').appendTo($linha);
      $('<th />').html('Regra avaliação').appendTo($linha);
      $('<th />').html('Tipo nota').appendTo($linha);
      $('<th />').html('Tipo presença').appendTo($linha);
      $('<th />').html('Tipo parecer').appendTo($linha);
      $linha.appendTo($tableDadosDiario);

      var $linha = $('<tr />');
      $('<td />').html($j('#etapa').children("[selected='selected']").html().toLowerCase()).appendTo($linha);
      $('<td />').html($j('#ref_cod_componente_curricular').children("[selected='selected']").html().toLowerCase()).appendTo($linha);
      $('<td />').html($j('#ref_cod_turma').children("[selected='selected']").html().toLowerCase()).appendTo($linha);
      $('<td />').html($j('#ref_ref_cod_serie').children("[selected='selected']").html().toLowerCase()).appendTo($linha);
      $('<td />').html($j('#ano_escolar').children("[selected='selected']").html()).appendTo($linha);
      $('<td />').html($j('#ref_cod_escola').children("[selected='selected']").html().toLowerCase()).appendTo($linha);
      $('<td />').html(regraAvaliacao.id + ' - ' +regraAvaliacao.nome).appendTo($linha);
      $('<td />').html(regraAvaliacao.tipo_nota.replace('_', ' ')).appendTo($linha);
      $('<td />').html(regraAvaliacao.tipo_presenca.replace('_', ' ')).appendTo($linha);
      $('<td />').html(regraAvaliacao.tipo_parecer_descritivo.replace('_', ' ')).appendTo($linha);

      $linha.appendTo($tableDadosDiario);
      $tableDadosDiario.show();

      $tableDadosDiario.data.regra_avaliacao = regraAvaliacao;
    }


    function handleMatriculasSearch(dataResponse) { 

      //todo move to a function ?
      $navActions.html(
        $("<a href='#'>Nova consulta</a>").bind('click', function(){
          $(this).hide();
          $tableDadosDiario.children().remove();
          $formFilter.fadeIn('fast', function(){$(this).show()});
          $resultTable.children().fadeOut('fast', function(){$resultTable.children().remove()});
        }).attr('style', 'text-decoration: underline').addClass('button')
      );

      setTableDadosDiario(dataResponse.regra_avaliacao);
      var useNota = $tableDadosDiario.data.regra_avaliacao.tipo_nota != 'nenhum';
      var useParecer = $tableDadosDiario.data.regra_avaliacao.tipo_parecer_descritivo != 'nenhum';

      handleMessages(dataResponse.msgs);

      //set headers table
      var $linha = $('<tr />');
      $('<th />').html('Matricula').appendTo($linha);
      $('<th />').html('Aluno').appendTo($linha);
      $('<th />').html('Situação').appendTo($linha);

      if(useNota)
        $('<th />').html('Nota').appendTo($linha);

      $('<th />').html('Falta').appendTo($linha);

      if(useParecer)
        $('<th />').html('Parecer').appendTo($linha);
  
      $linha.appendTo($resultTable);

      $.each(dataResponse.matriculas, function(index, value){

        var $linha = $('<tr />');
        
        $('<td />').html(value.matricula_id).addClass('center').appendTo($linha);
        $('<td />').html(value.aluno_id + ' - ' +value.nome).appendTo($linha);

        //situacao
        $('<td />').addClass('situacao-matricula').attr('id', 'situacao-matricula-' + value.matricula_id).data('matricula_id', value.matricula_id).addClass('center').html(value.situacao).appendTo($linha);

        //nota
        if(useNota) {

          if($tableDadosDiario.data.regra_avaliacao.tipo_nota == 'conceitual')
          {

            var opcoesNotas = $tableDadosDiario.data.regra_avaliacao.opcoes_notas;
            var $notaField = $('<select />').addClass('nota-matricula').attr('id', 'nota-matricula-' + value.matricula_id).data('matricula_id', value.matricula_id);

            //adiciona options
            var $option = $('<option />').appendTo($notaField);
            for(key in opcoesNotas) {
              var $option = $('<option />').val(key).html(opcoesNotas[key]);

              if (value.nota_atual == key)
                $option.attr('selected', 'selected');
    
              $option.appendTo($notaField);
            }
          }
          else
          {
            var $notaField = $('<input />').addClass('nota-matricula').attr('id', 'nota-matricula-' + value.matricula_id).val(value.nota_atual).attr('maxlength', '4').attr('size', '4').data('matricula_id', value.matricula_id);
          }
          $('<td />').html($notaField).addClass('center').appendTo($linha);
        }
        
        //falta
        var $faltaField = $('<input />').addClass('falta-matricula').attr('id', 'falta-matricula-' + value.matricula_id).val(value.falta_atual).attr('maxlength', '4').attr('size', '4').data('matricula_id', value.matricula_id);
        $('<td />').html($faltaField).addClass('center').appendTo($linha);

        //parecer
        if(useParecer) {
          var $parecerField = $('<textarea />').attr('cols', '40').attr('rows', '5').addClass('parecer-matricula').attr('id', 'parecer-matricula-' + value.matricula_id).val(value.parecer_atual).data('matricula_id', value.matricula_id);
          $('<td />').addClass('center').html($parecerField).appendTo($linha);
        }

        $linha.fadeIn('slow').appendTo($resultTable);
      });
      $resultTable.find('tr:even').addClass('even');

      //events
      var $notaFields = $resultTable.find('.nota-matricula');
      var $faltaFields = $resultTable.find('.falta-matricula');
      var $parecerFields = $resultTable.find('.parecer-matricula');

      $notaFields.on('change', changeNota);
      $faltaFields.on('change', changeFalta);
      $parecerFields
        .on('change', changeParecer);
        //.on('focusout', function(event){$(this).attr('rows', '2')})
        //.on('focusin', function(event){$(this).attr('rows', '10')});

      //#FIXME not working $notaFields.on('keypress', useTabOnPressEnter);

      $resultTable.find('input:first').focus();
    }

    $submitButton.val('Carregar');
    $submitButton.addClass('change-status-on-ajax-change');
    $submitButton.attr('onclick', '');
    $submitButton.click(function(event){
      if (validatesPresenseOfValueInRequiredFields())
      {
        matriculasSearchOptions.url = getResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, 'matriculas');

        if (window.history && window.history.pushState)
          window.history.pushState('', '', getResourceUrlBuilder.buildUrl(diarioUrlBase, 'matriculas'));

        $resultTable.children().fadeOut('fast').remove();
        $formFilter.submit();
        $formFilter.fadeOut('fast', function(){$navActions.fadeIn('slow').html('Aguarde, carregando...').attr('style', 'text-align:center;').unbind('click');});
      }
    });
    $formFilter.ajaxForm(matriculasSearchOptions);

  });

})(jQuery);
