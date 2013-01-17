(function($){
  $(document).ready(function(){

    // turma search expect an id for escola
    var $escolaField = getElementFor('escola');

    var $serieField = getElementFor('serie');
    var $turmaField = getElementFor('turma');

    var handleGetTurmas = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'query', 'cod_turma');
      updateSelect($turmaField, selectOptions, "Selecione uma turma");
    }

    var updateTurmas = function(){
      resetSelect($turmaField);

      if ($escolaField.val() && $serieField.val() && $serieField.is(':enabled')) {
        $turmaField.children().first().html('Aguarde carregando...');

        var urlForGetTurmas = getResourceUrlBuilder.buildUrl('educar_turma_xml.php', '', {
                                                       esc : $escolaField.attr('value'),
                                                       ser : $serieField.attr('value') });

        var options = {
          url : urlForGetTurmas,
          dataType : 'xml',
          success  : handleGetTurmas
        };

        getResources(options);
      }

      $turmaField.change();
    };

    // bind onchange event
    $serieField.change(updateTurmas);

  }); // ready
})(jQuery);
