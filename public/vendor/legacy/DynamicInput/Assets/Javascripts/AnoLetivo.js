(function($){
  $(document).ready(function(){

    var $escolaField       = getElementFor('escola');
    var $serieField        = getElementFor('serie');
    var $anoLetivoField    = getElementFor('ano_letivo');

    var handleGetAnoEscolares = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($anoLetivoField, selectOptions, "Selecione um ano escolar");

      $j('#ref_cod_curso').change(selecionaAno);

      function selecionaAno(){
        var numeroElementos = $j('#ano option').length;
        posicaoElemento = numeroElementos - 1;
        var ultimoAno = $('#ano option').eq(posicaoElemento).val();

        $j('#ano option').each(function(){
          var $this = $(this);

          if ($this.val() == ultimoAno) {
            $this.prop('selected', true);
            return false;
          }
        });
      }
    }

    var fetchAnosEscolares = function(resource, data){
      var urlForGetAnosEscolares = getResourceUrlBuilder.buildUrl('/module/DynamicInput/AnoLetivo',
        resource, data);

      var options = {
        url : urlForGetAnosEscolares,
        dataType : 'json',
        success  : handleGetAnoEscolares
      };

      getResources(options);
    }

    var updateAnoEscolares = function(){
      resetSelect($anoLetivoField);

      if ($serieField.length) {
        if ($escolaField.val() && $escolaField.is(':enabled') &&
              $serieField.val() && $serieField.is(':enabled')) {

          $anoLetivoField.children().first().html('Aguarde carregando...');

          var data = {
            escola_id: $escolaField.attr('value'),
            serie_id: $serieField.attr('value')
          };

          fetchAnosEscolares('anos_letivos_escola_serie', data);
        }

      } else if($escolaField.val() && $escolaField.is(':enabled')) {
        $anoLetivoField.children().first().html('Aguarde carregando...');

        var data = {
          escola_id : $escolaField.attr('value'),
        };

        $j("input[name='situacoes_ano_letivo']").each(function(index, input){
          data['situacao_' + $j(input).val()] = true;
        });

        fetchAnosEscolares('anos_letivos', data);
      }

      $anoLetivoField.change();
    };

    // bind onchange event
    $escolaField.change(updateAnoEscolares);
    if ($serieField.length) {
      $serieField.on('change', updateAnoEscolares);
    }
  }); // ready
})(jQuery);
