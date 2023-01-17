(function($){
    $(document).ready(function(){
  
      var $anoField                  = getElementFor('ano');
      var $turmaField                = getElementFor('turma');
      var $componenteCurricularField = getElementFor('componente_curricular');
      var $professorComponenteField = getElementFor('professor_componente');
  
      var $professorComponenteTitleField =  $professorComponenteField[0].parentElement.parentElement.parentElement.children[0].children[0];
  
      var handleGetComponentesCurriculares = function(response) {
        var selectOptions = jsonResourcesToSelectOptions(response['options']);
        updateSelect($professorComponenteField, selectOptions);
   
        let tipoPresenca = $turmaField.attr('tipo_presenca');
  
        if (tipoPresenca == 1 || tipoPresenca == '1') {
          $professorComponenteField.prop('disabled', true);
        }
      }
  
      function getResultado(xml) {
        $professorComponenteTitleField.innerText = xml.getElementsByTagName("ce")[0]?.getAttribute("resp") == '0' ? 'Professor:' : 'Professor:';
      }
  
      var xml = new ajax(getResultado);
      xml.envia("educar_campo_experiencia_xml.php?tur=" + $turmaField.val());
  
      var updateProfessores = function(){
        resetSelect($professorComponenteField);
        $professorComponenteField.prop('disabled', false);
       
  
       
        
       
       
          
       
          
          var xml = new ajax(getResultado);
          xml.envia("educar_campo_experiencia_xml.php?tur=" + $turmaField.val());
  
          var data = {
            ano      : $anoField.attr('value'),
            turma_id : $turmaField.attr('value'),
            componente_id : $componenteCurricularField.attr('value')
           
          };
  
          var urlForGetFrequenciaComponentes = getResourceUrlBuilder.buildUrl(
            '/module/DynamicInput/professorComponente', 'professoresComponente', data
          );
  
          var options = {
            url : urlForGetFrequenciaComponentes,
            dataType : 'json',
            success  : handleGetComponentesCurriculares
          };
  
          getResources(options);
       
  
        $professorComponenteField.change();
       
      };
  
      // bind onchange event
      $componenteCurricularField.change(updateProfessores);
     
  
      
    }); // ready
  })(jQuery);
  