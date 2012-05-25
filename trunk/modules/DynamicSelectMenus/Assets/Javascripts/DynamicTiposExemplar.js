(function($){
  $(document).ready(function(){
    var $bibliotecaField   = $('#ref_cod_biblioteca');
    var $tipoExemplarField = $('#ref_cod_exemplar_tipo');

    var handleGetTiposExemplar = function(resources) {

      // #FIXME criar metodo jsonResourcesToSelectOptions

      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_biblioteca');
      updateSelect($tipoExemplarField, selectOptions, "Selecione um tipo de exemplar");
    }

    var updateTiposExemplar = function(){
      resetSelect($tipoExemplarField);

      if ($bibliotecaField.val() && $bibliotecaField.is(':enabled')) {
        var path = 'module/DynamicSelectMenus/DynamicTiposExemplar';
        var urlForGetTipoExemplar = getResourceUrlBuilder.buildUrl(path, '', {
                                                            biblioteca_id : $bibliotecaField.attr('value') });

        var options = {
          url : urlForGetTipoExemplar,
          dataType : 'json',
          success  : handleGetTiposExemplar
        };

        getResources(options, handleGetTiposExemplar);
      }

      $tipoExemplarField.change();
    };

    // bind onchange event
    $bibliotecaField.change(updateTiposExemplar);

  }); // ready
})(jQuery);
