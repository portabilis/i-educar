(function($){
  $(document).ready(function(){
    var $bibliotecaField   = getElementFor('biblioteca');
    var $tipoClienteField  = getElementFor('cliente_tipo');

    var handleGetTiposCliente = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_cliente_tipo');
      updateSelect($tipoClienteField, selectOptions, "Selecione um tipo de cliente");
    }

    var updateTiposCliente = function(){
      resetSelect($tipoClienteField);

      if ($bibliotecaField.val() && $bibliotecaField.is(':enabled')) {
        $tipoClienteField.children().first().html('Aguarde carregando...');

        var path = '/intranet/educar_cliente_tipo_xml.php';
        var urlForGetTipoCliente = getResourceUrlBuilder.buildUrl(path, '', {
                                                            bib : $bibliotecaField.attr('value') });

        var options = {
          url : urlForGetTipoCliente,
          dataType : 'xml',
          success  : handleGetTiposCliente
        };

        getResources(options);
      }

      $tipoClienteField.change();
    };

    // bind onchange event
    $bibliotecaField.change(updateTiposCliente);

  }); // ready
})(jQuery);
