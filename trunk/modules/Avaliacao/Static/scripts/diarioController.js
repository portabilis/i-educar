var $j = jQuery.noConflict();

(function($) {

  $(function(){

    var $formFilter = $('#formcadastro');
    var $submitButton = $('#botao_busca');
    var $resultTable = $('#form_resultado .tablelistagem').addClass('horizontal-expand');
    $resultTable.children().remove();

    var diarioUrlBase = 'diario';
    var diarioAjaxUrlBase = 'diarioAjax';

    var $navActions = $('<p />').attr('id', 'nav-actions');
    $navActions.prependTo($formFilter.parent()); 

    var $tableDadosDiario = $('<table />')
                              .attr('id', 'dados-diario')
                              .addClass('styled')
                              .addClass('horizontal-expand')
                              .addClass('center')
                              .hide()
                              .prependTo($formFilter.parent());

    var $feedbackMessages = $('<div />').attr('id', 'feedback-messages').prependTo($formFilter.parent());

    var $tableOrientationSearch = $('<table />')
                                    .attr('id', 'orientation-search')
                                    .appendTo($resultTable.parent());

    var $orientationSearch = $('<p />')
      .html('<strong>Obs:</strong>Caso n&atilde;o esteja sendo listado as op&ccedil;&otilde;es de filtro que voc&ecirc; espera, solicite a(o) secret&aacute;rio(a) da escola que verifique a aloca&ccedil;&atilde;o do seu usu&aacute;rio.')
      .appendTo($('<td />').addClass('center').appendTo($('<tr />').appendTo($tableOrientationSearch)));

    function fixupFieldsWidth(){
      var maxWidth = 0;
      var $fields = $('#formcadastro select');

      //get maxWidh
      $.each($fields, function(index, value){
        $value = $(value);
        if ($value.width() > maxWidth)
          maxWidth = $value.width(); 
      });

      //set maxWidth
      $.each($fields, function(index, value){
        $(value).width(maxWidth);
      });
    };
    fixupFieldsWidth();

    //url builders
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


    function changeResource($resourceElement, postFunction, deleteFunction){
      if ($.trim($resourceElement.val())  == '')
        deleteFunction($resourceElement);
      else
        postFunction($resourceElement);
    };

   
    function setDefaultFaltaIfEmpty(matricula_id){
      var $element = $('#falta-matricula-' + matricula_id);
      console.log('#falta-matricula-' + matricula_id);
      if ($.trim($element.val()) == '')
      {
        $element.val(0);
        $element.change();
      }
    }    


    var changeNota = function(event){
      var $element = $(this);
      setDefaultFaltaIfEmpty($element.data('matricula_id'));
      changeResource($element, postNota, deleteNota);
    };


    var changeNotaExame = function(event){
      var $element = $(this);
      setDefaultFaltaIfEmpty($element.data('matricula_id'));
      changeResource($element, postNotaExame, deleteNotaExame);
    };


    var changeFalta = function(event){
      changeResource($(this), postFalta, deleteFalta);
    };


    var changeParecer = function(event){
      var $element = $(this);
      setDefaultFaltaIfEmpty($element.data('matricula_id'));
      changeResource($element, postParecer, deleteParecer);
    };


    function beforeChangeResource($resourceElement){
      $resourceElement.attr('disabled', 'disabled');
      if ($resourceElement.siblings('img').length < 1);
        $('<img alt="loading..." src="/modules/Avaliacao/Static/images/loading.gif" />').appendTo($resourceElement.parent());
    }


    function afterChangeResource($resourceElement){
      $resourceElement.removeAttr('disabled').siblings('img').remove();
      var resourceElementTabIndex = $resourceElement.attr('tabindex');
      var focusedElementTabIndex = $j('*:focus').first().attr('tabindex');
      var nextTabIndex = resourceElementTabIndex + 1;

      if(focusedElementTabIndex == resourceElementTabIndex)
        $($resourceElement.closest('table').find('[tabindex ="' + nextTabIndex + '"]')).focus();
    }


    function validatesIfValueIsNumberic(value, targetId){
      var isNumeric = $.isNumeric(value);

      if (! isNumeric)
        handleMessages([{type : 'error', msg : 'Informe um numero válido.'}], targetId);

      return isNumeric;
    }  


    function validatesIfNumericValueIsInRange(value, targetId, initialRange, finalRange){

      if (! $.isNumeric(value) || value < initialRange || value > finalRange)
      {
        handleMessages([{type : 'error', msg : 'Informe um valor entre ' + initialRange + ' e ' + finalRange}], targetId);
        return false;
      }

      return true;
    }

    
    function postResource(options, errorCallback, completeCallback){
      $.ajax(options).error(errorCallback).complete(completeCallback);
    }


    function postNota($notaFieldElement){

      $notaFieldElement.val($notaFieldElement.val().replace(',', '.'));

      if (validatesIfValueIsNumberic($notaFieldElement.val(), $notaFieldElement.attr('id')) &&
          validatesIfNumericValueIsInRange($notaFieldElement.val(), $notaFieldElement.attr('id'), 0, 10))
      {
      
        beforeChangeResource($notaFieldElement);

        var options = {
          url : postResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, 'nota', {matricula_id : $notaFieldElement.data('matricula_id'),}),
          dataType : 'json',
          data : {att_value : $notaFieldElement.val()},
          success : function(dataResponse){
            afterChangeResource($notaFieldElement);
            handlePost(dataResponse);
          }
        };

        $notaFieldElement.data('old_value', $notaFieldElement.val());
        postResource(options, handleErrorPost, handleCompletePostNota);
      }
    }


    function postNotaExame($notaExameFieldElement){

      $notaExameFieldElement.val($notaExameFieldElement.val().replace(',', '.'));

      if (validatesIfValueIsNumberic($notaExameFieldElement.val(), $notaExameFieldElement.attr('id')) &&
          validatesIfNumericValueIsInRange($notaExameFieldElement.val(), $notaExameFieldElement.attr('id'), 0, 10))
      {

        beforeChangeResource($notaExameFieldElement);

        var options = {
          url : postResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, 'nota_exame', {matricula_id : $notaExameFieldElement.data('matricula_id'), etapa : 'Rc'}),
          dataType : 'json',
          data : {att_value : $notaExameFieldElement.val()},
          success : function(dataResponse){
            afterChangeResource($notaExameFieldElement);
            handlePost(dataResponse);
          }
        };

        $notaExameFieldElement.data('old_value', $notaExameFieldElement.val());
        postResource(options, handleErrorPost, handleCompletePostNotaExame);
      }
    }


    function postFalta($faltaFieldElement){

      $faltaFieldElement.val($faltaFieldElement.val().replace(',', '.'));
      
      //falta é persistida como inteiro
      if ($.isNumeric($faltaFieldElement.val()))
        $faltaFieldElement.val(parseInt($faltaFieldElement.val()).toString());

      if (validatesIfValueIsNumberic($faltaFieldElement.val(), $faltaFieldElement.attr('id')) &&
          validatesIfNumericValueIsInRange($faltaFieldElement.val(), $faltaFieldElement.attr('id'), 0, 100))
      {

        beforeChangeResource($faltaFieldElement);

        var options = {
          url : postResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, 'falta', {matricula_id : $faltaFieldElement.data('matricula_id'),}),
          dataType : 'json',
          data : {att_value : $faltaFieldElement.val()},
          success : function(dataResponse){
            afterChangeResource($faltaFieldElement);
            handlePost(dataResponse);
          }
        };

        $faltaFieldElement.data('old_value', $faltaFieldElement.val());
        postResource(options, handleErrorPost, handleCompletePostFalta);
      }
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

      $parecerFieldElement.data('old_value', $parecerFieldElement.val());
      postResource(options, handleErrorPost, handleCompletePostParecer);
    }

    
    function confirmDelete(resourceName){
      return confirm('Confirma exclusão ' + resourceName + '?');
    }


    function deleteResource(resourceName, $resourceElement, options, handleCompleteDeleteResource, handleErrorDeleteResource){
      if (confirmDelete(resourceName))
      {
        beforeChangeResource($resourceElement);
        $resourceElement.data('old_value', '');
        $.ajax(options).error(handleErrorDeleteResource).complete(handleCompleteDeleteResource);
      }
      else
      {
        afterChangeResource($resourceElement);
        $resourceElement.val($resourceElement.data('old_value'));
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

      deleteResource(resourceName, $notaFieldElement, options, handleCompleteDeleteResource, handleErrorDeleteResource);
    };


    function deleteNotaExame($notaExameFieldElement){
      var resourceName = 'nota_exame';

      var options = {
        url : deleteResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, resourceName, {matricula_id : $notaExameFieldElement.data('matricula_id'), etapa : 'Rc'}),
        dataType : 'json',
        success : function(dataResponse){
          afterChangeResource($notaExameFieldElement);
          handleDelete(dataResponse);
        }
      };

      deleteResource(resourceName, $notaExameFieldElement, options, handleCompleteDeleteResource, handleErrorDeleteResource);
    };


    function deleteFalta($faltaFieldElement){
        
      //excluir falta se nota, nota exame e parecer (não existirem ou) estiverem sem valor
      var matricula_id = $faltaFieldElement.data('matricula_id');

      var $notaField = $('#nota-matricula-'+matricula_id);
      var $notaExameField = $('#nota-exame-matricula-'+matricula_id);
      var $parecerField = $('#parecer-matricula-'+matricula_id);

      if(($notaField.length < 1 || $notaField.val() == '') &&
         ($notaExameField.length < 1 || $notaExameField.val() == '') &&
         ($parecerField.length < 1 || $.trim($parecerField.val()) == '')
        )
      {      
        var resourceName = 'falta';

        var options = {
          url : deleteResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, resourceName, {matricula_id : $faltaFieldElement.data('matricula_id'),}),
          dataType : 'json',
          success : function(dataResponse){
            afterChangeResource($faltaFieldElement);
            handleDelete(dataResponse);
          }
        };

        deleteResource(resourceName, $faltaFieldElement, options, handleCompleteDeleteResource, handleErrorDeleteResource);
      }
      else
        handleMessages([{type : 'error', msg : 'Falta não pode ser removida após ter lançado notas ou parecer descritivo, tente definir como 0 (zero).'}], $faltaFieldElement.attr('id'));
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

      deleteResource(resourceName, $parecerFieldElement, options, handleCompleteDeleteResource, handleErrorDeleteResource);
    }


    function updateFieldSituacaoMatricula(matricula_id, situacao){
      $('#situacao-matricula-' + matricula_id).html(situacao);
  
      $fieldNotaExame = $('#nota-exame-matricula-'+matricula_id);
      console.log($fieldNotaExame);
      if ($fieldNotaExame.val() != '' || situacao.toLowerCase() == 'em exame')
        $fieldNotaExame.show();
      else if($fieldNotaExame.val() == '')
        $fieldNotaExame.hide();
    }


    //callback handlers

    //delete
    function handleDelete(dataResponse){
      var targetId = dataResponse.att + '-matricula-' + dataResponse.matricula_id;
      handleMessages(dataResponse.msgs, targetId);
      updateFieldSituacaoMatricula(dataResponse.matricula_id, dataResponse.situacao);
    }


    function handleErrorDeleteResource(request){
      console.log('#todo handleErrorDeleteResource');
      console.log(request);
    }


    var handleCompleteDeleteResource = function(){
      console.log('delete completado...')
    };

    //post
    function handleErrorPost(request){
      console.log('#todo handleError');
      console.log(request);
    }


    function handlePost(dataResponse){
      var targetId = dataResponse.att + '-matricula-' + dataResponse.matricula_id;
      handleMessages(dataResponse.msgs, targetId);
      updateFieldSituacaoMatricula(dataResponse.matricula_id, dataResponse.situacao);
    }


    function handleCompletePostNota(){
      console.log('#todo post nota completado...')
    };


    function handleCompletePostNotaExame(){
      console.log('#todo post nota_exame completado...')
    };


    function handleCompletePostFalta(){
      console.log('#todo post falta completado...')
    };


    function handleCompletePostParecer(){
      console.log('#todo post parecer completado...')
    };


    //get
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

      var $linha = $('<tr />').addClass('even');
      $('<td />').html($j('#etapa').children("[selected='selected']").html().toUpperCase()).appendTo($linha);
      $('<td />').html($j('#ref_cod_componente_curricular').children("[selected='selected']").html().toUpperCase()).appendTo($linha);
      $('<td />').html($j('#ref_cod_turma').children("[selected='selected']").html().toUpperCase()).appendTo($linha);
      $('<td />').html($j('#ref_ref_cod_serie').children("[selected='selected']").html().toUpperCase()).appendTo($linha);
      $('<td />').html($j('#ano_escolar').children("[selected='selected']").html()).appendTo($linha);
      $('<td />').html($j('#ref_cod_escola').children("[selected='selected']").html().toUpperCase()).appendTo($linha);
      $('<td />').html(regraAvaliacao.id + ' - ' +regraAvaliacao.nome.toUpperCase()).appendTo($linha);
     
      //corrige acentuação
      var tipoNota = regraAvaliacao.tipo_nota.replace('_', ' ');
      if (tipoNota == 'numerica')
        tipoNota = 'numérica';
      $('<td />').html(tipoNota.toUpperCase()).appendTo($linha);

      $('<td />').html(regraAvaliacao.tipo_presenca.replace('_', ' ').toUpperCase()).appendTo($linha);
      $('<td />').html(regraAvaliacao.tipo_parecer_descritivo.replace('_', ' ').toUpperCase()).appendTo($linha);

      $linha.appendTo($tableDadosDiario);
      $tableDadosDiario.show();
      $tableDadosDiario.data.regra_avaliacao = regraAvaliacao;
    }

    function handleMatriculasSearch(dataResponse) { 

      //link para nova consulta
      var setSearchPage = function(event) {
        $(this).hide();
        $tableDadosDiario.children().remove();
        $resultTable.children().fadeOut('fast').remove();
        $formFilter.fadeIn('fast', function(){
          $(this).show()
        });
        $tableOrientationSearch.show();
      };

      $navActions.html(
        $("<a href='#'>Nova consulta</a>")
        .bind('click', setSearchPage)
        .attr('style', 'text-decoration: underline')
      );

      handleMessages(dataResponse.msgs);

      if(! $.isArray(dataResponse.matriculas))
      {
         $('<td />')
          .html('As matriculas não poderam ser recuperadas, verifique as mensagens de erro ou tente <a alt="Recarregar página" href="/">recarregar</a>.')
          .addClass('center')
          .appendTo($('<tr />').appendTo($resultTable));
      }
      else if (dataResponse.matriculas.length < 1)
      {
         $('<td />')
          .html('Sem matriculas em andamento nesta turma.')
          .addClass('center')
          .appendTo($('<tr />').appendTo($resultTable));
      }
      else
      {

        setTableDadosDiario(dataResponse.regra_avaliacao);
        var useNota = $tableDadosDiario.data.regra_avaliacao.tipo_nota != 'nenhum';
        var useParecer = $tableDadosDiario.data.regra_avaliacao.tipo_parecer_descritivo != 'nenhum';

        //set headers
        var $linha = $('<tr />');
        $('<th />').html('Matricula').appendTo($linha);
        $('<th />').html('Aluno').appendTo($linha);
        $('<th />').html('Situação').appendTo($linha);

        if(useNota)
        {
          $('<th />').html('Nota').appendTo($linha);

          if ($tableDadosDiario.data.regra_avaliacao.quantidade_etapas == $('#etapa').val())
            $('<th />').html('Nota exame').appendTo($linha);
        }

        $('<th />').html('Falta').appendTo($linha);

        if(useParecer)
          $('<th />').html('Parecer').appendTo($linha);
    
        $linha.appendTo($resultTable);

        var nextTabIndex = 1;
        var setNextTabIndex = function($element){
          $element.attr('tabindex', nextTabIndex);
          nextTabIndex += 1;
        };

        //set (result) rows
        $.each(dataResponse.matriculas, function(index, value){

          var $linha = $('<tr />');
          
          $('<td />').html(value.matricula_id).addClass('center').appendTo($linha);
          $('<td />').html(value.aluno_id + ' - ' +value.nome.toUpperCase()).appendTo($linha);
          $('<td />').addClass('situacao-matricula').attr('id', 'situacao-matricula-' + value.matricula_id).data('matricula_id', value.matricula_id).addClass('center').html(value.situacao).appendTo($linha);

          //nota
          var getFieldNota = function(notaAtual, klass, id){
      
            if($tableDadosDiario.data.regra_avaliacao.tipo_nota == 'conceitual')
            {
              var opcoesNotas = $tableDadosDiario.data.regra_avaliacao.opcoes_notas;
              var $notaField = $('<select />').addClass(klass).attr('id', id).data('matricula_id', value.matricula_id);

              //adiciona options
              var $option = $('<option />').appendTo($notaField);
              for(key in opcoesNotas) {
                var $option = $('<option />').val(key).html(opcoesNotas[key]);

                if (notaAtual == key)
                  $option.attr('selected', 'selected');
      
                $option.appendTo($notaField);
              }
            }
            else
            {
              var $notaField = $('<input />').addClass(klass).attr('id', id).val(notaAtual).attr('maxlength', '4').attr('size', '4').data('matricula_id', value.matricula_id);
            }

            $notaField.data('old_value', $notaField.val());
            setNextTabIndex($notaField);
            return $notaField;
          }

          if(useNota) {
            $('<td />').html(getFieldNota(value.nota_atual, 'nota-matricula', 'nota-matricula-' + value.matricula_id)).addClass('center').appendTo($linha);

            if ($tableDadosDiario.data.regra_avaliacao.quantidade_etapas == $('#etapa').val())
            {

              var $fieldNotaExame = getFieldNota(value.nota_exame, 'nota-exame-matricula', 'nota-exame-matricula-' + value.matricula_id);
              $('<td />').html($fieldNotaExame).addClass('center').appendTo($linha);

              if (value.nota_exame == '' && value.situacao.toLowerCase() != 'em exame')
                $fieldNotaExame.hide();
            }
          }
          
          //falta
          var $faltaField = $('<input />').addClass('falta-matricula').attr('id', 'falta-matricula-' + value.matricula_id).val(value.falta_atual).attr('maxlength', '4').attr('size', '4').data('matricula_id', value.matricula_id);
          $faltaField.data('old_value', $faltaField.val());
          setNextTabIndex($faltaField);
          $('<td />').html($faltaField).addClass('center').appendTo($linha);

          //parecer
          if(useParecer) {
            var $parecerField = $('<textarea />').attr('cols', '40').attr('rows', '5').addClass('parecer-matricula').attr('id', 'parecer-matricula-' + value.matricula_id).val(value.parecer_atual).data('matricula_id', value.matricula_id);
            $parecerField.data('old_value', $parecerField.val());
            setNextTabIndex($parecerField);
            $('<td />').addClass('center').html($parecerField).appendTo($linha);
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
        //.on('focusout', function(event){$(this).attr('rows', '2')})
        //.on('focusin', function(event){$(this).attr('rows', '10')});

        $resultTable.addClass('styled').find('input:first').focus();
      }
    }

    //change submit button
    var onClickSearchEvent = function(event){
      if (validatesPresenseOfValueInRequiredFields())
      {
        matriculasSearchOptions.url = getResourceUrlBuilder.buildUrl(diarioAjaxUrlBase, 'matriculas', {matricula_id : $('#ref_cod_matricula').val()});

        if (window.history && window.history.pushState)
          window.history.pushState('', '', getResourceUrlBuilder.buildUrl(diarioUrlBase, 'matriculas'));

        $resultTable.children().fadeOut('fast').remove();
        $tableOrientationSearch.hide();

        //submit form after load a page, to keep session alive
        $.ajax({url: diarioUrlBase, async: false, success: function(data){
          $formFilter.submit();
          $formFilter.fadeOut('fast');
          $navActions
            .html('Aguarde, carregando...')
            .attr('style', 'text-align:center;')
            .unbind('click');
        }});
      }
    };
    $submitButton.val('Carregar');
    $submitButton.attr('onclick', '');
    $submitButton.click(onClickSearchEvent);

    //config form search
    var matriculasSearchOptions = {
      url : '',
      dataType : 'json',
      success : handleMatriculasSearch
    };
    $formFilter.ajaxForm(matriculasSearchOptions);

  });

})(jQuery);
