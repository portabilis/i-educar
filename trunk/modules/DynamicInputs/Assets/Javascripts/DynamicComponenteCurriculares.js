(function($){
  $(document).ready(function(){

    var $anoField                  = getElementFor('ano');
    var $turmaField                = getElementFor('turma');
    var $componenteCurricularField = getElementFor('componente_curricular');

    var handleGetComponenteCurriculares = function(resources) {
      var selectOptions = xmlResourcesToSelectOptions(resources, 'componentes_curriculares', 'id', 'value');
      updateSelect($componenteCurricularField, selectOptions, "Selecione um componente curricular");
    }

    var updateComponenteCurriculares = function(){
      resetSelect($componenteCurricularField);

      if ($anoField.val() && $turmaField.val() && $turmaField.is(':enabled')) {
        $componenteCurricularField.children().first().html('Aguarde carregando...');

        var urlForGetComponenteCurriculares = getResourceUrlBuilder.buildUrl('portabilis_componente_curricular_xml.php', '', {
          ano_escolar : $anoField.attr('value'),
          turma_id    : $turmaField.attr('value') 
        });

        var options = {
          url : urlForGetComponenteCurriculares,
          dataType : 'xml',
          success  : handleGetComponenteCurriculares
        };

        getResources(options);
      }

      $componenteCurricularField.change();
    };

    // bind onchange event
    $turmaField.change(updateComponenteCurriculares);

  }); // ready
})(jQuery);
