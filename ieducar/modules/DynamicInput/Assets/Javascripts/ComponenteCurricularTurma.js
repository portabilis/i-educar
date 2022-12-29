(function($){
  $(document).ready(function(){

    var $anoField                  = getElementFor('ano');
    var $turmaField                = getElementFor('turma');
    var $componenteCurricularField = getElementFor('componente_curricular');
    var $componenteCurricularTurmaField = getElementFor('componente_curricular_turma');

    var $componenteCurricularTitleField =  $componenteCurricularTurmaField[0].parentElement.parentElement.parentElement.children[0].children[0];

    var handleGetComponentesCurriculares = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($componenteCurricularTurmaField, selectOptions);

      let tipoPresenca = $turmaField.attr('tipo_presenca');

      if (tipoPresenca == 1 || tipoPresenca == '1') {
        $componenteCurricularTurmaField.prop('disabled', true);
      }
    }

    function getResultado(xml) {
      $componenteCurricularTitleField.innerText = xml.getElementsByTagName("ce")[0]?.getAttribute("resp") == '0' ? 'Carga Horária do Componente:' : 'Carga Horária do Componente:';
    }

    var xml = new ajax(getResultado);
    xml.envia("educar_campo_experiencia_xml.php?tur=" + $turmaField.val());

    var updateComponentesCurriculares = function(){
      resetSelect($componenteCurricularTurmaField);
     

     
        
     
      $("#ref_cod_componente_curricular").change(function(){
        
     

        var xml = new ajax(getResultado);
        xml.envia("educar_campo_experiencia_xml.php?tur=" + $turmaField.val());

        var data = {
          ano      : $anoField.attr('value'),
          turma_id : $turmaField.attr('value'),
          componente_id : $componenteCurricularField.attr('value')
        };

        var urlForGetComponentesCurriculares = getResourceUrlBuilder.buildUrl(
          '/module/DynamicInput/componenteCurricularTurma', 'componentesCurricularesTurma', data
        );

        var options = {
          url : urlForGetComponentesCurriculares,
          dataType : 'json',
          success  : handleGetComponentesCurriculares
        };

        getResources(options);
      });

      $componenteCurricularTurmaField.change();
     
    };

    // bind onchange event
    $turmaField.change(updateComponentesCurriculares);

    
  }); // ready
})(jQuery);
