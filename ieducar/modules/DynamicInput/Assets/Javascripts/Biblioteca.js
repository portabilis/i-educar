(function($){
  $(document).ready(function(){
    var $instituicaoField = getElementFor('instituicao');
    var $escolaField      = getElementFor('escola');
    var $bibliotecaField       = getElementFor('biblioteca');

    var handleGetBiblioteca = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($bibliotecaField, selectOptions, "Selecione um biblioteca");
    }

    var updateBiblioteca = function(){
      resetSelect($bibliotecaField);

      if ($instituicaoField.val() && $escolaField.val() && $escolaField.is(':enabled')) {
        $bibliotecaField.children().first().html('Aguarde carregando...');

        var urlForGetBiblioteca = getResourceUrlBuilder.buildUrl('/module/DynamicInput/Biblioteca', 'bibliotecas', {
          escola_id      : $escolaField.attr('value'),
          instituicao_id : $instituicaoField.attr('value'),
        });

        var options = {
          url : urlForGetBiblioteca,
          dataType : 'json',
          success  : handleGetBiblioteca
        };

        getResources(options);
      }

      $bibliotecaField.change();
    };

    // bind onchange event
    $escolaField.change(updateBiblioteca);

  }); // ready
})(jQuery);
