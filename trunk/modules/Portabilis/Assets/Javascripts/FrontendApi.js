function safeLog(value)
{
  if(typeof(console) != 'undefined' && typeof(console.log) == 'function')
    console.log(value);
}


function buildId(id) {
  return typeof(id) == 'string' && id.length > 0 && id[0] == '#' ? id : '#' + id;
}


function validatesPresenseOfValueInRequiredFields(additionalFields) {
  var emptyFields = [];
  var requiredFields = document.getElementsByClassName('obrigatorio');

  if (additionalFields)
    requiredFields = requiredFields.concat(additionalFields);

  for (var i = 0; i < requiredFields.length; i++) {
    var requiredField = requiredFields[i];
    if (requiredField.style.display != 'none' && ! requiredField.getAttribute('disabled') && ! requiredField.value) {
      emptyFields.push(requiredField);

      if (requiredField.className.indexOf('error') < 0)
        requiredField.className = requiredField.className + " error";
    }
    else
      requiredField.className = requiredField.className.replace('error', '')
  }

  if (emptyFields.length == 0)
    return true;

  alert('Preencha todos campos obrigat\u00F3rios, antes de continuar.');
  emptyFields[0].focus();
  return false;
}


(function($){
  $(document).ready(function(){

    var $formFilter = $('#formcadastro');
    var $submitButton = $('#botao_busca');

    // prepare result table
    var $resultTable = $('#form_resultado .tablelistagem').addClass('horizontal-expand');
    $resultTable.children().remove();


    // add action bars
    $('<div />').attr('id', 'first-bar-action')
                .attr('class', 'bar-action hide-on-search')
                .prependTo($resultTable.parent());

    $('<div />').attr('id', 'second-bar-action')
                .attr('class', 'bar-action hide-on-search')
                .appendTo($resultTable.parent());

    var $barActions = $('.bar-action').hide();

    $('<input class="selecionar disable-on-apply-changes" type="button" value="Selecionar todos" />').appendTo($barActions);

    $('<input class="execute-action disable-on-apply-changes" type="button" value="Execute action" />').appendTo($barActions);

    $('<input class="destroy disable-on-apply-changes" type="button" value="Remover" />').appendTo($barActions);

    var $selectAllButton = $barActions.find('input.selecionar');
    var $actionButton = $barActions.find('input.execute-action');
    var $destroyButton = $barActions.find('input.destroy');


    // add resource options table
    var $resourceOptionsTable = $('#resource-options');
    $resourceOptionsTable.find('tr:even').addClass('even');
    $resourceOptionsTable.hide().prependTo($formFilter.parent());


    // add navigation actions
    var $navActions = $('<p />').attr('id', 'nav-actions');
    $navActions.prependTo($formFilter.parent());


    // add table for search details
    var $tableSearchDetails = $('<table />')
                              .attr('id', 'search-details')
                              .addClass('styled')
                              .addClass('horizontal-expand')
                              .addClass('center')
                              .hide()
                              .prependTo($formFilter.parent());


    // add div for feedback messages
    var $feedbackMessages = $('<div />').attr('id', 'feedback-messages').appendTo($formFilter.parent());


    // before search changes
    $('.disable-on-search').attr('disabled', 'disabled');
    $('.hide-on-search').hide();


    // functions, callbacks

    function handleMessages(messages, targetId, useDelayClassRemoval){

      var hasErrorMessages = false;
      var hasSuccessMessages = false;
      var hasNoticeMessages = false;
      var delayClassRemoval = 20000;

      var $targetElement = $(buildId(targetId));

      for (var i = 0; i < messages.length; i++){
        if (messages[i].type == 'success')
          var delay = 2000;
        else if (messages[i].type != 'error')
          var delay = 10000;
        else
          var delay = 60000;

        $('<p />').addClass(messages[i].type).html(messages[i].msg).appendTo($feedbackMessages).delay(delay).fadeOut(function(){$(this).remove()}).data('target_id', targetId);

        if (! hasErrorMessages && messages[i].type == 'error')
          hasErrorMessages = true;
        else if(! hasSuccessMessages && messages[i].type == 'success')
          hasSuccessMessages = true;
        else if(! hasNoticeMessages && messages[i].type == 'notice')
          hasNoticeMessages = true;
      }

      if($targetElement){
        if (hasErrorMessages)
          $targetElement.addClass('error').removeClass('success').removeClass('notice');
        else if (hasSuccessMessages)
          $targetElement.addClass('success').removeClass('error').removeClass('notice');
        else if (hasNoticeMessages)
          $targetElement.addClass('notice').removeClass('error').removeClass('sucess');
        else
          $targetElement.removeClass('success').removeClass('error').removeClass('notice');

        $($targetElement.get(0)).focus();

        if (useDelayClassRemoval){
          window.setTimeout(function(){$targetElement.removeClass('success').removeClass('error').removeClass('notice');}, delayClassRemoval);
        }
      }
    }


    function showSearchForm(event){
      $navActions.html('');
      $tableSearchDetails.children().remove();
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


    // config search form
    var searchOptions = {
      url : '',
      dataType : 'json',
      success : _handleSearch,
      error : handleSearchError
    };

    $formFilter.ajaxForm(searchOptions);


    // submit button callbacks
    var onClickSearchEvent = function(event) {
      if (validatesPresenseOfValueInRequiredFields()) {
        searchOptions.url = getResourceUrlBuilder.buildUrl(ApiUrlBase, ResourcesName, {});

        if (window.history && window.history.pushState)
          window.history.pushState('', '', getResourceUrlBuilder.buildUrl(PageUrlBase, ResourcesName));

        $resultTable.children().fadeOut('fast').remove();

        $formFilter.submit();
        $formFilter.fadeOut('fast');
        $navActions
          .html('Aguarde, carregando...')
          .attr('style', 'text-align:center;')
          .unbind('click');
      }
    };

    function _handleSearch(dataResponse){
      showNewSearchButton();

      //try{
        handleMessages(dataResponse.msgs);

        var resources = dataResponse[ResourcesName];

        if(! $.isArray(resources))
        {
           $('<td />')
            .html('O(a)s '+ ResourcesName +' n&#227;o poderam ser recuperado(a)s, verifique as mensagens de erro ou tente <a alt="Recarregar página" href="/" style="text-decoration:underline">recarregar</a>.')
            .addClass('center')
            .appendTo($('<tr />').appendTo($resultTable));
        }
        else if (resources.length < 1)
        {
           $('<td />')
            .html('Busca de '+ ResourcesName +' sem resultados.')
            .addClass('center')
            .appendTo($('<tr />').appendTo($resultTable));
        }
        else
        {
          setTableSearchDetails();
          handleSearch(dataResponse);
        }
      /*}
      catch(error){
        showNewSearchButton();

        handleMessages([{type : 'error', msg : 'Ocorreu um erro ao exibir o(a)s '+ ResourcesName +', por favor tente novamente, detalhes: ' + error}], '');

        safeLog('Error details:');
        safeLog(error);

        safeLog('dataResponse details:');
        safeLog(dataResponse);
      }*/
    }


    function handleSearchError(response){
      showNewSearchButton();

      handleMessages([{type : 'error', msg : 'Ocorreu um erro ao carregar o(a)s '+ ResourcesName +', por favor tente novamente, detalhes:' + response.responseText}], '');

      safeLog(response);
    }

    $submitButton.val('Carregar');
    $submitButton.attr('onclick', '');

    // bind events
    $submitButton.click(onClickSearchEvent);
    $actionButton.click(onClickActionEvent);
    $selectAllButton.click(onClickSelectAllEvent);
    $destroyButton.click(onClickDestroyEvent)

  }); // ready
})(jQuery);
