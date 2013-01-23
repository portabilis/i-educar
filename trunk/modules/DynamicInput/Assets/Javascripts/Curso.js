(function($){
  $(document).ready(function(){
    var $escolaField = getElementFor('escola');
    var $cursoField  = getElementFor('curso');

    var handleGetCursos = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_curso');
      updateSelect($cursoField, selectOptions, "Selecione um curso");
    }

    var updateCursos = function(){
      resetSelect($cursoField);

      if ($escolaField.val() && $escolaField.is(':enabled')) {
        $cursoField.children().first().html('Aguarde carregando...');

        var urlForGetCursos = getResourceUrlBuilder.buildUrl('educar_curso_xml.php', '', {
                                                       esc : $escolaField.attr('value') });

        var options = {
          url : urlForGetCursos,
          dataType : 'xml',
          success  : handleGetCursos
        };

        getResources(options);
      }

      $cursoField.change();
    };

    // bind onchange event
    $escolaField.change(updateCursos);

  }); // ready
})(jQuery);
