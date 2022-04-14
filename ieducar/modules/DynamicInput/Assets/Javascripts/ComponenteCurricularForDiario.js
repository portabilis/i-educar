(function($){
  $(document).ready(function(){

    var $anoField                  = getElementFor('ano');
    var $turmaField                = getElementFor('turma');
    var $componenteCurricularField = getElementFor('componente_curricular');
    var $etapaField                = getElementFor('etapa');

    var handleGetComponentesCurriculares = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options'], false);
      updateSelect($componenteCurricularField, selectOptions, "Selecione um componente curricular");
    }

    var updateComponentesCurriculares = function(){
      resetSelect($componenteCurricularField);

      if ($anoField.val() && $turmaField.val() && $turmaField.is(':enabled')) {
        $componenteCurricularField.children().first().html('Aguarde carregando...');

        var data = {
          ano      : $anoField.attr('value'),
          turma_id : $turmaField.attr('value'),
          etapa    : $etapaField.attr('value')
        };

        var urlForGetComponentesCurriculares = getResourceUrlBuilder.buildUrl(
          '/module/DynamicInput/componenteCurricular', 'componentesCurricularesForDiario', data
        );

        var options = {
          url : urlForGetComponentesCurriculares,
          dataType : 'json',
          success  : handleGetComponentesCurriculares
        };

        getResources(options);
      }

      $componenteCurricularField.change();
    };

    // bind onchange event
    $etapaField.change(updateComponentesCurriculares);

  }); // ready
})(jQuery);
