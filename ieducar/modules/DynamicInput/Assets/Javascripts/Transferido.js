(function($){
  $(document).ready(function(){

    var $anoField       = getElementFor('ano');
    var $turmaField     = getElementFor('turma');
    var $matriculaField = getElementFor('matricula');

    var handleGetTransferidos = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($matriculaField, selectOptions, "Selecione uma matricula");
    }

    var updateTransferidos = function(){
      resetSelect($matriculaField);

      if ($anoField.val() && $turmaField.val() && $turmaField.is(':enabled')) {
        $matriculaField.children().first().html('Aguarde carregando...');

        var data = {
          ano      : $anoField.attr('value'),
          turma_id : $turmaField.attr('value')
        };

        var urlForGetTransferidos = getResourceUrlBuilder.buildUrl('/module/DynamicInput/Transferido',
                                                                 'transferidos', data);

        var options = {
          url      : urlForGetTransferidos,
          dataType : 'json',
          success  : handleGetTransferidos
        };

        getResources(options);
      }

      $matriculaField.change();
    };

    // bind onchange event
    $turmaField.change(updateTransferidos);

  }); // ready
})(jQuery);
