(function($){
  $(document).ready(function(){

    var $anoField       = getElementFor('ano');
    var $turmaField     = getElementFor('turma');
    var $matriculaField = getElementFor('matricula');

    var handleGetMatriculas = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($matriculaField, selectOptions, "Selecione uma matricula");
    }

    var updateMatriculas = function(){
      resetSelect($matriculaField);

      if ($anoField.val() && $turmaField.val() && $turmaField.is(':enabled')) {
        $matriculaField.children().first().html('Aguarde carregando...');

        var data = {
          ano      : $anoField.attr('value'),
          turma_id : $turmaField.attr('value')
        };

        var urlForGetMatriculas = getResourceUrlBuilder.buildUrl('/module/DynamicInput/Matricula',
                                                                 'matriculas', data);

        var options = {
          url      : urlForGetMatriculas,
          dataType : 'json',
          success  : handleGetMatriculas
        };

        getResources(options);
      }

      $matriculaField.change();
    };

    // bind onchange event
    $turmaField.change(updateMatriculas);

  }); // ready
})(jQuery);
