(function ($) {
  $(document).ready(function () {

    var $escolaField = getElementFor('escola');
    var $serieField = getElementFor('serie');
    var $anoField = getElementFor('ano');
    var $cursoField = getElementFor('curso');
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
          serie: $serieField.attr('value'),
          ano: $anoField.attr('value')
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

    $j('#ref_cod_turma').on('change', function () {
      if (!$j('#ref_cod_turma').val()) {
        updateComponentesCurriculares()
      }
    });

    // bind onchange event
    $serieField.change(updateComponentesCurriculares);

    var updateComponentesCurricularesPorEscola = function () {
      resetSelect($componenteCurricularField);

      if ($escolaField.val()) {
        $componenteCurricularField.children().first().html('Aguarde carregando...');

        var data = {
          escola: $escolaField.attr('value'),
          ano: $anoField.attr('value')
        };

        var urlForGetComponentesCurricularesPorEscola = getResourceUrlBuilder.buildUrl(
          '/module/DynamicInput/componenteCurricular', 'componentesCurricularesEscola', data
        );

        var options = {
          url: urlForGetComponentesCurricularesPorEscola,
          dataType: 'json',
          success: handleGetComponentesCurriculares
        };

        getResources(options);
      }

      $componenteCurricularField.change();
    };

    $cursoField.change(updateComponentesCurricularesPorEscola);

  }); // ready
})(jQuery);
