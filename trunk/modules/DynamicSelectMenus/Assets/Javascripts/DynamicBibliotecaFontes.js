(function($){
  $(document).ready(function(){
    var $bibliotecaField = getElementFor('biblioteca');
    var $fonteField      = getElementFor('fonte');

    var handleGetFontes = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_fonte');
      updateSelect($fonteField, selectOptions, "Selecione uma fonte");
    }

    var updateFontes = function(){
      resetSelect($fonteField);

      if ($bibliotecaField.val() && $bibliotecaField.is(':enabled')) {
        $fonteField.children().first().html('Aguarde carregando...');

        var urlForGetFontes = getResourceUrlBuilder.buildUrl('educar_fonte_xml.php', '', {
                                                       bib : $bibliotecaField.attr('value') });

        var options = {
          url : urlForGetFontes,
          dataType : 'xml',
          success  : handleGetFontes
        };

        getResources(options);
      }

      $fonteField.change();
    };

    // bind onchange event
    $bibliotecaField.change(updateFontes);

  }); // ready
})(jQuery);
