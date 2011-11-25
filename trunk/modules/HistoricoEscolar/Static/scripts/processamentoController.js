var $j = jQuery.noConflict();

(function($){

  $(function(){

    function safeToUpperCase(value){

      if (typeof(value) == 'string')
        value = value.toUpperCase();

      return value;
    }

    function safeLog(value)
    {
      if(typeof(console) != 'undefined' && typeof(console.log) == 'function')
        console.log(value);
    }

    function utf8Decode(s){
      try{
          return decodeURIComponent(escape(s));
      }
      catch(e){
          safeLog('Erro ao decodificar string utf8: ' + s);
          return s;
      }
    }

    var $formFilter = $('#formcadastro');
    var $submitButton = $('#botao_busca');
    var $resultTable = $('#form_resultado .tablelistagem').addClass('horizontal-expand');
    $resultTable.children().remove();

    $('<div />').attr('id', 'first-bar-action')
                .attr('class', 'bar-action')
                .prependTo($resultTable.parent());

    $('<div />').attr('id', 'second-bar-action')
                .attr('class', 'bar-action')
                .appendTo($resultTable.parent());

    var $barActions = $('.bar-action');

    var PageUrlBase = 'processamento';
    var ApiUrlBase = 'processamentoApi';

    var $navActions = $('<p />').attr('id', 'nav-actions');
    $navActions.prependTo($formFilter.parent()); 

    var $tableSearchDetails = $('<table />')
                              .attr('id', 'search-details')
                              .addClass('styled')
                              .addClass('horizontal-expand')
                              .addClass('center')
                              .hide()
                              .prependTo($formFilter.parent());

    var $feedbackMessages = $('<div />').attr('id', 'feedback-messages').appendTo($formFilter.parent());

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
          instituicao_id : $('#ref_cod_instituicao').val(),
          escola_id : $('#ref_cod_escola').val(),
          curso_id : $('#ref_cod_curso').val(),
          serie_id : $('#ref_ref_cod_serie').val(),
          turma_id : $('#ref_cod_turma').val(),
          ano : $('#ano').val(),
          componente_curricular_id : $('#ref_cod_componente_curricular').val(),
          etapa : $('#etapa').val()
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
          escola_id : $('#ref_cod_escola').val(),
          curso_id : $('#ref_cod_curso').val(),
          serie_id : $('#ref_ref_cod_serie').val(),
          turma_id : $('#ref_cod_turma').val(),
          ano : $('#ano').val(),
          componente_curricular_id : $('#ref_cod_componente_curricular').val(),
          etapa : $('#etapa').val(),
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
          serie_id : $('#ref_ref_cod_serie').val(),
          turma_id : $('#ref_cod_turma').val(),
          ano : $('#ano').val(),
          componente_curricular_id : $('#ref_cod_componente_curricular').val(),
          etapa : $('#etapa').val()
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

    function validatesIfValueIsNumeric(value, targetId){
      var isNumeric = $.isNumeric(value);

      if (! isNumeric)
        handleMessages([{type : 'error', msg : 'Informe um numero válido.'}], targetId);

      return isNumeric;
    }  

    
    function postResource(options, errorCallback){
      $.ajax(options).error(errorCallback);
    }

 
    var postProcessamento = function(){

      var $proximoMatriculaIdField = $('#proximo-matricula-id');
      $proximoMatriculaIdField.data('initial_matricula_id', $proximoMatriculaIdField.val())

      if (validatesIfValueIsNumeric($proximoMatriculaIdField.val()))
      {

        var options = {
          url : postResourceUrlBuilder.buildUrl(ApiUrlBase, 'promocao', {matricula_id : $proximoMatriculaIdField.val()}),
          dataType : 'json',
          data : {},
          success : handlePostProcessamento
        };

        postResource(options, handleErrorPost);

      }
    }


    //callback handlers

    //delete
    function handleDelete(dataResponse){
      var targetId = dataResponse.att + '-matricula-' + dataResponse.matricula_id;
      handleMessages(dataResponse.msgs, targetId);
      updateFieldSituacaoMatricula(dataResponse.matricula_id, dataResponse.situacao);
    }


    function handleErrorDeleteResource(response){
      handleMessages([{type : 'error', msg : 'Erro ao alterar recurso, detalhes:' + response.responseText}], '');
      safeLog(response);
    }

    //post
    function handleErrorPost(response){
      handleMessages([{type : 'error', msg : 'Erro ao alterar recurso, detalhes:' + response.responseText}], '');
      safeLog(response);
    }


    function handlePostProcessamento(dataResponse){

      safeLog(dataResponse);
      handleMessages(dataResponse.msgs);
      safeLog('#TODO handlePostProcessamento');

    }

    function handleMessages(messages, targetId){

      var hasErrorMessages = false;
      var hasSuccessMessages = false;

      for (var i = 0; i < messages.length; i++){

        if (messages[i].type != 'error')
          var delay = 10000;
        else
          var delay = 60000;

        $('<p />').addClass(messages[i].type).html(messages[i].msg).appendTo($feedbackMessages).delay(delay).fadeOut(function(){$(this).remove()}).data('target_id', targetId);

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


    function setTableSearchDetails(dataDetails){
      $('<caption />').html('<strong>Proccessamento histórico</strong>').appendTo($tableSearchDetails);

      //set headers table
      var $linha = $('<tr />');
      $('<th />').html('Ano').appendTo($linha);
      $('<th />').html('Escola').appendTo($linha);
      $('<th />').html('Curso').appendTo($linha);
      $('<th />').html('Serie').appendTo($linha);
      $('<th />').html('Turma').appendTo($linha);
      $('<th />').html('Matricula').appendTo($linha);

      $linha.appendTo($tableSearchDetails);

      var $linha = $('<tr />').addClass('even');

      $('<td />').html($('#ano').val()).appendTo($linha);

      //field escola pode ser diferente de select caso usuario comum 
      var $htmlEscolaField = $('#ref_cod_escola').children("[selected='selected']").html() ||
                             $j('#tr_nm_escola span:last').html();
      $('<td />').html(safeToUpperCase($htmlEscolaField)).appendTo($linha);

      $('<td />').html(safeToUpperCase($('#ref_cod_curso').children("[value!=''][selected='selected']").html()  || 'Todos')).appendTo($linha);
      $('<td />').html(safeToUpperCase($('#ref_ref_cod_serie').children("[value!=''][selected='selected']").html()  || 'Todas')).appendTo($linha);
      $('<td />').html(safeToUpperCase($('#ref_cod_turma').children("[value!=''][selected='selected']").html()  || 'Todas')).appendTo($linha);
      $('<td />').html(safeToUpperCase($('#ref_cod_matricula').children("[value!=''][selected='selected']").html() || 'Todas')).appendTo($linha);
     
      $linha.appendTo($tableSearchDetails);
      $tableSearchDetails.show();

      $tableSearchDetails.data('details', dataDetails);
    }

    //exibe formulário nova consulta
    function showSearchForm(event){
      //$(this).hide();
      $navActions.html('');
      $tableSearchDetails.children().remove();
      $resultTable.children().fadeOut('fast').remove();
      $formFilter.fadeIn('fast', function(){
        $(this).show()
      });
    }


    function showNewSearchButton(){
      $navActions.html(
        $("<a href='#'>Nova consulta</a>")
        .bind('click', showSearchForm)
        .attr('style', 'text-decoration: underline')
      );
    }

    function showSearchButton(){
      $navActions.html(
        $("<a href='#'>Nova consulta</a>")
        .bind('click', showSearchForm)
        .attr('style', 'text-decoration: underline')
      );
    }

    function handleMatriculasSearch(dataResponse){ 

      showNewSearchButton();

      //try{      
        handleMessages(dataResponse.msgs);

        if(! $.isArray(dataResponse.matriculas))
        {
           $('<td />')
            .html('As matriculas n&#227;o poderam ser recuperadas, verifique as mensagens de erro ou tente <a alt="Recarregar página" href="/" style="text-decoration:underline">recarregar</a>.')
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
          setTableSearchDetails();
          //set headers
          var $linha = $('<tr />');
          $('<th />').html('Selecionar').appendTo($linha);
          $('<th />').html('Curso').appendTo($linha);
          $('<th />').html('Série').appendTo($linha);
          $('<th />').html('Turma').appendTo($linha);
          $('<th />').html('Matricula').appendTo($linha);
          $('<th />').html('Aluno').appendTo($linha);
          $('<th />').html('Situa&#231;&#227;o').appendTo($linha);
          $linha.appendTo($resultTable);

          //set rows
          $.each(dataResponse.matriculas, function(index, value){

            var $checkbox = $('<input />')
                            .attr('type', 'checkbox')
                            .attr('name', 'processar-matricula')
                            .attr('value', 'sim')
                            .attr('id', 'matricula-id-' + value.matricula_id)
                            .attr('class', 'matricula')
                            .data('matricula_id', value.matricula_id);

            var $linha = $('<tr />');
            $('<td />').html($checkbox).addClass('center').appendTo($linha);
            $('<td />').html(value.nome_curso).addClass('center').appendTo($linha);
            $('<td />').html(value.nome_serie).addClass('center').appendTo($linha);
            $('<td />').html(value.nome_turma).addClass('center').appendTo($linha);
            $('<td />').html(value.matricula_id).addClass('center').appendTo($linha);
            $('<td />').html(value.aluno_id + " - " + safeToUpperCase(value.nome)).appendTo($linha);
            $('<td />').html(value.situacao_historico).addClass('center').appendTo($linha);

            $linha.fadeIn('slow').appendTo($resultTable);
          });//fim each matriculas

          $resultTable.find('tr:even').addClass('even');
          $resultTable.addClass('styled').find('checkbox:first').focus();
        }
      /*}
      catch(error){
        showSearchButton();

        handleMessages([{type : 'error', msg : 'Ocorreu um erro ao exibir as matriculas, por favor tente novamente, detalhes: ' + error}], '');

        safeLog(dataResponse);
      }*/
    }

    function handleErrorMatriculasSearch(response){
      showSearchButton();

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
      }
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

  });

})(jQuery);
