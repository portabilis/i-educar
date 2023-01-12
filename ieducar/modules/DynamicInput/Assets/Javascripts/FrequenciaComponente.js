(function($){
  $(document).ready(function(){

    var $anoField                  = getElementFor('ano');
    var $dataField                  = getElementFor('data');
    var $turmaField                = getElementFor('turma');
    var $componenteCurricularField = getElementFor('componente_curricular');
    var $frequenciaComponenteField = getElementFor('frequencia_componente');

    var $frequenciaComponenteTitleField =  $frequenciaComponenteField[0].parentElement.parentElement.parentElement.children[0].children[0];

    var handleGetComponentesCurriculares = function(response) {
      var selectOptions = jsonResourcesToSelectOptions(response['options']);
      updateSelect($frequenciaComponenteField, selectOptions);

      let tipoPresenca = $turmaField.attr('tipo_presenca');

      if (tipoPresenca == 1 || tipoPresenca == '1') {
        $frequenciaComponenteField.prop('disabled', true);
      }
    }

    function getResultado(xml) {
      $frequenciaComponenteTitleField.innerText = xml.getElementsByTagName("ce")[0]?.getAttribute("resp") == '0' ? 'INFORMAÇÕES:' : 'INFORMAÇÕES:';
    }

    var xml = new ajax(getResultado);
    xml.envia("educar_campo_experiencia_xml.php?tur=" + $turmaField.val());

    var updateComponentesCurriculares = function(){
      resetSelect($frequenciaComponenteField);
      $frequenciaComponenteField.prop('disabled', false);
     

     
      
     
      $("#ref_cod_componente_curricular").change(function(){
        
     
        
        var xml = new ajax(getResultado);
        xml.envia("educar_campo_experiencia_xml.php?tur=" + $turmaField.val());

        var data = {
          ano      : $anoField.attr('value'),
          turma_id : $turmaField.attr('value'),
          componente_id : $componenteCurricularField.attr('value'),
          data_frequencia : $dataField.attr('value').toString()
        };

        var urlForGetFrequenciaComponentes = getResourceUrlBuilder.buildUrl(
          '/module/DynamicInput/frequenciaComponente', 'frequenciasComponente', data
        );

        var options = {
          url : urlForGetFrequenciaComponentes,
          dataType : 'json',
          success  : handleGetComponentesCurriculares
        };

        getResources(options);
      });

      $frequenciaComponenteField.change();
     
    };

    // bind onchange event
    $turmaField.change(updateComponentesCurriculares);
   

    
  }); // ready
})(jQuery);
