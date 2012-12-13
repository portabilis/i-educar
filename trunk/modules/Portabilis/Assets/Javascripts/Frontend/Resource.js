// metodos e variaveis acessiveis por outros modulos

var resourceOptions = {
  // options that cannot be overwritten in child

  get    : function(optionName) { return optionsUtils.get(this, optionName) },

  isNew : function() { return this.id() == undefined },

  id : function(){
    var id;

    if (window.location.search.indexOf('id=') > -1) {
      id = window.location.search.split('id=');
      id = id[id.length - 1];
    }

    return id;
  },


  // options that can be overwritten in child

  form         : $j('#formcadastro'),

  apiUrlBase : function() { return '/module/Api/' + resourceOptions.get('name')() },

  name : function() {
    var name = window.location.pathname.split('/');
        name = name[name.length - 1];

    return name;
  },

  handlePost : function(dataResponse) {
    if (dataResponse.id) {
      if (window.history && window.history.pushState)
        window.history.pushState('', '', window.location.href.split("?")[0] + "?id=" + dataResponse.id);
    }
  },

  handlePut : function(dataResponse) {},

  handleGet : function(dataResponse) {
    throw new Error('The function resourceOptions.handleGet must be overwritten!');
  },

  getResource : function(id) {
    var additionalVars = {
      id : id,
    };

    var options = {
      url      : getResourceUrlBuilder.buildUrl(this.apiUrlBase(), this.get('name')(), additionalVars),
      dataType : 'json',
      success  : this.handleGet,
    };

    getResource(options);
  },
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
        var additionalVars = {};

        if (resourceOptions.isNew())
          urlBuilder = postResourceUrlBuilder;
        else {
          urlBuilder = putResourceUrlBuilder;
          additionalVars.id = resourceOptions.id();
        }

        submitOptions.url = urlBuilder.buildUrl(resourceOptions.get('apiUrlBase')(),
                                                resourceOptions.get('name')(),
                                                additionalVars);

        // #TODO alterar texto $submitButton para Aguarde... enquanto estiver enviando ?
        resourceOptions.form.submit();
      }
    };


    function handleSuccess(dataResponse) {
      try {
        handleMessages(dataResponse.msgs, 'btn_enviar');

        if(! dataResponse.any_error_msg && ! dataResponse[resourceOptions.get('name')()] && ! dataResponse.id)
          throw new Error('A API não retornou o recurso nem seu id.');

        if (resourceOptions.isNew())
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


    if (! resourceOptions.isNew())
      resourceOptions.getResource(resourceOptions.id());

  }); // ready
})(jQuery);
