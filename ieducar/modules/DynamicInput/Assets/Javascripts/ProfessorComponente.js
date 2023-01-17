(function($){
    $(document).ready(function(){
      
  
      var $anoField                  = getElementFor('ano');
      var $turmaField                = getElementFor('turma');
      var $componenteCurricularField = getElementFor('componente_curricular');
      var $professorComponenteField = getElementFor('professor_componente');
  
      var $professorComponenteTitleField =  $professorComponenteField[0].parentElement.parentElement.parentElement.children[0].children[0];
  
      var handleGetProfessores = function(response) {
        var selectOptions = jsonResourcesToSelectOptions(response['options']);
        updateSelect($professorComponenteField, selectOptions, "Selecione um professor");
      }
  
        $professorComponenteTitleField.innerText =  'Professor:';
     
  
    
  
      var updateProfessores = function(){
      


          $professorComponenteField.children().first().html('Aguarde, carregando...');
       

          
  
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
            success  : handleGetProfessores
          };
  
          getResources(options);

  
        $professorComponenteField.change();
       
      };
  
     updateProfessores();
     

     $("#ref_cod_componente_curricular").change(function(){


      $professorComponenteField.children().first().html('Aguarde, carregando...');
   

      

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
        success  : handleGetProfessores
      };

      getResources(options);

      $professorComponenteField.change();

    });

    
     
  
      
    }); // ready
  })(jQuery);
  