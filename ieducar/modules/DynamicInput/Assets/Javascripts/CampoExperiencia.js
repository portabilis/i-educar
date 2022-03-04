(function($){
  $(document).ready(function(){

    var $campoExperienciaField = getElementFor('campoExperiencia');
    var $parentCampoExeperienciaField = $campoExperienciaField[0].parentElement.parentElement.parentElement;

    $parentCampoExeperienciaField.hidden = true;

    var $frequenciaField = getElementFor('frequencia');

    var updateCampoExperiencia = function(){
      if ($frequenciaField.val()) {

        function getCampoExperiencia(xml) {
          $result = xml.getElementsByTagName("ce")[0].getAttribute("resp");
          $parentCampoExeperienciaField.hidden = $result == 0 ? true : false;
        }

        var xml_disciplina = new ajax(getCampoExperiencia);
        xml_disciplina.envia("educar_campo_experiencia_xml.php?freq=" + $frequenciaField.val());
      }
    };

    // bind onchange event
    $frequenciaField.change(updateCampoExperiencia);

  }); // ready
})(jQuery);
