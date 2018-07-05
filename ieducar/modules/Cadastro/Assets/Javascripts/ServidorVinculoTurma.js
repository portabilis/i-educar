$j(document).ready(function() {

  var $turmaField = getElementFor('turma');
  var $anoField = getElementFor('ano');
  var $instituicaoField = getElementFor('instituicao');

  let obrigarCamposCenso = $j('#obrigar_campos_censo').val() == '1';

  var handleGetComponentesArea = function(response){
    let componentes = Object.keys(response['options']||{});
    $j('#componentecurricular').val(componentes.concat($j('#componentecurricular').val()||[])).trigger('chosen:updated');
    $j("#dialog_area_conhecimento").dialog("close");
  }

  var preenchePorAreaConhecimento = function(){
    let areaConhecimento = $j('#area_conhecimento').val();
    if (!areaConhecimento) {
      alert('Área de conhecimento deve ser preenchida');
      return false;
    }
    urlForGetAreaConhecimento = getResourceUrlBuilder.buildUrl('/module/Api/ComponenteCurricular', 'componentes-curriculares-for-multiple-search', {
      turma_id  : $turmaField.val(),
      ano: $anoField.val(),
      instituicao_id: $instituicaoField.val(),
      area_conhecimento_id: areaConhecimento
    });

    var options = {
      url : urlForGetAreaConhecimento,
      dataType : 'json',
      success  : handleGetComponentesArea
    };

    getResources(options);
  }

  $j('body').append(htmlFormModal());

  $j("#dialog_area_conhecimento").dialog({
    autoOpen: false,
    height: 'auto',
    width: 'auto',
    modal: true,
    resizable: false,
    draggable: false,
    title: 'Selecionar por área de conhecimento',
    buttons: {
        "Preencher": preenchePorAreaConhecimento,
        "Cancelar": function(){
            $j(this).dialog("close");
        }
    },
    create: function () {
        $j(this)
            .closest(".ui-dialog")
            .find(".ui-button-text:first")
            .addClass("btn-green");
    },
    close: function () {
        $j('#area_conhecimento').val("");
    }
  });


  var handleGetAreaConhecimento = function(response) {
    $j('#area_conhecimento').html('').val('');
    var selectOptions = response['options'];
    for(let key in selectOptions){
      if (selectOptions.hasOwnProperty(key)) {
        $j('#area_conhecimento').append($j('<option/>').val(key).text(selectOptions[key]));
      }
    }
    $j("#dialog_area_conhecimento").dialog("open");
  }

  function modalOpen(){
    var turma            = $turmaField.val();

    if (!turma) {
      alert('Informe uma turma');
      return false;
    }

    urlForGetAreaConhecimento = getResourceUrlBuilder.buildUrl('/module/Api/AreaConhecimento', 'areaconhecimento-turma', {
      turma_id  : turma
    });

    var options = {
      url : urlForGetAreaConhecimento,
      dataType : 'json',
      success  : handleGetAreaConhecimento
    };

    getResources(options);
  }

  function htmlFormModal(){
    return `<div id="dialog_area_conhecimento">
              <form>
                <label for="area_conhecimento">Área de conhecimento</label>
                <select name="area_conhecimento" id="area_conhecimento">
              </form>
            </div>`;
  }

  let $linkModalArea = $j('<a/>').attr('href','#').text('Selecionar por área de conhecimento').on('click', modalOpen);

  $j('#tr_componentecurricular td:last-child').append($linkModalArea);

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

      // Insere o componente no multipleSearch caso não exista
      if (0 == $componentecurricular.children("[value=" + value + "]").length) {
        addComponenteCurricular(value);
      } else {
        $componentecurricular.children("[value=" + value + "]").attr('selected', '');
      }
    });

    $componentecurricular.trigger('chosen:updated');
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