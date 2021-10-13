(function($){
  $(document).ready(function(){

    var $turmaField = getElementFor('turma');
    var $etapaField = getElementFor('fase_etapa');

    var handleGetEtapas = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($etapaField, selectOptions, "Selecione uma etapa");
    }

    var updateEtapas = function(){
      resetSelect($etapaField);

      if ($turmaField.val() && $turmaField.is(':enabled')) {
        $etapaField.children().first().html('Aguarde, carregando...');

        var data = {
          turma_id  : $turmaField.attr('value')
        };

        var urlForGetEtapas = getResourceUrlBuilder.buildUrl('/module/DynamicInput/FaseEtapa',
                                                             'etapas', data);
        var options = {
          url      : urlForGetEtapas,
          dataType : 'json',
          success  : handleGetEtapas
        };

        getResources(options);
      }

      $etapaField.change();
    };

    // bind onchange event
    $turmaField.change(updateEtapas);

  }); // ready
})(jQuery);
