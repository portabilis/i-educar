(function($){
  $(document).ready(function(){

    var $escolaField       = getElementFor('escola');
    var $anoLetivoField    = getElementFor('ano');

    var handleGetAnoEscolares = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'anos', 'id', 'value');
      updateSelect($anoLetivoField, selectOptions, "Selecione um ano escolar");
    }

    var updateAnoEscolares = function(){
      resetSelect($anoLetivoField);

      if ($escolaField.val() && $escolaField.is(':enabled')) {
        $anoLetivoField.children().first().html('Aguarde carregando...');

        var situacoesAnoLetivo = $j("input[name='situacoes_ano_letivo']").map(function(){
          return $j(this).val();
        });

        var urlForGetAnosEscolares = getResourceUrlBuilder.buildUrl('portabilis_ano_escolar_xml.php', '', {
          escola_id            : $escolaField.attr('value'),
          situacoes_ano_letivo : situacoesAnoLetivo.get()
        });

        var options = {
          url : urlForGetAnosEscolares,
          dataType : 'xml',
          success  : handleGetAnoEscolares
        };

        getResources(options);
      }

      $anoLetivoField.change();
    };

    // bind onchange event
    $escolaField.change(updateAnoEscolares);

  }); // ready
})(jQuery);
