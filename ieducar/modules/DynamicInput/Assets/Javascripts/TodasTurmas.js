(function($){
  $(document).ready(function(){
    
    var $anoField                  = getElementFor('ano');
    var $turmaField                = getElementFor('ref_cod_turma');

    // var $turmaOcultaField          = document.getElementById("ref_cod_turma");

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

        getResources(options);
      }

      $turmaField.change();
    };

    // var updateTurmaOculta = function(){
    //   $turmaOcultaField.value = $turmaField.val();
    //   console.log($turmaOcultaField.value);

    //   var evt = document.createEvent('HTMLEvents');
    //   evt.initEvent('change', false, true);
    //   $turmaOcultaField.dispatchEvent(evt);
    // };

    // bind onchange event
    $anoField.change(updateTodasTurmas);
    // $turmaField.change(updateTurmaOculta);

  }); // ready
})(jQuery);
