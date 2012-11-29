// metodos e variaveis acessiveis por outros modulos

function removeImgLoadingFor($targetElement) {
  $targetElement.siblings('img').remove();
}


function appendImgLoadingTo($targetElement) {
  if ($targetElement.siblings('img').length < 1);
    $j('<img alt="loading..." src="/modules/Portabilis/Assets/Images/loading.gif" />').appendTo($targetElement.parent());
}


function beforeChangeResource($resourceElement) {
  if ($resourceElement.is(':checkbox'))
    $j('.disable-on-apply-changes').attr('disabled', 'disabled');

  appendImgLoadingTo($resourceElement);
}

function afterChangeResource($resourceElement) {
  if ($resourceElement.is(':checkbox'))
    $resourceElement.attr('checked', false);

  removeImgLoadingFor($resourceElement);
  $j('.disable-on-apply-changes').removeAttr('disabled');

  // change value of execute action button
  $j('input.execute-action').val(POST_LABEL);
  $j('input.delete').val(DELETE_LABEL);
}

function getFirstCheckboxChecked($targetElement) {
  var $firstChecked = $j('input.'+RESOURCE_NAME+':checked:first');

  if ($firstChecked.length < 1) {
    handleMessages([{type : 'error', msg : 'Selecione algum '+RESOURCE_NAME+'.'}], $targetElement, true);
    $firstChecked = undefined;
  }

  return $firstChecked;
}


var $formFilter = $j('#formcadastro');

var $tableSearchDetails = $j('<table />').attr('id', 'search-details')
                                        .addClass('styled')
                                        .addClass('horizontal-expand')
                                        .addClass('center')
                                        .hide()
                                        .prependTo($formFilter.parent());


// metodos e variaveis não acessiveis por outros modulos

(function($) {
  $(document).ready(function() {
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

    $('<input class="select-all disable-on-apply-changes" type="button" value="Selecionar todos" />').appendTo($barActions);

    $('<input class="execute-action disable-on-apply-changes" type="button" />').val(POST_LABEL).appendTo($barActions);

    $('<input class="delete disable-on-apply-changes" type="button" />').val(DELETE_LABEL).appendTo($barActions);

    var $selectAllButton = $barActions.find('input.select-all');
    var $actionButton = $barActions.find('input.execute-action');
    var $deleteButton = $barActions.find('input.delete');

    // add resource options table
    var $resourceOptionsTable = $('#resource-options');
    $resourceOptionsTable.find('tr:even').addClass('even');
    $resourceOptionsTable.hide().prependTo($formFilter.parent());

    // add navigation actions
    var $navActions = $('<p />').attr('id', 'nav-actions');
    $navActions.insertAfter($tableSearchDetails);

    // add orientations about search
    $('<p />').html(SEARCH_ORIENTATION)
              .addClass('center')
              .attr('id', 'orientation-search')
              .appendTo($resultTable.parent());

    // before search changes
    $('.disable-on-search').attr('disabled', 'disabled');
    $('.hide-on-search').hide();


    // functions, callbacks

    function showSearchForm(event) {
      $navActions.html('');
      $tableSearchDetails.children().remove();
      $resultTable.children().fadeOut('fast').remove();
      $formFilter.fadeIn('fast', function() {
        $(this).show()
      });
      $('.disable-on-search').attr('disabled', 'disabled');
      $('.hide-on-search').hide();
      $('.disable-on-apply-changes').removeAttr('disabled');
      //$actionButton.val(POST_LABEL);
    }


    function showNewSearchButton() {
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
        searchOptions.url = getResourceUrlBuilder.buildUrl(API_URL_BASE, RESOURCES_NAME, {});

        if (window.history && window.history.pushState)
          window.history.pushState('', '', getResourceUrlBuilder.buildUrl(PAGE_URL_BASE, RESOURCES_NAME));

        $resultTable.children().fadeOut('fast').remove();

        $formFilter.submit();
        $formFilter.fadeOut('fast');
        $navActions
          .html('Aguarde, carregando...')
          .attr('style', 'text-align:center;')
          .unbind('click');
      }
    };


    function _setTableSearchDetails(dataDetails) {
      setTableSearchDetails($tableSearchDetails, dataDetails);
    }


    function _handleSearch(dataResponse) {
      showNewSearchButton();

      try{
        handleMessages(dataResponse.msgs);

        var resources = dataResponse[RESOURCES_NAME];

        if(! $.isArray(resources))
        {
           $('<td />')
            .html('O(a)s '+ RESOURCES_NAME +' n&#227;o poderam ser recuperado(a)s, verifique as mensagens de erro ou tente <a alt="Recarregar página" href="/" style="text-decoration:underline">recarregar</a>.')
            .addClass('center')
            .appendTo($('<tr />').appendTo($resultTable));
        }
        else if (resources.length < 1)
        {
           $('<td />')
            .html('Busca de '+ RESOURCES_NAME +' sem resultados.')
            .addClass('center')
            .appendTo($('<tr />').appendTo($resultTable));
        }
        else
        {
          _setTableSearchDetails(dataResponse['details']);
          handleSearch($resultTable, dataResponse);
        }
      }
      catch(error) {
        showNewSearchButton();

        handleMessages([{type : 'error', msg : 'Ocorreu um erro ao exibir o recurso '+ RESOURCES_NAME +', por favor tente novamente, detalhes: ' + error}], '');

        safeLog('Error details:');
        safeLog(error);

        safeLog('dataResponse details:');
        safeLog(dataResponse);
      }
    }


    function handleSearchError(response) {
      showNewSearchButton();

      handleMessages([{type : 'error', msg : 'Ocorreu um erro ao carregar o recurso '+ RESOURCES_NAME +', por favor tente novamente, detalhes:' + response.responseText}], '');

      safeLog('response details:');
      safeLog(response);
    }

    $submitButton.val('Carregar');
    $submitButton.attr('onclick', '');

    // bind events
    $submitButton.click(onClickSearchEvent);

    onClickSelectAllEvent ? $selectAllButton.click(onClickSelectAllEvent) : $selectAllButton.hide();
    onClickActionEvent    ? $actionButton.click(onClickActionEvent)       : $actionButton.hide();
    onClickDeleteEvent   ? $deleteButton.click(onClickDeleteEvent)     : $deleteButton.hide();

  }); // ready
})(jQuery);
