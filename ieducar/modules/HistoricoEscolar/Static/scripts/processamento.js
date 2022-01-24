(function($){

  $(function(){
    $("tr#tr_alunos_dependencia td:first-child").width("455px");
    var $formFilter = $('#formcadastro');
    var $submitButton = $('#botao_busca');
    var $resultTable = $('#form_resultado .tablelistagem').addClass('horizontal-expand');
    $resultTable.children().remove();

    $('<div />').attr('id', 'first-bar-action')
                .attr('class', 'bar-action hide-on-search')
                .prependTo($resultTable.parent());

    $('<div />').attr('id', 'second-bar-action')
                .attr('class', 'bar-action hide-on-search')
                .appendTo($resultTable.parent());

    var $barActions = $('.bar-action').hide();

    $('<input class="selecionar disable-on-apply-changes" type="button" value="Selecionar todos" />').appendTo($barActions);
    var $selectAllButton = $barActions.find('input.selecionar');

    $('<input class="processar disable-on-apply-changes" type="button" value="Processar" />').appendTo($barActions);
    var $actionButton = $barActions.find('input.processar');

    $('<input class="destroy disable-on-apply-changes" type="button" value="Remover" />').appendTo($barActions);
    var $destroyButton = $barActions.find('input.destroy');

    var PageUrlBase = 'processamento';
    var ApiUrlBase = 'processamentoApi';

    var $resourceOptionsTable = $('#resource-options');
    $resourceOptionsTable.find('tr:even').addClass('even');
    $resourceOptionsTable.hide().prependTo($formFilter.parent());

    var $disciplinasManualTable = $('#disciplinas-manual');
    $('#new-disciplina-line').click(function(){
      var $lastDisplinaRow = $disciplinasManualTable.find('tr.disciplina:last');
      var $newRow = $lastDisplinaRow.clone().removeClass('notice').insertAfter($lastDisplinaRow);
      var $fieldNome = $newRow.find('input.nome');
      resetAutoCompleteNomeDisciplinaEvent($fieldNome.val(''));
      $fieldNome.focus();
      setRemoveDisciplinaLineEvent($newRow.find('.remove-disciplina-line'));
    });

    function resetAutoCompleteNomeDisciplinaEvent($element){
      var handleSelect = function(event, ui){
        $(event.target).val(ui.item.label);
        return false;
      };

      var search = function(request, response) {
        var searchPath = '/module/Api/ComponenteCurricular?oper=get&resource=componente_curricular-search';
        var params     = { query : request.term };

        $.get(searchPath, params, function(dataResponse) {
          simpleSearch.handleSearch(dataResponse, response);
        });
      };

      $element.autocomplete({
        source    : search,
        select    : handleSelect,
        minLength : 1,
        autoFocus : true
      });
    }

    function setRemoveDisciplinaLineEvent($targetElement){
      $targetElement.click(function(event){
        event.preventDefault();
        if($disciplinasManualTable.find('tr.disciplina').length > 1)
          $(this).closest('tr').remove();
        else
          handleMessages([{type : 'notice', msg : 'Não é possivel remover a primeira linha.'}], $(this).closest('tr'));
      });
    }
    setRemoveDisciplinaLineEvent($('.remove-disciplina-line'));

    var $notasField = $resourceOptionsTable.find('#notas');
    $notasField.change(function(){
      var $targetElementId = '#notas-manual';

      if ($notasField.val() == 'informar-manualmente')
        $($targetElementId).show().removeAttr('disabled');
      else if($notasField.val() == 'AP')
        $($targetElementId).show().removeAttr('disabled').val('AP');
      else
        $($targetElementId).hide().attr('disabled', 'disabled');
    });

    var changeStateFieldManual = function($containerElementId, $targetElementId){
      $targetElement = $($targetElementId);

      if ($($containerElementId).val() == 'informar-manualmente'){
        $targetElement.show().removeAttr('disabled').find('.change-state-with-parent').show().removeAttr('disabled');
      }
      else{
        $targetElement.hide().attr('disabled', 'disabled').find('.change-state-with-parent').hide().attr('disabled', 'disabled');
      }
    };

    $resourceOptionsTable.find('#percentual-frequencia').change(function(){
      changeStateFieldManual('#percentual-frequencia', '#percentual-frequencia-manual');
    });

    $resourceOptionsTable.find('#faltas').change(function(){
      changeStateFieldManual('#faltas', '#faltas-manual');
    });

    $resourceOptionsTable.find('#disciplinas').change(function(){
      changeStateFieldManual('#disciplinas', '#disciplinas-manual');

      /*chama .change para respectivos elementos esconderem / mostrar os campos que
        dependam deles*/
      if ($(this).val() == 'informar-manualmente'){
        $('.disable-and-hide-wen-disciplinas-manual').hide().attr('disabled', 'disabled').change();
        $('#disciplinas-manual').find('input.nome').focus();
      }
      else
        $('.disable-and-hide-wen-disciplinas-manual').show().removeAttr('disabled').change();

    });

    $('.disable-on-search').attr('disabled', 'disabled');
    $('.hide-on-search').hide();

    $('#ref_cod_curso').change(function(){
      $('.clear-on-change-curso').val('');
    });

    var $navActions = $('<p />').attr('id', 'nav-actions').hide();
    $navActions.prependTo($formFilter.parent());

    var $tableSearchDetails = $('<table />')
                              .attr('id', 'search-details')
                              .addClass('styled')
                              .addClass('horizontal-expand')
                              .addClass('left')
                              .hide()
                              .prependTo($formFilter.parent()).hide();

    var $feedbackMessages = $('<div />').attr('id', 'feedback-messages').appendTo($formFilter.parent());

    var $additionalFields = $('#resource-options select, #resource-options input[type="text"]');
    fixupFieldsWidth($additionalFields);

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
          instituicao_id : $('#ref_cod_instituicao').val(),
          matricula_id : ''
        };

        return resourceUrlBuilder.buildUrl(urlBase, $.extend(vars, additionalVars));
      }
    };


    var postResourceUrlBuilder = {
      buildUrl : function(urlBase, resourceName, additionalVars){

        var vars = {
          att : resourceName,
          oper : 'post',
          instituicao_id : $('#ref_cod_instituicao').val(),
          matricula_id : ''
        };

        return resourceUrlBuilder.buildUrl(urlBase, $.extend(vars, additionalVars));
      }
    };


    var getResourceUrlBuilder = {
      buildUrl : function(urlBase, resourceName, additionalVars){

        var vars = {
          att : resourceName,
          oper : 'get',
          instituicao_id : $('#ref_cod_instituicao').val(),
          escola_id : $('#ref_cod_escola').val(),
          curso_id : $('#ref_cod_curso').val(),
          serie_id : $('#ref_cod_serie').val(),
          turma_id : $('#ref_cod_turma').val(),
          ano : $('#ano').val(),
          etapa : $('#etapa').val(),
          dependencia : document.getElementById("alunos_dependencia").checked ? 't' : 'f'
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

    var changeResourceName = function(event){
      changeResource($(this), postFalta, deleteFalta);
    };


    function postResource(options, errorCallback){
      $.ajax(options).error(errorCallback);
    }


    function updateFieldSituacao(linkToHistorico, matricula_id, situacao){
      if(situacao){
        var $fieldSituacao = $('#situacao-matricula-' + matricula_id);
        var situacaoHistorico = safeUtf8Decode(situacao);

        $fieldSituacao.html(getLinkToHistorico(linkToHistorico, situacaoHistorico));
        $fieldSituacao.data('situacao_historico', situacaoHistorico);
      }
    }


    //callback handlers

    //delete
    function handleDelete(dataResponse){
      try{
        var $checkbox = $('matricula-' + dataResponse.matricula_id);
        var $targetElement = $('#matricula-'+dataResponse.matricula_id).closest('tr').first();
        handleMessages(dataResponse.msgs, $targetElement);
        updateFieldSituacao(dataResponse.link_to_historico, dataResponse.matricula_id, dataResponse.situacao_historico);
      }
      catch(error){
        showNewSearchButton();
        handleMessages([{type : 'error', msg : 'Ocorreu um erro ao remover o recurso, por favor tente novamente, detalhes: ' + error}], '');

        safeLog(dataResponse);
      }
    }


    function handleErrorDeleteResource(response){
      handleMessages([{type : 'error', msg : 'Erro ao alterar recurso, detalhes:' + response.responseText}], '');
      safeLog(response);
    }

    function handleErrorPost(response){
      handleMessages([{type : 'error', msg : 'Erro ao alterar recurso, detalhes:' + response.responseText}], '');
      safeLog(response);
    }


    function setTableSearchDetails(dataDetails){
      $('<caption />').html(safeUtf8Decode('<strong>Processamento dos históricos</strong>')).appendTo($tableSearchDetails);

      //set headers table
      var $linha = $('<tr />');
      $('<th />').html('Ano').appendTo($linha);
      $('<th />').html('Escola').appendTo($linha);
      $('<th />').html('Curso').appendTo($linha);
      $('<th />').html('Série').appendTo($linha);
      $('<th />').html('Turma').appendTo($linha);
      $('<th />').html('Matrícula').appendTo($linha);

      $linha.appendTo($tableSearchDetails);

      var $linha = $('<tr />').addClass('even');

      $('<td />').html($('#ano').val()).appendTo($linha);

      //field escola pode ser diferente de select caso usuario comum
      var $htmlEscolaField = $('#ref_cod_escola').children("[selected='selected']").html() ||
                             $('#tr_nm_escola span:last').html();
      $('<td />').html(safeToUpperCase($htmlEscolaField)).appendTo($linha);

      $('<td />').html(safeToUpperCase($('#ref_cod_curso').children("[value!=''][selected='selected']").html()  || 'Todos')).appendTo($linha);
      $('<td />').html(safeToUpperCase($('#ref_cod_serie').children("[value!=''][selected='selected']").html()  || 'Todas')).appendTo($linha);
      $('<td />').html(safeToUpperCase($('#ref_cod_turma').children("[value!=''][selected='selected']").html()  || 'Todas')).appendTo($linha);
      $('<td />').html(safeToUpperCase($('#ref_cod_matricula').children("[value!=''][selected='selected']").html() || 'Todas')).appendTo($linha);

      $linha.appendTo($tableSearchDetails);
      $tableSearchDetails.show();

      $tableSearchDetails.data('details', dataDetails);
    }

    //exibe formulário nova consulta
    function showSearchForm(event){
      $navActions.html('').hide();
      $tableSearchDetails.hide().children().remove();
      $resultTable.children().fadeOut('fast').remove();
      $formFilter.fadeIn('fast', function(){
        $(this).show()
      });
      $('.disable-on-search').attr('disabled', 'disabled');
      $('.hide-on-search').hide();
      $('.disable-on-apply-changes').removeAttr('disabled');
      $actionButton.val('Processar');
    }


    function showNewSearchButton(){
      $navActions.html(
        $("<a href='#'>Nova consulta</a>")
        .bind('click', showSearchForm)
        .attr('style', 'text-decoration: underline')
      );
      $('.disable-on-search').removeAttr('disabled');
      $('.hide-on-search').show();
    }

    function getLinkToHistorico(link, text){
      if (link)
        return $('<a target="__blank" style="text-decoration:underline;" href='+link+'>'+text+'</a>');
      else
        return text;
    }


    function handleMatriculasSearch(dataResponse){

      showNewSearchButton();

      try{
        handleMessages(dataResponse.msgs);

        if(! $.isArray(dataResponse.matriculas))
        {
           $('<td />')
            .html('As matriculas n&#227;o poderam ser recuperadas, verifique as mensagens de erro ou tente <a alt="Recarregar página" href="/" style="text-decoration:underline">recarregar</a>.')
            .addClass('left')
            .appendTo($('<tr />').appendTo($resultTable));
        }
        else if (dataResponse.matriculas.length < 1)
        {
           $('<td />')
            .html('Sem matriculas em andamento nesta turma.')
            .addClass('left')
            .appendTo($('<tr />').appendTo($resultTable));
        }
        else
        {
          setTableSearchDetails();
          //set headers
          var $linha = $('<tr />');
          $('<th />').html('Selecionar').appendTo($linha);
          $('<th />').html('Curso').appendTo($linha);
          $('<th />').html(safeUtf8Decode('Série')).appendTo($linha);
          $('<th />').html('Turma').appendTo($linha);
          $('<th />').html('Matrícula').appendTo($linha);
          $('<th />').html('Aluno').appendTo($linha);
          $('<th />').html('Situa&#231;&#227;o').appendTo($linha);
          $linha.appendTo($resultTable);

          //set rows
          $.each(dataResponse.matriculas, function(index, value){

            var $checkbox = $('<input />')
                            .attr('type', 'checkbox')
                            .attr('name', 'processar-matricula')
                            .attr('value', 'sim')
                            .attr('id', 'matricula-' + value.matricula_id)
                            .attr('class', 'matricula disable-on-apply-changes')
                            .data('matricula_id', value.matricula_id);

            var $linha = $('<tr />');
            $('<td />').html($checkbox).addClass('left').appendTo($linha);
            $('<td />').html(value.nome_curso).addClass('left').appendTo($linha);
            $('<td />').html(safeUtf8Decode(value.nome_serie)).addClass('left').appendTo($linha);
            $('<td />').html(safeUtf8Decode(value.nome_turma)).addClass('left').appendTo($linha);
            $('<td />').html(value.matricula_id).addClass('left').appendTo($linha);
            $('<td />').html(value.aluno_id + " - " + safeToUpperCase(value.nome)).appendTo($linha);

            var situacaoHistorico = safeUtf8Decode(value.situacao_historico);
            var $htmlSituacao = getLinkToHistorico(value.link_to_historico, situacaoHistorico);
            $('<td />').html($htmlSituacao).data('situacao_historico', situacaoHistorico).attr('id', 'situacao-matricula-' + value.matricula_id).addClass('situacao').addClass('left').appendTo($linha);

            $linha.fadeIn('slow').appendTo($resultTable);
          });//fim each matriculas

          $resultTable.find('tr:even').addClass('even');
          $resultTable.addClass('styled').find('checkbox:first').focus();

          var $observacaoField = $('#observacao');
          if($.trim($observacaoField.val()) == '' || ($observacaoField.val() == $observacaoField.data('old_value'))){
            $observacaoField.val(dataResponse.observacao_padrao);
            $observacaoField.data('old_value', dataResponse.observacao_padrao);
          }
        }
      }
      catch(error){
        showNewSearchButton();

        handleMessages([{type : 'error', msg : 'Ocorreu um erro ao exibir as matriculas, por favor tente novamente, detalhes: ' + error}], '');

        safeLog(dataResponse);
      }
    }

    function handleErrorMatriculasSearch(response){
      showNewSearchButton();

      handleMessages([{type : 'error', msg : 'Ocorreu um erro ao carregar as matriculas, por favor tente novamente, detalhes:' + response.responseText}], '');

      safeLog(response);
    }

    //change submit button
    var onClickSearchEvent = function(event){

      if (validatesPresenseOfValueInRequiredFields())
      {
        matriculasSearchOptions.url = getResourceUrlBuilder.buildUrl(ApiUrlBase, 'matriculas', {matricula_id : $('#ref_cod_matricula').val()});

        if (window.history && window.history.pushState)
          window.history.pushState('', '', getResourceUrlBuilder.buildUrl(PageUrlBase, 'matriculas'));

        $resultTable.children().fadeOut('fast').remove();

        $formFilter.submit();
        $formFilter.fadeOut('fast');
        $navActions
          .html('Aguarde, carregando...')
          .attr('style', 'text-align:center;')
          .unbind('click');

        resetAutoCompleteNomeDisciplinaEvent($disciplinasManualTable.find('input.nome'));
      }
      updateAreaConhecimento();
    };
    $submitButton.val('Carregar');
    $submitButton.attr('onclick', '');
    $submitButton.click(onClickSearchEvent);

    //config form search
    var matriculasSearchOptions = {
      url : '',
      dataType : 'json',
      success : handleMatriculasSearch,
      error : handleErrorMatriculasSearch
    };

    $formFilter.ajaxForm(matriculasSearchOptions);

    var onClickActionEvent = function(event){

      var $firstChecked = $('input.matricula:checked:first');

      if ($firstChecked.length < 1)
        handleMessages([{type : 'error', msg : 'Selecione alguma matrícula.'}], $actionButton, true);
      else {
        var additionalFields = [
          $('#percentual-frequencia-manual').get(0),
          $('#notas-manual').get(0),
          $('#faltas-manual').get(0)
        ];

        $.each($('#disciplinas-manual').find('.obrigatorio'), function(index, requiredElement){
          additionalFields.push(requiredElement);
        });

        if (validatesPresenseOfValueInRequiredFields(additionalFields)){

          var isValid = validatesIfValueIsNumeric($('#dias-letivos').val(), 'dias-letivos');

          if (isValid && $('#percentual-frequencia').val() != 'buscar-boletim')
            isValid = validatesIfNumericValueIsInRange($('#percentual-frequencia-manual').val(), '#percentual-frequencia-manual', 0, 100);

          var $faltas = $('#faltas');
          if (isValid && $faltas.val() != 'buscar-boletim' && $faltas.is(':visible'))
            isValid = validatesIfNumericValueIsInRange($('#faltas-manual').val(), '#faltas-manual', 0, 999);

          if (isValid && $('#disciplinas').val() != 'buscar-boletim'){
            $.each($('#disciplinas-manual').find('.falta'), function(index, field){
              $field = $(field);
              isValid = $.trim($field.val()) == '' || validatesIfNumericValueIsInRange($field.val(), $field, 0, 999);
            });
          }

          if (isValid){
            $('.disable-on-apply-changes').attr('disabled', 'disabled');
            $actionButton.val('Aguarde processando...');
            postProcessamento($firstChecked);
          }
        }
      }
    };

    function getDisciplinasManuais(){
      var disciplinas = [];
      $.each($('#disciplinas-manual').find('.disciplina'), function(index, disciplina){
        var $disciplina = $(disciplina);

        disciplinas.push({
          nome : $disciplina.find('.nome').val(),
          nota : $disciplina.find('.nota').val(),
          falta : $disciplina.find('.falta').val()
        });
      });
      return disciplinas;
    }

    function postProcessamento($resourceElement){

      var percentualFrequencia = $('#percentual-frequencia').val() == 'buscar-boletim' ? 'buscar-boletim' : $('#percentual-frequencia-manual').val();
      var faltas = $('#faltas').val() == 'buscar-boletim' ? 'buscar-boletim' : $('#faltas-manual').val();
      var notas = $('#notas').val() == 'buscar-boletim' ? 'buscar-boletim' : $('#notas-manual').val();
      var disciplinas = $('#disciplinas').val() == 'buscar-boletim' ? 'buscar-boletim' : getDisciplinasManuais();

      var options = {
        url : postResourceUrlBuilder.buildUrl(ApiUrlBase, 'processamento', {
          matricula_id : $resourceElement.data('matricula_id')
        }),
        dataType : 'json',
        data : {
          dias_letivos : $('#dias-letivos').val(),
          situacao : $('#situacao').val(),
          extra_curricular : $('#extra-curricular').is(':checked') ? 1 : 0,
          media_area_conhecimento : $('#media-area-conhecimento').is(':checked') ? 1 : 0,
          processar_media_geral : $('#processar-media-geral').is(':checked') ? 1 : 0,
          grade_curso_id : $('#grade-curso').val(),
          percentual_frequencia : percentualFrequencia,
          notas : notas,
          faltas : faltas,
          observacao : $('#observacao').val(),
          registro : $('#registro').val(),
          livro : $('#livro').val(),
          folha : $('#folha').val(),
          disciplinas : disciplinas,
          turma_id : $('#ref_cod_turma').val(),
          dependencia : document.getElementById("alunos_dependencia").checked ? 't' : 'f',
          posicao : $('#posicao').val(),
          area_conhecimento : $('#area-conhecimento').val(),
          emitir_carga_disciplinas : document.getElementById("emitir_carga_disciplinas").checked ? 1 : 0
        },
        success : function(dataResponse){
          afterChangeResource($resourceElement, postProcessamento);
          handlePostProcessamento(dataResponse);
        }
      };

      beforeChangeResource($resourceElement);
      postResource(options, handleErrorPost);
    }

    function deleteHistorico($resourceElement){
      var options = {
        url : deleteResourceUrlBuilder.buildUrl(ApiUrlBase, 'historico', {
          matricula_id : $resourceElement.data('matricula_id')
        }),
        dataType : 'json',
        data : {
        },
        success : function(dataResponse){
          afterChangeResource($resourceElement, deleteHistorico);
          handlePostProcessamento(dataResponse);
        }
      };

      beforeChangeResource($resourceElement);
      deleteResource(options, handleErrorDeleteResource);
    }

    function deleteResource(options, errorCallback){
      $.ajax(options).error(errorCallback);
    }

    function beforeChangeResource($resourceElement){
      if ($resourceElement.siblings('img').length < 1);
        $('<img alt="loading..." src="/modules/HistoricoEscolar/Static/images/loading.gif" />').appendTo($resourceElement.parent());
    }

    function handlePostProcessamento(dataResponse){
      try{
        var $checkbox = $('matricula-' + dataResponse.matricula_id);
        var $targetElement = $('#matricula-'+dataResponse.matricula_id).closest('tr').first();
        handleMessages(dataResponse.msgs, $targetElement);
        updateFieldSituacao(dataResponse.link_to_historico, dataResponse.matricula_id, dataResponse.situacao_historico);
      }
      catch(error){
        showNewSearchButton();
        handleMessages([{type : 'error', msg : 'Ocorreu um erro ao enviar o processamento, por favor tente novamente, detalhes: ' + error}], '');

        safeLog(dataResponse);
      }
    }


    function afterChangeResource($resourceElement, callbackContinueNextChange){
      $resourceElement.siblings('img').remove();
      $resourceElement.attr('checked', false);

      //verifica se chegou na ultima matricula e ativa os elements desativados
      var $firstChecked = $('input.matricula:checked:first');
      if ($firstChecked.length < 1){
        $('.disable-on-apply-changes').removeAttr('disabled');
        $actionButton.val('Processar');
        window.setTimeout(function(){alert(safeUtf8Decode('Operação finalizada.'));}, 1);
      }
      else if (typeof(callbackContinueNextChange) == 'function')
        callbackContinueNextChange($firstChecked);
    }

    var onClickSelectAllEvent = function(event){
      var $checked = $('input.matricula:checked');
      var $unchecked = $('input.matricula:not(:checked)');

      $checked.attr('checked', false);
      $unchecked.attr('checked', true);
    };

    var onClickDestroyEvent = function(event){

      var $firstChecked = $('input.matricula:checked:first');

      if ($firstChecked.length < 1)
        handleMessages([{type : 'error', msg : 'Selecione alguma matrícula.'}], $actionButton, true);
      else{

        if (confirm("Confirma remo\u00e7\u00e3o dos hist\u00f3ricos selecionados?")){

          $.each($('input.matricula:checked').closest('tr').find('.situacao'), function(indice, fieldSituacao){
            var $fieldSituacao = $(fieldSituacao);
            if ($fieldSituacao.data('situacao_historico') != 'Processado')
              $fieldSituacao.closest('tr').find('input.matricula').attr('checked', false);
          });

          $('.disable-on-apply-changes').attr('disabled', 'disabled');
          $actionButton.val('Aguarde removendo...');
          deleteHistorico($firstChecked);
        }
      }
    };

    $actionButton.click(onClickActionEvent);
    $selectAllButton.click(onClickSelectAllEvent);
    $destroyButton.click(onClickDestroyEvent)

    $('#tableLocalizacao').prependTo($formFilter.parent());

  });

  $("#alunos_dependencia").click(function(){
    if ((this).checked) {
      $('.tr_posicao').hide();
    } else{
      $('.tr_posicao').show();
    }
  });

    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

    function getAreaConhecimento(response) {
      var areaConhecimentoField = $('#area-conhecimento');
      var selectOptions = {};
      var areasConhecimentoInstituicao = response['areas'];
      var areasConhecimentoSerie = response.options;

      if (areasConhecimentoInstituicao) {
        areasConhecimentoInstituicao.forEach((area) => {
          selectOptions[area.id] = area.nome
        });
      }

      if (areasConhecimentoSerie) {
        selectOptions = areasConhecimentoSerie;
      }

      updateChozen(areaConhecimentoField, selectOptions);
    }

    function updateAreaConhecimento() {
      var instituicao_id  = getUrlParameter('instituicao_id');
      var serie_id  = getUrlParameter('serie_id');
      var url = getResourceUrlBuilder.buildUrl('/module/Api/AreaConhecimento', 'areas-de-conhecimento', {
        instituicao_id : instituicao_id
      });

      if (serie_id != '') {
        var url = getResourceUrlBuilder.buildUrl('/module/Api/AreaConhecimento', 'areaconhecimento-serie', {
          serie_id : serie_id
        });
      }

      var options = {
        url : url,
        dataType : 'json',
        success  : getAreaConhecimento
      };

      getResources(options);
    }


    $j('#media-area-conhecimento').change(function() {
        $j('#tr-area-conhecimento').show();
        $j('#area-conhecimento').chosen();

        if($j('#media-area-conhecimento').is(':checked') == true){
            $j('#area-conhecimento').chosen('destroy');
            $j('#tr-area-conhecimento').hide();
            $j('#area-conhecimento option:selected').removeAttr("selected");
        }
    });

    $('#area-conhecimento').chosen();

})(jQuery);
