(function($){
  $(document).ready(function(){
    var $escolaField     = getElementFor('escola');
    var $bibliotecaField = getElementFor('biblioteca');

    var handleGetBibliotecas = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_biblioteca');
      updateSelect($bibliotecaField, selectOptions, "Selecione uma biblioteca");
    }

    var updateBibliotecas = function(){
      resetSelect($bibliotecaField);

      if ($escolaField.val() && $escolaField.is(':enabled')) {
        $bibliotecaField.children().first().html('Aguarde carregando...');

        var urlForGetBibliotecas = getResourceUrlBuilder.buildUrl('educar_biblioteca_xml.php', '', {
                                                       esc : $escolaField.attr('value') });

        var options = {
          url : urlForGetBibliotecas,
          dataType : 'xml',
          success  : handleGetBibliotecas
        };

        getResources(options);
      }

      $bibliotecaField.change();
    };

    // bind onchange event
    $escolaField.change(updateBibliotecas);

  }); // ready
})(jQuery);
