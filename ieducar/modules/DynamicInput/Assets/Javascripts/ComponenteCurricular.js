(function($){
  $(document).ready(function(){

    var $anoField                  = getElementFor('ano');
    var $turmaField                = getElementFor('turma');
    var $componenteCurricularField = getElementFor('componente_curricular');
   
    var handleGetComponentesCurriculares = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($componenteCurricularField, selectOptions, "Selecione um componente curricular");
    }

    var updateComponentesCurriculares = function(){
      resetSelect($componenteCurricularField);

      if ($anoField.val() && $turmaField.val() && $turmaField.is(':enabled')) {
        $componenteCurricularField.children().first().html('Aguarde, carregando...');

        function getCampoExperiencia(xml) {
          $result = xml.getElementsByTagName("ce")[0].getAttribute("resp");

          $componenteCurricularField[0].parentElement.parentElement.parentElement.children[0].children[0].innerText =
            $result == '0' ? 'Componente curricular' : 'Campo de experiência';
        }

        var xml = new ajax(getCampoExperiencia);
        xml.envia("educar_campo_experiencia_xml.php?tur=" + $turmaField.val());

        var data = {
          ano      : $anoField.attr('value'),
          turma_id : $turmaField.attr('value')
        };

        var urlForGetComponentesCurriculares = getResourceUrlBuilder.buildUrl(
          '/module/DynamicInput/componenteCurricular', 'componentesCurriculares', data
        );

        var options = {
          url : urlForGetComponentesCurriculares,
          dataType : 'json',
          success  : handleGetComponentesCurriculares
        };

        getResources(options);
      }

      $componenteCurricularField.change();
    };

    // bind onchange event
    $turmaField.change(updateComponentesCurriculares);

  }); // ready
})(jQuery);
