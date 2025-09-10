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
  const lecionaItinerarioField = $j('#leciona_itinerario_tecnico_profissional');
  const areaItinerarioField = $j('#area_itinerario');
  const copiaDeVinculo = $j('#copia').val() == 1 ? true : false;

  getRegraAvaliacao();
  getTurnoTurma();
  validaAreaItinerarioFormativo();

  const handleGetComponenteCurricular = function (dataResponse) {

    setTimeout(function () {
      $j.each(dataResponse['componentecurricular'], function (id, value) {

        // Insere o componente no multipleSearch caso não exista
        // e caso não seja um novo vinculo oriundo de uma cópia
        if (0 == componentecurricular.children("[value=" + value + "]").length && copiaDeVinculo === false) {
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
    validaLecionaItinerarioTecnicoProfissional();
    validaAreaItinerarioFormativo();
  });

  $j('#funcao_exercida').change(function () {
    getTurnoTurma();
  });

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

document.getElementById("funcao_exercida").addEventListener("change", (event) => {

    let value = event.target.value;

    if (value == '1' || value == '5') {
        $j('#componentecurricular').makeRequired();
    } else {
        $j('#componentecurricular').makeUnrequired();
    }

    validaLecionaItinerarioTecnicoProfissional();
    validaAreaItinerarioFormativo();
});

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
    validaLecionaItinerarioTecnicoProfissional(dataResponse);
    validaAreaItinerarioFormativo(dataResponse);
  }

  function toggleTurno (turno_id) {
    turno_edicao = turnoField.val();
    turno_id = parseInt(turno_id, 10);

    if (turno_id === 4) { // 4 - Integral
      turnoField.closest('tr').show();

      getApiResource("/api/period", function (turnos) {
        const campoturno = document.getElementById('turma_turno_id');
        campoturno.options[0].text = 'Carregando';
        setAttributes(campoturno, 'Selecione', false);

        $j.each(turnos, function (id, name) {
          if (id === turno_edicao) {
            campoturno.options[campoturno.options.length] = new Option(name, id, false, true);
          } else {
            campoturno.options[campoturno.options.length] = new Option(name, id, false, false);
          }
        });

      }, {schoolclass: turmaField.val()});

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

  function validaLecionaItinerarioTecnicoProfissional(turmaData = null) {
    const funcaoExercida = $j('#funcao_exercida').val();
    const turmaId = turmaField.val();
    
    // Verifica se a função exercida é uma das obrigatórias (1, 5, 9)
    const funcoesObrigatorias = ['1', '5', '9']; // Docente, Docente titular, Instrutor da Educação Profissional
    
    if ($j.inArray(funcaoExercida, funcoesObrigatorias) === -1 || !turmaId) {
      lecionaItinerarioField.prop('disabled', true);
      lecionaItinerarioField.val('');
      lecionaItinerarioField.makeUnrequired();
      return;
    }
    
    // Primeiro tenta usar os dados já carregados
    if (turmaData && turmaData.organizacao_curricular) {
      handleValidacaoItinerario(turmaData.organizacao_curricular);
      return;
    }
    
    // Se não tem os dados, busca via API
    const params = {id: turmaId};
    const options = {
      url: getResourceUrlBuilder.buildUrl('/module/Api/Turma', 'turma', params),
      dataType: 'json',
      data: {},
      success: function(dataResponse) {
        if (dataResponse && dataResponse.organizacao_curricular) {
          handleValidacaoItinerario(dataResponse.organizacao_curricular);
        } else {
          lecionaItinerarioField.prop('disabled', true);
          lecionaItinerarioField.val('');
          lecionaItinerarioField.makeUnrequired();
        }
      },
    };
    getResource(options);
  }
  
  function handleValidacaoItinerario(organizacaoCurricular) {
    if (!organizacaoCurricular) {
      lecionaItinerarioField.prop('disabled', true);
      lecionaItinerarioField.val('');
      lecionaItinerarioField.makeUnrequired();
      return;
    }
    
    // Verifica se a organização curricular contém Itinerário de formação técnica e profissional
    // Tratando o formato que vem do banco: "{5}" ou "{4,5}" etc
    let organizacaoString = organizacaoCurricular.toString();
    
    // Remove as chaves { } se existirem
    organizacaoString = organizacaoString.replace(/[{}]/g, '');
    
    // Faz o split por vírgula para criar o array
    const organizacaoArray = organizacaoString.split(',').map(item => item.trim());
    
    const temItinerarioTecnico = organizacaoArray.includes('5'); // 5 é o valor para Itinerário de formação técnica e profissional
    
    if (temItinerarioTecnico) {
      lecionaItinerarioField.prop('disabled', false);
      lecionaItinerarioField.makeRequired();
    } else {
      lecionaItinerarioField.prop('disabled', true);
      lecionaItinerarioField.val('');
      lecionaItinerarioField.makeUnrequired();
    }
  }

  function validaAreaItinerarioFormativo(turmaData = null) {
    const funcaoExercida = $j('#funcao_exercida').val();
    const turmaId = turmaField.val();
    
    const funcoesObrigatorias = ['1', '5']; // Docente, Docente titular
    
    if ($j.inArray(funcaoExercida, funcoesObrigatorias) === -1 || !turmaId) {
      areaItinerarioField.prop('disabled', true);
      areaItinerarioField.val([]);
      areaItinerarioField.trigger('chosen:updated');
      areaItinerarioField.makeUnrequired();
      return;
    }
    
    if (turmaData && turmaData.organizacao_curricular) {
      handleValidacaoAreaItinerario(turmaData.organizacao_curricular);
      return;
    }
    
    const params = {id: turmaId};
    const options = {
      url: getResourceUrlBuilder.buildUrl('/module/Api/Turma', 'turma', params),
      dataType: 'json',
      data: {},
      success: function(dataResponse) {
        if (dataResponse && dataResponse.organizacao_curricular) {
          handleValidacaoAreaItinerario(dataResponse.organizacao_curricular);
        } else {
          areaItinerarioField.prop('disabled', true);
          areaItinerarioField.val([]);
          areaItinerarioField.trigger('chosen:updated');
          areaItinerarioField.makeUnrequired();
        }
      },
    };
    getResource(options);
  }
  
  function handleValidacaoAreaItinerario(organizacaoCurricular) {
    if (!organizacaoCurricular) {
      areaItinerarioField.prop('disabled', true);
      areaItinerarioField.val([]);
      areaItinerarioField.trigger('chosen:updated');
      areaItinerarioField.makeUnrequired();
      return;
    }
    
    let organizacaoString = organizacaoCurricular.toString();
    organizacaoString = organizacaoString.replace(/[{}]/g, '');
    const organizacaoArray = organizacaoString.split(',').map(item => item.trim());
    
    const temItinerarioAprofundamento = organizacaoArray.includes('4'); // Itinerário formativo de aprofundamento
    
    if (temItinerarioAprofundamento) {
      areaItinerarioField.prop('disabled', false);
      areaItinerarioField.trigger('chosen:updated');
      areaItinerarioField.makeRequired();
    } else {
      areaItinerarioField.prop('disabled', true);
      areaItinerarioField.val([]);
      areaItinerarioField.trigger('chosen:updated');
      areaItinerarioField.makeUnrequired();
    }
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
