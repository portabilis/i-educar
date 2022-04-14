(function($){
  $(document).ready(function(){

    // serie search expect an id for escola
    var $instituicaoField = getElementFor('instituicao');

    var $escolaField = getElementFor('escola');

    var $cursoField = getElementFor('curso');
    var $serieField = getElementFor('serie');
    var $ano = getElementFor('ano');

    var handleGetSeries = function(resources) {
      var selectOptions = jsonResourcesToSelectOptions(resources['options']);
      updateSelect($serieField, selectOptions, "Selecione uma s&eacute;rie");
    }

    var updateSeries = function(){
      resetSelect($serieField);

      if ($instituicaoField.val() && $escolaField.val() && $cursoField.val() && $cursoField.is(':enabled')) {
        $serieField.children().first().html('Aguarde carregando...');

        var urlForGetSeries = getResourceUrlBuilder.buildUrl('/module/DynamicInput/serie', 'series', {
          instituicao_id: $instituicaoField.val(),
          escola_id: $escolaField.val(),
          curso_id: $cursoField.val(),
          ano: $ano.val()
        });

        var options = {
          url: urlForGetSeries,
          dataType: 'json',
          success: handleGetSeries
        };

        getResources(options);
      }

      $serieField.change();
    };

    // bind onchange event
    $cursoField.change(updateSeries);
    $ano.change(updateSeries);

  }); // ready
})(jQuery);
