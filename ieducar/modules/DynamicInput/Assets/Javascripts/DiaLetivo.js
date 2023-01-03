(function($){
    $(document).ready(function(){
  
      var $anoField                  = getElementFor('ano');
      var $turmaField                = getElementFor('turma');
      var $diaLetivoField            = getElementFor('dia_letivo');
  
      var $componenteCurricularTitleField =  $diaLetivoField[0].parentElement.parentElement.parentElement.children[0].children[0];
  
      var handleGetDiasLetivos = function(response) {
        var selectOptions = jsonResourcesToSelectOptions(response['options']);
        updateSelect($diaLetivoField, selectOptions);
  
        let tipoPresenca = $turmaField.attr('tipo_presenca');
  
        if (tipoPresenca == 1 || tipoPresenca == '1') {
          $diaLetivoField.prop('disabled', true);
        }
      }
  
      function getResultado(xml) {
        $componenteCurricularTitleField.innerText = xml.getElementsByTagName("ce")[0]?.getAttribute("resp") == '0' ? 'INFORMACÕES:' : 'INFORMACÕES:';
      }
  
      var xml = new ajax(getResultado);
      xml.envia("educar_campo_experiencia_xml.php?tur=" + $turmaField.val());
  
      var updateDiasLetivos = function(){
        resetSelect($diaLetivoField);
        $diaLetivoField.prop('disabled', false);
  
        if ($anoField.val() && $turmaField.val() && $turmaField.is(':enabled')) {
         
  
          var xml = new ajax(getResultado);
          xml.envia("educar_campo_experiencia_xml.php?tur=" + $turmaField.val());
  
          var data = {
            ano      : $anoField.attr('value'),
            turma_id : $turmaField.attr('value')
          };
  
          var urlForGetComponentesCurriculares = getResourceUrlBuilder.buildUrl(
            '/module/DynamicInput/diaLetivo', 'diasLetivos', data
          );
  
          var options = {
            url : urlForGetComponentesCurriculares,
            dataType : 'json',
            success  : handleGetDiasLetivos
          };
  
          getResources(options);
        }
  
        $diaLetivoField.change();
      };
  
      // bind onchange event
      $turmaField.change(updateDiasLetivos);
  
    }); // ready
  })(jQuery);
  