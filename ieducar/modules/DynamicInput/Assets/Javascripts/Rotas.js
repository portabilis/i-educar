(function($){
  $(document).ready(function(){

    var $anoField   = getElementFor('ano_rota');
    var $rotasField = getElementFor('ref_cod_rota_transporte_escolar');
    var handleGetRotas = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($rotasField, selectOptions, "Selecione uma rota");
    }

    var updateRotas = function(){
      resetSelect($rotasField);

      if ($anoField.val()) {
        $rotasField.children().first().html('Aguarde carregando...');

        var data = {
          ano_rota : $anoField.attr('value')
        };

        var urlForGetRotas = getResourceUrlBuilder.buildUrl('/module/DynamicInput/Rotas',
                                                                 'rotas', data);

        var options = {
          url      : urlForGetRotas,
          dataType : 'json',
          success  : handleGetRotas
        };

        getResources(options);
      }

      $rotasField.change();
    };

    // bind onchange event
    $anoField.change(updateRotas);
    // carrega rotas quando o ano já estiver preenchido
    if ($anoField.val()) {
      updateRotas();
    }

  }); // ready
})(jQuery);