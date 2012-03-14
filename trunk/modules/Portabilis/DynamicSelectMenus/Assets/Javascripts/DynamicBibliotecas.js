(function($){
  $(document).ready(function(){
    var $escolaField     = $('#ref_cod_escola');
    var $bibliotecaField = $('#ref_cod_biblioteca');

    var handleGetBibliotecas = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_biblioteca');
      updateSelect($bibliotecaField, selectOptions);
    }

    var updateBibliotecas = function(){
      $bibliotecaField.attr('disabled', 'disabled');

      if ($escolaField.val() && $escolaField.is(':enabled')) {
        getBibliotecasUrl = getResourceUrlBuilder.buildUrl('educar_biblioteca_xml.php', '', {
                                                       esc : $escolaField.attr('value') });

        var options = {
          url : getBibliotecasUrl,
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
