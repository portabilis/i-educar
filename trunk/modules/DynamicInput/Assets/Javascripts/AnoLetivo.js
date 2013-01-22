(function($){
  $(document).ready(function(){

    var $escolaField = getElementFor('escola');
    var $anoField    = getElementFor('ano');

    var handleGetAnoEscolares = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'anos', 'id', 'value');
      updateSelect($anoField, selectOptions, "Selecione um ano escolar");
    }

    var updateAnoEscolares = function(){
      resetSelect($anoField);

      if ($escolaField.val() && $escolaField.is(':enabled')) {
        $anoField.children().first().html('Aguarde carregando...');

        var urlForGetAnosEscolares = getResourceUrlBuilder.buildUrl('portabilis_ano_escolar_xml.php', '', {
          escola_id    : $escolaField.attr('value')
        });

        var options = {
          url : urlForGetAnosEscolares,
          dataType : 'xml',
          success  : handleGetAnoEscolares
        };

        getResources(options);
      }

      $anoField.change();
    };

    // bind onchange event
    $escolaField.change(updateAnoEscolares);

  }); // ready
})(jQuery);
