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

    var promocaoUrlBase = 'promocao';
    var promocaoAjaxUrlBase = 'promocaoAjax';

    var $navActions = $('<p />').attr('id', 'nav-actions');
    $navActions.prependTo($formFilter.parent()); 

    var $feedbackMessagesSuccess = $('<div />').attr('id', 'feedback-messages-success').appendTo($formFilter.parent());
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
          ano_escolar : $('#ano_escolar').val(),
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
          ano_escolar : $('#ano_escolar').val(),
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
          ano_escolar : $('#ano_escolar').val(),
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

 
    var postPromocaoMatricula = function(){

      var $proximoMatriculaIdField = $('#proximo-matricula-id');
      $proximoMatriculaIdField.data('initial_matricula_id', $proximoMatriculaIdField.val())

      if (validatesIfValueIsNumeric($proximoMatriculaIdField.val()))
      {

        var options = {
          url : postResourceUrlBuilder.buildUrl(promocaoAjaxUrlBase, 'promocao', {matricula_id : $proximoMatriculaIdField.val()}),
          dataType : 'json',
          data : {},
          success : handlePostPromocaoMatricula
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


    function handlePostPromocaoMatricula(dataResponse){

      safeLog(dataResponse);

      handleMessages(dataResponse.msgs);
      var $proximoMatriculaIdField = $('#proximo-matricula-id');
      $proximoMatriculaIdField.val(dataResponse.result.proximo_matricula_id);

      if($('#continuar-processo').is(':checked') && 
         $.isNumeric($proximoMatriculaIdField.val()) &&
         $proximoMatriculaIdField.data('initial_matricula_id') != $proximoMatriculaIdField.val()){
        $('#promover-matricula').click();
      }
      else if(($('#continuar-processo').is(':checked') && 
             $proximoMatriculaIdField.data('initial_matricula_id') == $proximoMatriculaIdField.val()) ||
             ! $.isNumeric($proximoMatriculaIdField.val())){
        alert('Processo finalizado');
      }
    }


    function handleMessages(messages, targetId){

      for (var i = 0; i < messages.length; i++){

        if (messages[i].type == 'success')
          $('<p />').addClass(messages[i].type).html(messages[i].msg).appendTo($feedbackMessagesSuccess);
        else
          $('<p />').addClass(messages[i].type).html(messages[i].msg).appendTo($feedbackMessages);
      }
    }


    //exibe formulário nova consulta
    function showSearchForm(event){
      //$(this).hide();
      $navActions.html('');
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

      try{      

        handleMessages(dataResponse.msgs);

        var $text = $('<p />').html('Quantidade de matrículas em andamento: ' + 
                                    dataResponse.quantidade_matriculas + '<br />');

        $('<input />').attr('type', 'checkbox').attr('id', 'continuar-processo').attr('name', 'continuar-processo').appendTo($text);
        $('<span />').html('Continuar processo <br />').appendTo($text);

        $('<span />').html('proxima matricula:').appendTo($text);
        $('<input />').attr('type', 'text').attr('name', 'proximo-matricula-id').attr('id', 'proximo-matricula-id').val('0').appendTo($text);

        $('<br />').appendTo($text);

        $('<a />').attr('id', 'promover-matricula')
                  .attr('href', '#')
                  .html('Iniciar processo')
                  .attr('style', 'text-decoration:underline')
                  .bind('click', postPromocaoMatricula)
                  .appendTo($text);

        $('<span />').html(' ').appendTo($text);

        $('<a />').attr('id', 'clear-messages')
                  .attr('href', '#')
                  .html('Limpar mensagens')
                  .attr('style', 'text-decoration:underline')
                  .bind('click', function(){
                    $('#feedback-messages').children().remove();
                    $('#feedback-messages-success').children().remove();
                  })
                  .appendTo($text);

        $('<td />').html($text).appendTo($('<tr />').appendTo($resultTable));

      }
      catch(error){
        showSearchButton();

        handleMessages([{type : 'error', msg : 'Ocorreu um erro ao exibir as matriculas, por favor tente novamente, detalhes: ' + error}], '');

        safeLog(dataResponse);
      }
    }

    function handleErrorMatriculasSearch(response){
      showSearchButton();

      handleMessages([{type : 'error', msg : 'Ocorreu um erro ao carregar as matrículas, por favor tente novamente, detalhes:' + response.responseText}], '');

      safeLog(response);
    }

    //change submit button
    var onClickSearchEvent = function(event){
      if (validatesPresenseOfValueInRequiredFields())
      {
        matriculasSearchOptions.url = getResourceUrlBuilder.buildUrl(promocaoAjaxUrlBase, 'quantidade_matriculas', {matricula_id : $('#ref_cod_matricula').val()});

        if (window.history && window.history.pushState)
          window.history.pushState('', '', getResourceUrlBuilder.buildUrl(promocaoUrlBase, 'quantidade_matriculas'));

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
