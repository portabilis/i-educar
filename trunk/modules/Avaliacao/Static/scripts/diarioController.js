var $j = jQuery.noConflict();

(function($) {

  $(function(){

    var diarioUrlBase = 'diario';
    var diarioAjaxUrlBase = 'diarioAjax';

    //global ajax events
    $('.change-visibility-on-ajax-change')
      .ajaxStart(function() { $(this).show(); })
      .ajaxStop(function() { $(this).hide(); });

    $('.change-status-on-ajax-change')
      .ajaxStart(function() { $(this).attr('disabled', 'disabled'); })
      .ajaxStop(function() { $(this).removeAttr('disabled'); });

    var $formFilter = $('#formcadastro');
    var $resultTable = $('#form_resultado .tablelistagem').addClass('styled');
    var $submitButton = $('#botao_busca');

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
        $firstField = $j('#parecer-matricula-4469').closest('tr').next().find('input:first');
        if ($firstField)
          $firstField.focus();
        else
          $j('#parecer-matricula-4469').closest('tr').next().find('textarea:first').focus();
      }
    }


    function postResource(resourceName, $resourceElement, successCallback, errorCallback, completeCallback){

      var options = {
        url : postResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, resourceName, {matricula_id : $resourceElement.data('matricula_id'),}),
        dataType : 'json',
        data : {att_value : $resourceElement.val()},
        success : function(dataResponse){
          afterChangeResource($resourceElement);
          successCallback(dataResponse);
        }
      };

      $.ajax(options).error(errorCallback).complete(completeCallback);
    }


    function postNota($notaFieldElement){
      postResource('nota', $notaFieldElement, handlePost, handleErrorPost, handleCompletePostNota);
    }


    function postFalta($faltaFieldElement){
      postResource('falta', $faltaFieldElement, handlePost, handleErrorPost, handleCompletePostFalta);
    }


    function postParecer($parecerFieldElement){
      postResource('parecer', $parecerFieldElement, handlePost, handleErrorPost, handleCompletePostParecer);
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
          handleDeleteNota(dataResponse);
        }
      };

      deleteResource(resourceName, $notaFieldElement, options, handleErrorDeleteResource);
    };


    function deleteFalta(vars){
      var options = {
        url : '',
        dataType : 'json',
        success : handleDeleteFalta
      };

      deleteResource('falta', options);
    }


    function deleteParecer(vars){
      var options = {
        url : '',
        dataType : 'json',
        success : handleDeleteParecer
      };

      deleteResource('parecer', options);
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
      console.log(dataResponse);
      handleMessages(dataResponse.msgs);
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


    function handleDeleteNota(dataResponse){
      //console.log('#todo handleDeleteNota');
      handleMessages(dataResponse.msgs);
      console.log(dataResponse);
      updateFieldSituacaoMatricula(dataResponse.matricula_id, dataResponse.situacao);
    }


    function handleDeleteFalta(falta){
      console.log('#todo handleDeleteFalta');
    }


    function handleDeleteParecer(parecer){
      console.log('#todo handleDeleteParecer');
    }


    function handleMessages(messages){

      for (var i = 0; i < messages.length; i++){
        console.log('#TODO show messages');
        console.log(messages[i].type + ' - '+ messages[i].msg);
      }
    }

    
    function setTableDadosDiario(dadosDiario){

      var $table = $('#dados-diario').addClass('center');

      $('<caption />').html('<strong>Informações sobre o lançamento de notas</strong>').appendTo($table);

      var $linha = $('<tr />');
      $('<th />').html('Comp. Curricular').appendTo($linha);
      $('<th />').html('Turma').appendTo($linha);
      $('<th />').html('Serie').appendTo($linha);
      $('<th />').html('Ano').appendTo($linha);
      $('<th />').html('Escola').appendTo($linha);
      $('<th />').html('Regra avaliação').appendTo($linha);
      $('<th />').html('Tipo nota').appendTo($linha);
      $('<th />').html('Tipo presença').appendTo($linha);
      $('<th />').html('Tipo parecer').appendTo($linha);
      $linha.appendTo($table);

      var $linha = $('<tr />');
      $('<td />').html($j('#ref_cod_componente_curricular').children("[selected='selected']").html().toLowerCase()).appendTo($linha);
      $('<td />').html($j('#ref_cod_turma').children("[selected='selected']").html().toLowerCase()).appendTo($linha);
      $('<td />').html($j('#ref_ref_cod_serie').children("[selected='selected']").html().toLowerCase()).appendTo($linha);
      $('<td />').html($j('#ano_escolar').children("[selected='selected']").html()).appendTo($linha);
      $('<td />').html($j('#ref_cod_escola').children("[selected='selected']").html().toLowerCase()).appendTo($linha);
      $('<td />').html(dadosDiario.nome).appendTo($linha);
      $('<td />').html(dadosDiario.tipo_nota.replace('_', ' ')).appendTo($linha);
      $('<td />').html(dadosDiario.tipo_presenca.replace('_', ' ')).appendTo($linha);
      $('<td />').html(dadosDiario.tipo_parecer_descritivo.replace('_', ' ')).appendTo($linha);

      $linha.appendTo($table);
      $table.show()
    }


    function handleMatriculasSearch(dataResponse) { 

      console.log(dataResponse);

      $('#nav-actions').html(
        $("<a href='#'>Nova consulta</a>").bind('click', function(){
          $(this).hide();
          $('#dados-diario').children().remove();
          $formFilter.fadeIn('fast', function(){$(this).show()});
          $resultTable.children().fadeOut('fast', function(){$resultTable.children().remove()});
        }).attr('style', 'text-decoration: underline').addClass('button')
      );

      setTableDadosDiario(dataResponse.regra_avaliacao);

      handleMessages(dataResponse.msgs);

      var $linha = $('<tr />');
      $('<th />').html('Matricula').appendTo($linha);
      $('<th />').html('Aluno').appendTo($linha);
      $('<th />').html('Situação').appendTo($linha);
      $('<th />').html('Nota').appendTo($linha);
      $('<th />').html('Falta').appendTo($linha);
      $('<th />').html('Parecer').appendTo($linha);
      $linha.appendTo($resultTable);

      $.each(dataResponse.matriculas, function(index, value){

        var $linha = $('<tr />');
        
        $('<td />').html(value.matricula_id).appendTo($linha);
        $('<td />').html(value.aluno_id + ' - ' +value.nome).appendTo($linha);

        //situacao
        $('<td />').addClass('situacao-matricula').attr('id', 'situacao-matricula-' + value.matricula_id).data('matricula_id', value.matricula_id).html(value.situacao).appendTo($linha);

        //nota
        var $notaField = $('<input />').addClass('nota-matricula').attr('id', 'nota-matricula-' + value.matricula_id).val(value.nota_atual).attr('maxlength', '4').attr('size', '4').data('matricula_id', value.matricula_id);
        $('<td />').html($notaField).appendTo($linha);
        
        //falta
        var $faltaField = $('<input />').addClass('falta-matricula').attr('id', 'falta-matricula-' + value.matricula_id).val(value.falta_atual).attr('maxlength', '4').attr('size', '4').data('matricula_id', value.matricula_id);
        $('<td />').html($faltaField).appendTo($linha);

        //parecer
        var $parecerField = $('<textarea />').attr('cols', '40').attr('rows', '2').addClass('parecer-matricula').attr('id', 'parecer-matricula-' + value.matricula_id).val(value.parecer_atual).data('matricula_id', value.matricula_id);
        $('<td />').html($parecerField).appendTo($linha);

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
        .on('change', changeParecer)
        .on('focusout', function(event){$(this).attr('rows', '2')})
        .on('focusin', function(event){$(this).attr('rows', '10')});

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
        $formFilter.fadeOut('fast', function(){$('#nav-actions').fadeIn('slow').html('Aguarde, carregando...').attr('style', 'text-align:center;').unbind('click');});
      }
    });

    $('<p />').attr('id', 'nav-actions').prependTo($formFilter.parent());
    $('<table />').attr('id', 'dados-diario').addClass('styled').addClass('horizontal-expand').prependTo($formFilter.parent()).hide();
    $formFilter.ajaxForm(matriculasSearchOptions);

  });

})(jQuery);
