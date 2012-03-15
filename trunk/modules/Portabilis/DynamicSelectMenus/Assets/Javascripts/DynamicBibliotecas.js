(function($){
  $(document).ready(function(){
    var $escolaField     = $('#ref_cod_escola');
    var $bibliotecaField = $('#ref_cod_biblioteca');

    var handleGetBibliotecas = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_biblioteca');
      updateSelect($bibliotecaField, selectOptions, "Selecione uma biblioteca");
    }

    var updateBibliotecas = function(){
      resetSelect($bibliotecaField);

      if ($escolaField.val() && $escolaField.is(':enabled')) {
        urlForGetBibliotecas = getResourceUrlBuilder.buildUrl('educar_biblioteca_xml.php', '', {
                                                       esc : $escolaField.attr('value') });

        var options = {
          url : urlForGetBibliotecas,
          dataType : 'xml',
          success  : handleGetBibliotecas
        };

        getResources(options, handleGetBibliotecas);
      }

      $bibliotecaField.change();
    };

    // bind onchange event
    $escolaField.change(updateBibliotecas);

  }); // ready
})(jQuery);
