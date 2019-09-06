(function ($) {
  $(document).ready(function () {

    var $escolaField = getElementFor('escola');
    var $serieField = getElementFor('serie');
    var $componenteCurricularField = getElementFor('componente_curricular');

    var handleGetComponentesCurriculares = function (response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($componenteCurricularField, selectOptions, "Selecione um componente curricular");
    }

    var updateComponentesCurriculares = function () {
      resetSelect($componenteCurricularField);

      if ($escolaField.val() && $serieField.val() && $serieField.is(':enabled')) {
        $componenteCurricularField.children().first().html('Aguarde carregando...');

        var data = {
          escola: $escolaField.attr('value'),
          serie: $serieField.attr('value')
        };

        var urlForGetComponentesCurriculares = getResourceUrlBuilder.buildUrl(
          '/module/DynamicInput/componenteCurricular', 'componentesCurricularesEscolaSerie', data
        );

        var options = {
          url: urlForGetComponentesCurriculares,
          dataType: 'json',
          success: handleGetComponentesCurriculares
        };

        getResources(options);
      }

      $componenteCurricularField.change();
    };

    // bind onchange event
    $serieField.change(updateComponentesCurriculares);

  }); // ready
})(jQuery);
