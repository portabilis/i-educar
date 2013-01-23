(function($){
  $(document).ready(function(){

    var $escolaField       = getElementFor('escola');
    var $anoLetivoField    = getElementFor('ano');

    var handleGetAnoEscolares = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($anoLetivoField, selectOptions, "Selecione um ano escolar");
    }

    var updateAnoEscolares = function(){
      resetSelect($anoLetivoField);

      if ($escolaField.val() && $escolaField.is(':enabled')) {
        $anoLetivoField.children().first().html('Aguarde carregando...');

        var data = {
          escola_id : $escolaField.attr('value'),
        };

        $j("input[name='situacoes_ano_letivo']").each(function(index, input){
          data['situacao_' + $j(input).val()] = true;
        });

        var urlForGetAnosEscolares = getResourceUrlBuilder.buildUrl('/module/DynamicInput/AnoLetivo',
                                                                    'anos_letivos', data);

        var options = {
          url : urlForGetAnosEscolares,
          dataType : 'json',
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
