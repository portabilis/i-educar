(function($){
  $(document).ready(function(){

    var $anoField   = getElementFor('ano');
    var $escolaField = getElementFor('escola');
    var $cursoField = getElementFor('curso');
    var $turmaField = getElementFor('turma');
    var $etapaField = getElementFor('etapa');

    var handleGetEtapas = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($etapaField, selectOptions, "Selecione uma etapa");
    }

    var updateEtapas = function(){
      resetSelect($etapaField);

      if ($anoField.val() && $escolaField.val() && $cursoField.val() && $turmaField.val() && $turmaField.is(':enabled')) {
        $etapaField.children().first().html('Aguarde carregando...');

        var data = {
          ano       : $anoField.attr('value'),
          escola_id : $escolaField.attr('value'),
          curso_id  : $cursoField.attr('value'),
          turma_id  : $turmaField.attr('value')
        };

        var urlForGetEtapas = getResourceUrlBuilder.buildUrl('/module/DynamicInput/Etapa',
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
