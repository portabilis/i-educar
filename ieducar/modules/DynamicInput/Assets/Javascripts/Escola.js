(function($){
  $(document).ready(function(){
    var $instituicaoField = getElementFor('instituicao');
    var $escolaField = getElementFor('escola');

    var handleGetEscolas = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options'], false);
      updateSelect($escolaField, selectOptions, "Selecione uma escola");
    }

    var updateEscolas = function() {
      var additionalVars = {
        instituicao : $instituicaoField.val()
      };
      var options = {
        url      : getResourceUrlBuilder.buildUrl('/module/Api/escola', 'escolas-para-selecao', additionalVars),
        dataType : 'json',
        data     : {},
        success  : handleGetEscolas,
      };
      getResource(options);
    }

    $instituicaoField.change(updateEscolas);

  }); // ready
})(jQuery);
