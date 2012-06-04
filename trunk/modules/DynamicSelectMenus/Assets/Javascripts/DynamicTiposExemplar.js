(function($){
  $(document).ready(function(){
    var $bibliotecaField   = $('#ref_cod_biblioteca');
    var $tipoExemplarField = $('#ref_cod_exemplar_tipo');

    var handleGetTiposExemplar = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['tipos_exemplar'], 'id', 'nome');
      updateSelect($tipoExemplarField, selectOptions, "Selecione um tipo de exemplar");
    }

    var updateTiposExemplar = function(){
      resetSelect($tipoExemplarField);

      if ($bibliotecaField.val() && $bibliotecaField.is(':enabled')) {
        $tipoExemplarField.children().first().html('Aguarde carregando...');

        var path = '/module/DynamicSelectMenus/DynamicTiposExemplar';
        var urlForGetTipoExemplar = getResourceUrlBuilder.buildUrl(path, '', {
                                                            resource      : 'tipos_exemplar',
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
