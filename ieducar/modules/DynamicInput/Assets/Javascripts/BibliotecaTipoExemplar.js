(function($){
  $(document).ready(function(){
    var $bibliotecaField   = getElementFor('biblioteca');
    var $tipoExemplarField = getElementFor('exemplar_tipo');

    var handleGetTiposExemplar = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($tipoExemplarField, selectOptions, "Selecione um tipo de exemplar");
    }

    var updateTiposExemplar = function(){
      resetSelect($tipoExemplarField);

      if ($bibliotecaField.val() && $bibliotecaField.is(':enabled')) {
        $tipoExemplarField.children().first().html('Aguarde carregando...');

        var data = {
          resource      : 'tipos_exemplar',
          biblioteca_id : $bibliotecaField.attr('value')
        };

        var urlForGetTipoExemplar = getResourceUrlBuilder.buildUrl('/module/DynamicInput/TipoExemplar',
                                                                   'tipos_exemplar', data);
        var options = {
          url : urlForGetTipoExemplar,
          dataType : 'json',
          success  : handleGetTiposExemplar
        };

        getResources(options);
      }

      $tipoExemplarField.change();
    };

    // bind onchange event
    $bibliotecaField.change(updateTiposExemplar);

  }); // ready
})(jQuery);
