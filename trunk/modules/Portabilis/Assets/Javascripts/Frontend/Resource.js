// metodos e variaveis acessiveis por outros modulos


var resourceOptions = {
  // options that cannot be overwritten in child
  get           : function(optionName) { return optionsUtils.get(this, optionName) },

  // options that can be overwritten in child
  form         : $j('#formcadastro'),
  api_url_base : function() { return '/module/Api/' + resourceOptions.get('name') },

  // options that must be set in child
  name         : undefined,
};

// metodos e variaveis não acessiveis por outros modulos

(function($) {
  $(document).ready(function() {

    var $submitButton = $('#btn_enviar');

    // config resource form
    var submitOptions = {
      url      : '',
      dataType : 'json',
      success  : handleSuccess,
      error    : handleError
    };

    resourceOptions.form.ajaxForm(submitOptions);


    // submit button callbacks
    var onClickSubmitEvent = function(event) {
      if (validatesPresenseOfValueInRequiredFields()) {

        var urlBuilder;

        if (resourceOptions.get('new'))
          urlBuilder = postResourceUrlBuilder;
        else
          urlBuilder = putResourceUrlBuilder;

        submitOptions.url = urlBuilder.buildUrl(resourceOptions.get('api_url_base')(),
                                                resourceOptions.get('name'),
                                                {});

        // #TODO alterar texto $submitButton para Aguarde... enquanto estiver enviando ?
        resourceOptions.form.submit();
      }
    };


    function handleSuccess(dataResponse) {
      try{
        handleMessages(dataResponse.msgs);

        if(! dataResponse[resourceOptions.get('name')])
          throw new Error('Erro ao realizar operação, verifique as mensagens de erro e tente novamente.');

        if (resourceOptions.get('new'))
          resourceOptions.get('handlePost')(dataResponse);
        else
          resourceOptions.get('handlePut')(dataResponse);
      }
      catch(error) {
        handleMessages([{type : 'error', msg : 'Erro ao realizar operação, por favor tente novamente, detalhes: ' + error}], '');

        safeLog('Error details:');
        safeLog(error);

        safeLog('dataResponse details:');
        safeLog(dataResponse);

        throw error;
      }
    }


    function handleError(response) {
      handleMessages([{type : 'error', msg : 'Erro ao realizar operação, por favor tente novamente, detalhes:' + response.responseText}], '');

      safeLog('response details:');
      safeLog(response);
    }

    $submitButton.val('Gravar');

    // remove event attrs
    $submitButton.removeAttr('onclick');
    resourceOptions.form.removeAttr('onsubmit');

    // bind events
    $submitButton.click(onClickSubmitEvent);
  }); // ready
})(jQuery);
