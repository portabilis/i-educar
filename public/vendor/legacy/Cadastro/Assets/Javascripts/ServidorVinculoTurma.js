$j(document).ready(function() {

  const addComponenteCurricular = function(id) {

    const searchPath = '/module/Api/ComponenteCurricular?oper=get&resource=componente_curricular-search';
    const params = {query: id};

    $j.get(searchPath, params, function(dataResponse) {
      handleAddComponenteCurricular(dataResponse, id);
    });
  };
  const obrigarCamposCenso = $j('#obrigar_campos_censo').val() == '1';

  function fiupMultipleSearchSize(){
    $j('.search-field input').css('height', '25px');
  }

  fiupMultipleSearchSize();
  const componentecurricular = $j('#componentecurricular');
  const selecionarTodosElement = $j('#selecionar_todos');
  componentecurricular.trigger('chosen:updated');
  const anoLetivoField = $j('#ano');
  const serieField = $j('#ref_cod_serie');
  const turmaField = $j('#ref_cod_turma');
  const turnoField = $j('#turma_turno_id');
  const professorAreaEspecificaField = $j('#permite_lancar_faltas_componente');

  getRegraAvaliacao();
  getTurnoTurma();

  const handleGetComponenteCurricular = function (dataResponse) {

    setTimeout(function () {
      $j.each(dataResponse['componentecurricular'], function (id, value) {

        // Insere o componente no multipleSearch caso não exista
        if (0 == componentecurricular.children("[value=" + value + "]").length) {
          addComponenteCurricular(value);
        } else {
          componentecurricular.children("[value=" + value + "]").attr('selected', '');
        }
      });

      componentecurricular.trigger('chosen:updated');
    }, 1000);
  };

  const handleAddComponenteCurricular = function (dataResponse, id) {
    componentecurricular.append('<option value="' + id + '"> ' + dataResponse.result[id] + '</option>');
    componentecurricular.children("[value=" + id + "]").attr('selected', '');
    componentecurricular.trigger('chosen:updated');
  };

  $j('#ref_cod_turma').change(function () {
    getTurnoTurma();
  });

  $j('#funcao_exercida').change(function () {
    getTurnoTurma();
  });

  const unidadesCurriculares = (data) => {

    const unidadesCurriculares = $j('#tr_unidades_curriculares');
    const funcaoExercida = $j('#funcao_exercida').val();

    unidadesCurriculares.hide();
    if (!!data && 'estrutura_curricular' in data &&
      data.estrutura_curricular.length > 0 &&
      data.estrutura_curricular.includes("2") &&
      funcaoExercida &&
      $j.inArray($j('#funcao_exercida').val(),["1", "5"]) > -1
    ) {
      filtraUnidadesCurricularesDaTurma(data);
      unidadesCurriculares.show();
    }

    function filtraUnidadesCurricularesDaTurma(data) {
      $j("#unidades_curriculares option").each(function() {
        $j(this).prop('disabled', true)}
      ).trigger('chosen:updated');

      if ('unidade_curricular' in data && !!data.unidade_curricular) {
        let unidadesCurricularesDaTurma = data.unidade_curricular.slice(1,-1).split(',');
        $j("#unidades_curriculares option").each(function()  {
            if(unidadesCurricularesDaTurma.includes($j(this).val())){
              $j(this).prop('disabled', false)
            }
          }).trigger('chosen:updated');
      }
    }
  }

  unidadesCurriculares();

  const getComponenteCurricular = function () {
    const $id = $j('#id');
    if ($id.val() != '') {
      const additionalVars = {
        id: $id.val(),
      };

      const options = {
        url: getResourceUrlBuilder.buildUrl('/module/Api/componenteCurricular', 'componentecurricular-search', additionalVars),
        dataType: 'json',
        data: {},
        success: handleGetComponenteCurricular,
      };

      getResource(options);
    }
  };

  getComponenteCurricular();

  let dependenciaAdministrativa = undefined;

  function getDependenciaAdministrativaEscola(){
    const options = {
      dataType: 'json',
      url: getResourceUrlBuilder.buildUrl(
        '/module/Api/Escola',
        'escola-dependencia-administrativa',
        {escola_id: $j('#ref_cod_escola').val()}
      ),
      success: function (dataResponse) {
        dependenciaAdministrativa = parseInt(dataResponse.dependencia_administrativa);
        verificaObrigatoriedadeTipoVinculo();
      }
    };
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

  selecionarTodosElement.on('change',function(){
    $j('#componentecurricular option').attr('selected', $j(this).prop('checked'));
    componentecurricular.trigger("chosen:updated");
  });

  $j('#funcao_exercida').on('change', verificaObrigatoriedadeTipoVinculo);

  let toggleProfessorAreaEspecifica = function (tipoPresenca) {
    //se o tipo de presença for falta global
    if (tipoPresenca == '1') {
      professorAreaEspecificaField.closest('tr').show();
    } else {
      professorAreaEspecificaField.closest('tr').hide();
      professorAreaEspecificaField.attr('checked', false);
    }
  };

  // turmaField.on('change', function () {
  //   getTurnoTurma();
  // });

  function getTurnoTurma() {
    let turmaId = turmaField.val();

    if (turmaId == '') {
      toggleTurno(0);
      return;
    }

    let params = {id: turmaId};
    let options = {
      url: getResourceUrlBuilder.buildUrl('/module/Api/Turma', 'turma', params),
      dataType: 'json',
      data: {},
      success: handleGetTurnoTurma,
    };

    getResource(options);
  }

  function handleGetTurnoTurma(dataResponse) {
    toggleTurno(dataResponse['turma_turno_id']);
    unidadesCurriculares(dataResponse);
  }

  function toggleTurno (turno_id) {
    turno_id = parseInt(turno_id, 10);

    if (turno_id === 4) { // 4 - Integral
      turnoField.closest('tr').show();
    } else {
      turnoField.closest('tr').hide();
      turnoField.val('');
    }
  }

  serieField.on('change', function(){
    getRegraAvaliacao();
  });

  function getRegraAvaliacao(){
    const serieId = serieField.val();
    const anoLetivo = anoLetivoField.val();

    const params = {
      serie_id: serieId,
      ano_letivo: anoLetivo
    };

    const options = {
      url: getResourceUrlBuilder.buildUrl('/module/Api/Regra', 'regra-serie', params),
      dataType: 'json',
      data: {},
      success: handleGetRegraAvaliacao,
    };
    getResource(options);
  }

  function handleGetRegraAvaliacao(dataResponse){
    toggleProfessorAreaEspecifica(dataResponse["tipo_presenca"]);
  }

  const submitForm = function () {
    let canSubmit = validationUtils.validatesFields();
    if (canSubmit) {
      acao();
    }
  };

  const submitButton = $j('#btn_enviar');
  submitButton.removeAttr('onclick');
  $j(document.formcadastro).removeAttr('onsubmit');
  submitButton.click(submitForm);

});
