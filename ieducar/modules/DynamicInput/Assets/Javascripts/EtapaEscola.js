(function($){
  $(document).ready(function(){

    var $anoField   = getElementFor('ano');
    var $escolaField = getElementFor('escola');
    var $etapaField = getElementFor('etapa');

    var handleGetEtapasEscola = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($etapaField, selectOptions, "Selecione uma etapa");
    }

    var updateEtapasEscola = function(){
      resetSelect($etapaField);

      if ($anoField.val() && $escolaField.val()) {
        $etapaField.children().first().html('Aguarde carregando...');

        var data = {
          ano       : $anoField.attr('value'),
          escola_id : $escolaField.attr('value')
        };

        var urlForGetEtapas = getResourceUrlBuilder.buildUrl('/module/DynamicInput/Etapa',
                                                             'etapasEscola', data);

        var options = {
          url      : urlForGetEtapas,
          dataType : 'json',
          success  : handleGetEtapasEscola
        };

        getResources(options);
      }

      $etapaField.change();
    };

    // bind onchange event
    $escolaField.change(updateEtapasEscola);

  }); // ready
})(jQuery);
