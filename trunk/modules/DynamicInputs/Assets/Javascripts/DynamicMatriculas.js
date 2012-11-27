(function($){
  $(document).ready(function(){

    var $anoField       = getElementFor('ano');
    var $turmaField     = getElementFor('turma');
    var $matriculaField = getElementFor('matricula');

    var handleGetMatriculas = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'matriculas', 'id', 'value');
      updateSelect($matriculaField, selectOptions, "Selecione uma matricula");
    }

    var updateMatriculas = function(){
      resetSelect($matriculaField);

      if ($anoField.val() && $turmaField.val() && $turmaField.is(':enabled')) {
        $matriculaField.children().first().html('Aguarde carregando...');

        var urlForGetMatriculas = getResourceUrlBuilder.buildUrl('portabilis_alunos_matriculados_xml.php', '', {
                                                       ano_escolar : $anoField.attr('value'),
                                                       turma_id    : $turmaField.attr('value') });

        var options = {
          url : urlForGetMatriculas,
          dataType : 'xml',
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
