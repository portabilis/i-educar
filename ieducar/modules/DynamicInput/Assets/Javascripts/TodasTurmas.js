(function($){
  $(document).ready(function(){
    
    var $anoField                  = getElementFor('ano');
    var $turmaField                = getElementFor('todas_Turmas');

    var handleGetTodasTurmas = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($turmaField, selectOptions, "Selecione uma turma");
    }

    var updateTodasTurmas = function(){
      resetSelect($turmaField);

      if ($anoField.val() && $anoField.is(':enabled')) {
        $turmaField.children().first().html('Aguarde, carregando...');

        var data = {
          ano      : $anoField.attr('value')
        };

        var urlForGetTodasTurmas = getResourceUrlBuilder.buildUrl(
          '/module/DynamicInput/todasTurmas', 'todasTurmas', data
        );

        var options = {
          url : urlForGetTodasTurmas,
          dataType : 'json',
          success  : handleGetTodasTurmas
        };
        console.log(options);
        getResources(options);
      }

      console.log($turmaField);
      // $turmaField.change();
      var evt = document.createEvent('HTMLEvents');
      evt.initEvent('change', false, true);
      $turmaField.dispatchEvent(evt);
    };

    // bind onchange event
    $anoField.change(updateTodasTurmas);

  }); // ready
})(jQuery);
