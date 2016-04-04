$j(document).ready(function() {
  
  function fiupMultipleSearchSize(){
    $j('.search-field input').css('height', '25px');  
  }

  fiupMultipleSearchSize();
  $componentecurricular = $j('#componentecurricular');
  $selecionarTodosElement = $j('#selecionar_todos');
  $componentecurricular.trigger('chosen:updated');
  $serieField = $j('#ref_cod_serie');
  $professorAreaEspecificaField = $j('#permite_lancar_faltas_componente');

  getRegraAvaliacao();

  var handleGetComponenteCurricular = function(dataResponse) {
    
    $j.each(dataResponse['componentecurricular'], function(id, value) {
      $componentecurricular.children("[value=" + value + "]").attr('selected', '');
    });

    $componentecurricular.trigger('chosen:updated');
  }

  var getComponenteCurricular = function() {
    var $id = $j('#id');
    if ($id.val()!='') {    
      var additionalVars = {
        id : $id.val(),
      };

      var options = {
        url      : getResourceUrlBuilder.buildUrl('/module/Api/componenteCurricular', 'componentecurricular-search', additionalVars),
        dataType : 'json',
        data     : {},
        success  : handleGetComponenteCurricular,
      };

      getResource(options);
    }
  }

  getComponenteCurricular();

  $selecionarTodosElement.on('change',function(){
    $j('#componentecurricular option').attr('selected', $j(this).prop('checked'));
    $componentecurricular.trigger("chosen:updated");
  });

  $serieField.on('change', function(){
    getRegraAvaliacao();
  });

  var toggleProfessorAreaEspecifica = function(tipoPresenca){
    //se o tipo de presença for falta global
    if(tipoPresenca == '1'){
      $professorAreaEspecificaField.closest('tr').show();
    }else{
      $professorAreaEspecificaField.closest('tr').hide();
      $professorAreaEspecificaField.attr('checked', false);
    }
  };


  function getRegraAvaliacao(){
    $serieId = $serieField.val();

    var params = { serie_id: $serieId };

    var options = {
      url      : getResourceUrlBuilder.buildUrl('/module/Api/Regra', 'regra-serie', params),
      dataType : 'json',
      data     : {},
      success  : handleGetRegraAvaliacao,
    };
    getResource(options);
  };

  function handleGetRegraAvaliacao(dataResponse){
    toggleProfessorAreaEspecifica(dataResponse["tipo_presenca"]);
  }

});