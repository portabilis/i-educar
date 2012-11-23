(function($){
  $(document).ready(function(){

    // serie search expect an id for escola
    var $escolaField = getElementFor('escola');

    var $cursoField = getElementFor('curso');
    var $serieField = getElementFor('serie');

    var handleGetSeries = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_serie');
      updateSelect($serieField, selectOptions, "Selecione uma s&eacute;rie");
    }

    var updateSeries = function(){
      resetSelect($serieField);

      if ($escolaField.val() && $cursoField.val() && $cursoField.is(':enabled')) {
        $serieField.children().first().html('Aguarde carregando...');

        var urlForGetSeries = getResourceUrlBuilder.buildUrl('educar_escola_curso_serie_xml.php', '', {
                                                       esc : $escolaField.attr('value'),
                                                       cur : $cursoField.attr('value') });

        var options = {
          url : urlForGetSeries,
          dataType : 'xml',
          success  : handleGetSeries
        };

        getResources(options);
      }

      $serieField.change();
    };

    // bind onchange event
    $cursoField.change(updateSeries);

  }); // ready
})(jQuery);
