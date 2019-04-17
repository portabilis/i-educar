$j(document).ready(function() {

  let obrigarCamposCenso = $j('#obrigar_campos_censo').val() == '1';

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

    setTimeout(function () {
      $j.each(dataResponse['componentecurricular'], function(id, value) {

        // Insere o componente no multipleSearch caso não exista
        if (0 == $componentecurricular.children("[value=" + value + "]").length) {
          addComponenteCurricular(value);
        } else {
          $componentecurricular.children("[value=" + value + "]").attr('selected', '');
        }
      });

      $componentecurricular.trigger('chosen:updated');
    }, 1000);
  }

  var handleAddComponenteCurricular = function(dataResponse, id) {
    $componentecurricular.append('<option value="' + id + '"> ' + dataResponse.result[id] + '</option>');
    $componentecurricular.children("[value=" + id + "]").attr('selected', '');
    $componentecurricular.trigger('chosen:updated');
  }

  var addComponenteCurricular = function(id) {

    var searchPath = '/module/Api/ComponenteCurricular?oper=get&resource=componente_curricular-search';
    var params     = { query : id };

    $j.get(searchPath, params, function(dataResponse) {
      handleAddComponenteCurricular(dataResponse, id);
    });
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

  var dependenciaAdministrativa = undefined;

  function getDependenciaAdministrativaEscola(){
    var options = {
      dataType : 'json',
      url : getResourceUrlBuilder.buildUrl(
        '/module/Api/Escola',
        'escola-dependencia-administrativa',
        {escola_id : $j('#ref_cod_escola').val()}
      ),
      success : function(dataResponse) {
        dependenciaAdministrativa = parseInt(dataResponse.dependencia_administrativa);
        verificaObrigatoriedadeTipoVinculo();
      }
    }
    getResource(options);
  }

  let verificaObrigatoriedadeTipoVinculo = () => {
    $j('#tipo_vinculo').makeUnrequired();
    if (obrigarCamposCenso &&
        dependenciaAdministrativa >= 1 &&
        dependenciaAdministrativa <= 3 &&
        $j.inArray($j('#funcao_exercida').val(),["1", "5", "6"]) > -1){
      $j('#tipo_vinculo').makeRequired();
    }
  };

  $j('#ref_cod_escola').on('change', getDependenciaAdministrativaEscola);
  getDependenciaAdministrativaEscola();

  $selecionarTodosElement.on('change',function(){
    $j('#componentecurricular option').attr('selected', $j(this).prop('checked'));
    $componentecurricular.trigger("chosen:updated");
  });

  $j('#funcao_exercida').on('change', verificaObrigatoriedadeTipoVinculo);

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

  var submitForm = function(){
    let canSubmit = validationUtils.validatesFields();
    if (canSubmit) {
      acao();
    }
  }

  var $submitButton      = $j('#btn_enviar');
  $submitButton.removeAttr('onclick');
  $j(document.formcadastro).removeAttr('onsubmit');
  $submitButton.click(submitForm);

});
