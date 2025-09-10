//abas
let html = '<div id="tabControl"><ul>'
html += '<li><div id="tab1" class="turmaTab"> <span class="tabText">Dados gerais</span></div></li>';
html += '<li><div id="tab2" class="turmaTab"> <span class="tabText">Dados adicionais</span></div></li>';
if ($j('#turno_parcial').val() === 'S') {
  html += '<li><div id="tab3" class="turmaTab"> <span class="tabText">Dados dos Turnos Parciais</span></div></li>';
}
html += '</ul></div>';

$j('td .formdktd').append(html);
$j('td .formdktd b').remove();
$j('.tablecadastro td .formdktd div').remove();
$j('#tab1').addClass('turmaTab-active').removeClass('turmaTab');
$j('#ref_cod_disciplina_dispensada').css('maxWidth', '600px');

// Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
$j('#codigo_inep_educacenso').closest('tr').attr('id','tr_codigo_inep_educacenso');

// Adiciona um ID à linha que termina o formulário para parar de esconder os campos
$j('.tableDetalheLinhaSeparador').closest('tr').attr('id','stop');

// Pega o número dessa linha
linha_inicial_tipo = $j('#tr_codigo_inep_educacenso').index()-3;
linha_inicial_turno_parcial = $j('#tr_horario_funcionamento_turno_matutino').index()-3;

// hide nos campos das outras abas (deixando só os campos da primeira aba)
$j('.tablecadastro >tbody  > tr').each(function(index, row) {
  if (index>=linha_inicial_tipo){
    if (row.id!='stop')
      row.hide();
    else{
      return false;
    }
  }
});

var modoCadastro = $j('#retorno').val() == 'Novo';
let obrigarCamposCenso = $j('#obrigar_campos_censo').val() == '1';

let habilitaFormacaoAlternancia = ()=>{
  $j('#formacao_alternancia').makeUnrequired();

  if (obrigarCamposCenso) {
    $j('#formacao_alternancia').makeRequired();
  }
}

let verificaHorariosTurnoParcial = ()=>{
  if (!obrigarCamposCenso) {
    return true;
  }
  $j('#hora_inicial_matutino').makeUnrequired();
  $j('#hora_inicio_intervalo_matutino').makeUnrequired();
  $j('#hora_fim_intervalo_matutino').makeUnrequired();
  $j('#hora_final_matutino').makeUnrequired();
  $j('#hora_inicial_vespertino').makeUnrequired();
  $j('#hora_inicio_intervalo_vespertino').makeUnrequired();
  $j('#hora_fim_intervalo_vespertino').makeUnrequired();
  $j('#hora_final_vespertino').makeUnrequired();

  if ($j('#tipo_mediacao_didatico_pedagogico').val() == 1) {
    $j('#hora_inicial_matutino').prop('disabled', false).makeRequired();
    $j('#hora_inicio_intervalo_matutino').prop('disabled', false).makeRequired();
    $j('#hora_fim_intervalo_matutino').prop('disabled', false).makeRequired();
    $j('#hora_final_matutino').prop('disabled', false).makeRequired();
    $j('#hora_inicial_vespertino').prop('disabled', false).makeRequired();
    $j('#hora_inicio_intervalo_vespertino').prop('disabled', false).makeRequired();
    $j('#hora_fim_intervalo_vespertino').prop('disabled', false).makeRequired();
    $j('#hora_final_vespertino').prop('disabled', false).makeRequired();
  } else {
    $j('#hora_inicial_matutino').prop('disabled', true).val("");
    $j('#hora_inicio_intervalo_matutino').prop('disabled', true).val("");
    $j('#hora_fim_intervalo_matutino').prop('disabled', true).val("");
    $j('#hora_final_matutino').prop('disabled', true).val("");
    $j('#hora_inicial_vespertino').prop('disabled', true).val("");
    $j('#hora_inicio_intervalo_vespertino').prop('disabled', true).val("");
    $j('#hora_fim_intervalo_vespertino').prop('disabled', true).val("");
    $j('#hora_final_vespertino').prop('disabled', true).val("");
  }
}

let habilitaFormaOrganizacaoTurma = ()=> {
  const etapasInvalidas = ['1', '2', '3', '24', '62'];
  const tipoAtendimento = $j('#tipo_atendimento').val() || [];
  const escolarizacao = tipoAtendimento.includes('0');
  const etapaEducacenso = $j('#etapa_educacenso').val()

  $j('#formas_organizacao_turma').makeUnrequired();
  if (obrigarCamposCenso &&
      escolarizacao &&
      etapaEducacenso &&
     !etapasInvalidas.includes(etapaEducacenso)
  ) {
    $j('#formas_organizacao_turma').makeRequired();
  }

  $j("#formas_organizacao_turma").prop('disabled', false);

  if (!escolarizacao || !etapaEducacenso || etapasInvalidas.includes(etapaEducacenso)) {
    $j("#formas_organizacao_turma").prop('disabled', true).val("");
  }
}


let verificaLocalFuncionamentoDiferenciado = () => {
  $j('#local_funcionamento_diferenciado').makeUnrequired();
  let habilitaCampo = [1,2].includes(+($j('#tipo_mediacao_didatico_pedagogico').val()));
  $j('#local_funcionamento_diferenciado').prop('disabled', !habilitaCampo);

  if (habilitaCampo) {
    if (obrigarCamposCenso) {
      $j('#local_funcionamento_diferenciado').makeRequired();
    }
  } else {
    $j('#local_funcionamento_diferenciado').val("");
  }
}

function atualizaOpcoesTipoAtendimento() {
  let valores = $j('#tipo_atendimento').val() || [];
  const $options = $j('#tipo_atendimento option');

  if (valores.length > 1 && (valores.includes('') || valores.includes('null'))) {
    valores = valores.filter(v => v !== '' && v !== 'null');
    $j('#tipo_atendimento').val(valores).trigger('chosen:updated');
  }

  $options.prop('disabled', false);

  $options.each(function() {
    if ($j(this).val() === '' || $j(this).val() === 'null') {
      $j(this).prop('disabled', true);
    }
  });

  if (!valores.length) {
    $j('#tipo_atendimento').trigger('chosen:updated');
    return;
  }

  if (valores.includes('0') || valores.includes('4')) {
    $options.each(function() {
      if ($j(this).val() === '5') {
        $j(this).prop('disabled', true);
      }
    });
  }

  if (valores.includes('5')) {
    $options.each(function() {
      if ($j(this).val() === '0' || $j(this).val() === '4') {
        $j(this).prop('disabled', true);
      }
    });
  }

  $j('#tipo_atendimento').trigger('chosen:updated');
}

$j('#tipo_atendimento').change(function() {
  atualizaOpcoesTipoAtendimento();
  habilitaAtividadesComplementares();
  habilitaFormaOrganizacaoTurma();
  habilitaEtapaAgregada();
  habilitaClasseEspecial();
});

$j('#organizacao_curricular').change(function() {
  habilitaEtapaEducacenso();
  habilitaAreasItinerarioFormativo();
  habilitaTipoCursoIntinerario();
});

$j('#tipo_curso_intinerario').change(function() {
  habilitaCodigoCursoTecnico();
});

$j('#etapa_agregada').change(function() {
  habilitaOrganizacaoCurricular();
  habilitaEtapaEducacenso();
});

verificaLocalFuncionamentoDiferenciado();

$j('#etapa_educacenso').change(function() {
  habilitaCursoTecnico();
  habilitaFormaOrganizacaoTurma();
});

function habilitaAtividadesComplementares(){
  var tipoAtendimento = $j('#tipo_atendimento').val() || [];
  var mostraCampo = tipoAtendimento.includes('4');
  $j('#atividades_complementares').makeUnrequired();
  if (mostraCampo) {
    $j('#atividades_complementares').removeAttr('disabled');
    $j('#atividades_complementares').trigger('chosen:updated');
    if (obrigarCamposCenso) {
      $j('#atividades_complementares').makeRequired();
    }
  } else {
    $j('#atividades_complementares').attr('disabled', 'disabled');
    $j('#atividades_complementares').val([]).trigger('chosen:updated');
  }
}

function habilitaCursoTecnico() {
  var etapasEnsinoTecnico = ['39', '40', '64'];
  var mostraCampo = $j.inArray($j('#etapa_educacenso').val(),etapasEnsinoTecnico) != -1;
  if (mostraCampo) {
    $j('#cod_curso_profissional').prop('disabled', false);
    $j('#cod_curso_profissional').trigger('chosen:updated');
    $j('#cod_curso_profissional').makeUnrequired();
    if (obrigarCamposCenso) {
      $j('#cod_curso_profissional').makeRequired();
    }
  } else {
    $j('#cod_curso_profissional').val('');
    $j('#cod_curso_profissional').prop('disabled', true);
    $j('#cod_curso_profissional').trigger('chosen:updated');
  }

  $j('#cod_curso_profissional').trigger('change');
}

function validaHorarioInicialFinal() {
  var horarioInicial = $j('#hora_inicial').val().replace(':', '');
  var horarioFinal = $j('#hora_final').val().replace(':', '');
  var horarioInicialIntervalo = $j('#hora_inicio_intervalo').val().replace(':', '');
  var horarioFinalIntervalo = $j('#hora_fim_intervalo').val().replace(':', '');

  if (horarioInicial > horarioFinal){
    alert('O horário inicial não pode ser maior que o horário final.');
    return false;
  }

  if (horarioInicialIntervalo > horarioFinalIntervalo){
    alert('O horário inicial de intervalo não pode ser maior que o horário final de intervalo.');
    return false;
  }

  return true;
}

function validaHoras() {
  var campos = [{'id' : 'hora_inicial', 'label' : 'Hora inicial'},
                {'id' : 'hora_final', 'label' : 'Hora final'},
                {'id' : 'hora_inicio_intervalo', 'label' : 'Hora início intervalo'},
                {'id' : 'hora_fim_intervalo', 'label' : 'Hora fim intervalo'}];
  var minutosPermitidos = ['00','05','10','15','20','25','30','35','40','45','50','55'];
  var retorno = true;

  $j.each(campos, function(i, campo) {
    var hora = $j('#' + campo.id).val();
    var minutos = hora.substr(3, 2);
    var minutosValidos = $j.inArray(minutos,minutosPermitidos) != -1;

    if (obrigarCamposCenso && (minutos != '' && !minutosValidos)) {
      alert('O campo ' + campo.label + ' não permite minutos diferentes de 0 ou 5.');
      retorno = false;
      return false;
    }

    if (minutos != '' && (minutos < 0 || minutos > 60)) {
      alert('O campo ' + campo.label + ' foi preenchido com um horário inválido.');
      retorno = false;
      return;
    }

    if (parseInt(hora) < 0 || parseInt(hora) > 24) {
      alert('O campo ' + campo.label + ' foi preenchido com um horário inválido.');
      retorno = false;
      return;
    }
  });
  return retorno;
}

function validaAtividadesComplementares() {
  var atividadesComplementares = $j('#atividades_complementares').val() || [];
  var qtdeAtividadesComplementares = atividadesComplementares.length;

  if (qtdeAtividadesComplementares > 6) {
    alert('O campo: Tipos de atividades complementares, não pode ter mais que 6 opções.');
    return false;
  }
  return true;
}

$j('#tipo_mediacao_didatico_pedagogico').on('change', verificaLocalFuncionamentoDiferenciado);

function habilitaEtapaEducacenso() {
  $j("#etapa_educacenso").prop('disabled', true);
  $j('#etapa_educacenso').makeUnrequired();

  const notContainData = $j('#organizacao_curricular').val() === null;

  const etapasAgregadasNotFormacao = ['301', '302', '303', '306', '308'];
  const etapasAgregadasFormacao = ['304', '305'];

  if(
    (etapasAgregadasNotFormacao.includes($j('#etapa_agregada').val()) && notContainData) ||
      (etapasAgregadasFormacao.includes($j('#etapa_agregada').val()) && !notContainData && $j('#organizacao_curricular').val().include('1'))
  ) {
    $j("#etapa_educacenso").prop('disabled', false);
    if(obrigarCamposCenso) {
      $j('#etapa_educacenso').makeRequired();
    }
  } else {
    $j("#etapa_educacenso").val('');
  }
  $j("#etapa_educacenso").trigger('change');
}

function habilitaAreasItinerarioFormativo() {
  $j("#area_itinerario").prop('disabled', true);
  const notContainData = $j('#organizacao_curricular').val() === null;
  $j('#area_itinerario').makeUnrequired();

  if (!notContainData && $j('#organizacao_curricular').val().include('4')) {
    $j("#area_itinerario").prop('disabled', false);
    $j('#area_itinerario').makeRequired();
  } else {
    $j("#area_itinerario").val('');
  }
  $j('#area_itinerario').trigger('chosen:updated');
}

function habilitaTipoCursoIntinerario() {
  $j("#tipo_curso_intinerario").prop('disabled', true);
  const notContainData = $j('#organizacao_curricular').val() === null;
  $j('#tipo_curso_intinerario').makeUnrequired();

  if (!notContainData && $j('#organizacao_curricular').val().include('5')) {
    $j("#tipo_curso_intinerario").prop('disabled', false);
    $j('#tipo_curso_intinerario').makeRequired();
  } else {
    $j("#tipo_curso_intinerario").val('');
  }

  $j("#tipo_curso_intinerario").trigger('change');
}

function habilitaCodigoCursoTecnico() {
  $j("#cod_curso_profissional_intinerario").prop('disabled', true);
  $j('#cod_curso_profissional_intinerario').makeUnrequired();

  if ($j('#tipo_curso_intinerario').val() === '1') {
    $j("#cod_curso_profissional_intinerario").prop('disabled', false);
    $j('#cod_curso_profissional_intinerario').makeRequired();
  } else {
    $j("#cod_curso_profissional_intinerario").val('');
  }
  $j('#cod_curso_profissional_intinerario').trigger('chosen:updated');
}

function habilitaEtapaAgregada() {
  $j("#etapa_agregada").prop('disabled', true);

  const tipoAtendimento = $j('#tipo_atendimento').val() || [];
  if (tipoAtendimento.includes('0')) {
    $j("#etapa_agregada").prop('disabled', false);
  } else {
    $j("#etapa_agregada").val('');
  }

  $j('#etapa_agregada').makeUnrequired();
  if (tipoAtendimento.includes('0') &&
      obrigarCamposCenso) {
    $j('#etapa_agregada').makeRequired();
  }

  $j('#etapa_agregada').trigger('change');
}

function habilitaOrganizacaoCurricular() {
  const etapaAgregada = $j('#etapa_agregada').val();

  if (etapaAgregada === '304' || etapaAgregada === '305') {
    $j('#organizacao_curricular').prop('disabled', false);
    $j('#organizacao_curricular').makeRequired();
  } else {
    $j('#organizacao_curricular').prop('disabled', true);
    $j('#organizacao_curricular').makeUnrequired();
    $j('#organizacao_curricular').val([]);
  }

  $j('#organizacao_curricular').trigger('chosen:updated').trigger('change'); //chosen:updated não dispara o event change
}

function habilitaClasseEspecial() {
  $j("#classe_especial").prop('disabled', true);
  const tipoAtendimento = $j('#tipo_atendimento').val() || [];
  if (tipoAtendimento.includes('0')) {
    $j("#classe_especial").prop('disabled', false);
  } else {
    $j("#classe_especial").val('');
  }

  $j('#classe_especial').makeUnrequired();
  if (tipoAtendimento.includes('0') &&
      obrigarCamposCenso) {
    $j('#classe_especial').makeRequired();
  }

  $j("#classe_especial").trigger('change');
}

$j('#tipo_mediacao_didatico_pedagogico').on('change', function(){
  if (!obrigarCamposCenso) {
    return true;
  }
  let didaticoPedagogicoPresencial = this.value == 1;
  $j('#hora_inicial').makeUnrequired();
  $j('#hora_final').makeUnrequired();
  $j('#hora_inicio_intervalo').makeUnrequired();
  $j('#hora_fim_intervalo').makeUnrequired();
  $j('#dias_semana').makeUnrequired();
  if (didaticoPedagogicoPresencial) {
    $j('#hora_inicial').prop('disabled', false).makeRequired();
    $j('#hora_final').prop('disabled', false).makeRequired();
    $j('#hora_inicio_intervalo').prop('disabled', false).makeRequired();
    $j('#hora_fim_intervalo').prop('disabled', false).makeRequired();
    $j('#dias_semana').prop('disabled', false).makeRequired().trigger("chosen:updated");;
  } else {
    $j('#hora_inicial').prop('disabled', true).val("");
    $j('#hora_final').prop('disabled', true).val("");
    $j('#hora_inicio_intervalo').prop('disabled', true).val("");
    $j('#hora_fim_intervalo').prop('disabled', true).val("");
    $j('#dias_semana').prop('disabled', true).val([]).trigger("chosen:updated");
  }
  if ($j('#turno_parcial').val() === 'S') {
    verificaHorariosTurnoParcial();
  }
}).trigger('change');

function buscaEtapasDaEscola() {
  var urlApi = getResourceUrlBuilder.buildUrl('/module/Api/Escola', 'etapas-da-escola-por-ano', {
    escola_id : $j('#ref_cod_escola').val(),
    ano : new Date().getFullYear()
  });

  var options = {
    url : urlApi,
    dataType : 'json',
    success  : function(dataResponse){
      $j('#ref_cod_modulo').val(dataResponse.modulo).trigger('change');
      preencheEtapasNaTurma(dataResponse.etapas);
    }
  };

  getResources(options);
}

function atualizaEtapaEducacenso() {
  $j('select[name="etapa_educacenso"] option').show();
  if ($j('#ref_cod_serie').val() === '' || $j('#multiseriada').val() == 1) {
    return;
  }

  var urlApi = getResourceUrlBuilder.buildUrl('/module/Api/Serie', 'etapa-educacenso', {
    serie_id : $j('#ref_cod_serie').val()
  });

  getResources({
    url: urlApi,
    dataType: 'json',
    success: function (dataResponse) {
      const only = dataResponse.etapa_educacenso;
      if (only) {

        $j('select[name="etapa_educacenso"] option').each(function () {
          if ($j(this).val() != only && $j(this).val() !== '' && ($j('select[name="etapa_educacenso"]').val() === '' || $j('select[name="etapa_educacenso"]').val() == only)) {
            $j(this).hide();
          }
        });
      }
    }
  });
}

$j('[name="ref_cod_serie"], #multiseriada').change(atualizaEtapaEducacenso);
atualizaEtapaEducacenso();

function preencheEtapasNaTurma(etapas) {
  $j.each( etapas, function( key, etapa ) {
    $j('input[name^="data_inicio[' + key + '"]').val(formatDate(etapa.data_inicio));
    $j('input[name^="data_fim[' + key + '"]').val(formatDate(etapa.data_fim));
    $j('input[name^="dias_letivos[' + key + '"]').val(etapa.dias_letivos);
  });
}

function atualizaOpcoesDeDisciplinas() {
  let escola_id = $j('#ref_cod_escola').val();
  let serie_id = $j('#ref_cod_serie').val();
  let ano = $j('#ano').val();
  if (escola_id && serie_id && ano) {
    let parametros = {
      escola_id: escola_id,
      serie_id: serie_id,
      ano: ano
    };
    let url = getResourceUrlBuilder.buildUrl(
      '/module/Api/ComponenteCurricular',
      'componentes-curriculares-escola-serie-ano',
      parametros
    );
    let options = {
      dataType: 'json',
      url: url,
      success: preencheComponentesCurriculares
    };
    getResource(options);
  } else {
    $j('#disciplinas').html('');
  }
}

var preencheComponentesCurriculares = function(data) {
  let componentesCurriculares = data.componentes_curriculares;
  var conteudo = '';
  let multisseriada = $j('#multiseriada').is(':checked');

  if (componentesCurriculares && !multisseriada) {
    conteudo += `<tr>
                   <td> <span>Nome</span></td>
                   <td> <span>Abreviatura</span></td>
                   <td> <span>Carga horária </span></td>
                   <td> <span>Usar padrão do componente?</span></td>
                   <td> <span>Possui docente vinculado?</span></td>
                 </tr>`;

    componentesCurriculares.forEach((componente) => {
      conteudo += getLinhaComponente(componente);
    });

    $j('#tr_disciplinas_ td:first').html('Componentes curriculares definidos em séries da escola');
    $j('#disciplinas').show();
  }  else if (multisseriada) {
    $j('#tr_disciplinas_ td:first').html('Os componentes curriculares de turmas multisseriadas devem ser definidos em suas respectivas series (Escola > Cadastros > Séries da escola)');
    $j('#disciplinas').hide();
  } else {
    $j('#disciplinas').html('A série/ano escolar não possui componentes curriculares cadastrados.');
  }

  if (conteudo) {
    $j('#disciplinas').html(
      `<table id="componentes_turma_cad" cellspacing="0" cellpadding="0" border="0">
          <tr align="left"><td>${conteudo}</td></tr>
      </table>`
    );

    $j('#definir_componentes_diferenciados').prop('disabled', !componentesCurriculares[0].permite_por_turma).trigger("change");
  }
}


var getLinhaComponente = function(componente) {
  return  `
  <tr class="linha-disciplina">
    <td width="250"><input type="checkbox" name="disciplinas[${componente.id}]" class="check-disciplina" id="disciplinas[]" value="${componente.id}">${componente.nome}</td>
    <td><span>${componente.abreviatura}</span></td>
    <td><input type="text" name="carga_horaria[${componente.id}]" value="" size="5" maxlength="7"></td>
    <td><input type="checkbox" name="usar_componente[${componente.id}]" value="1">(${componente.carga_horaria} h)</td>
    <td><input type="checkbox" name="docente_vinculado[${componente.id}]" value="1"></td>
  </tr>`;
}

$j(document).ready(function() {

  // on click das abas

  // DADOS GERAIS
  $j('#tab1').click(
    function(){

      $j('.turmaTab-active').toggleClass('turmaTab-active turmaTab');
      $j('#tab1').toggleClass('turmaTab turmaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (index>=linha_inicial_tipo){
          if (row.id!='stop')
            row.hide();
          else
            return false;
        }else{
          row.show();
        }
      });
      //multisseriada
      configuraCamposExibidos();
    }
  );

  // Adicionais
  $j('#tab2').click(
    function(){
      $j('.turmaTab-active').toggleClass('turmaTab-active turmaTab');
      $j('#tab2').toggleClass('turmaTab turmaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_tipo && index < linha_inicial_turno_parcial){
            if ((index - linha_inicial_tipo) % 2 == 0){
              $j('#'+row.id).find('td').removeClass('formlttd');
              $j('#'+row.id).find('td').addClass('formmdtd');
            }else{
              $j('#'+row.id).find('td').removeClass('formmdtd');
              $j('#'+row.id).find('td').addClass('formlttd');

            }

            row.show();
          }else if (index>0){
            row.hide();
          }
        }else
          return false;
      });
      atualizaOpcoesTipoAtendimento();
      habilitaAtividadesComplementares();
      habilitaEtapaAgregada();
      habilitaEtapaEducacenso();
      habilitaOrganizacaoCurricular();
      habilitaClasseEspecial();
      habilitaAreasItinerarioFormativo();
      habilitaTipoCursoIntinerario();
      habilitaCursoTecnico();
      habilitaFormaOrganizacaoTurma();
      habilitaCodigoCursoTecnico();
      habilitaFormacaoAlternancia();
    });

  // Turmas Parciais
  $j('#tab3').click(
    function(){
      $j('.turmaTab-active').toggleClass('turmaTab-active turmaTab');
      $j('#tab3').toggleClass('turmaTab turmaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_turno_parcial){
            if ((index - linha_inicial_turno_parcial) % 2 == 0){
              $j('#'+row.id).find('td').removeClass('formlttd');
              $j('#'+row.id).find('td').addClass('formmdtd');
            }else{
              $j('#'+row.id).find('td').removeClass('formmdtd');
              $j('#'+row.id).find('td').addClass('formlttd');

            }

            row.show();
          }else if (index>0){
            row.hide();
          }
        }else
          return false;
      });
      verificaHorariosTurnoParcial();
    });

  // fix checkboxs
  $j('.tablecadastro >tbody  > tr').each(function(index, row) {
    if (index>=linha_inicial_tipo){
      $j('#'+row.id).find('input:checked').val('on');
    }
  });

  var submitForm = function(){
    let canSubmit = validationUtils.validatesFields(true);
    if (canSubmit) {
      valida();
    }
  }

  $j('#ano').on('change', function(){
    $j('#ano_letivo').val($j('#ano').val());
  });

  $j('#ref_cod_escola').on('change', function(){
    $j('#ref_cod_escola_').val($j('#ref_cod_escola').val());
  });

  $j('#ref_cod_curso').on('change', function(){
    $j('#ref_cod_curso_').val($j('#ref_cod_curso').val());
  });

  $j('#ref_cod_serie').on('change', function(){
    atualizaOpcoesDeDisciplinas();
    $j('#ref_cod_serie_').val($j('#ref_cod_serie').val());
  });

  $j("#tipo_boletim, #tipo_boletim_diferenciado").chosen({
    no_results_text: "Nenhum modelo encontrado!",
    allow_single_deselect: true,
  });
});

// Força reload na página quando utiliza "voltar" do navegador
window.addEventListener( "pageshow", function ( event ) {
  var historyTraversal = (
    event.persisted ||
    ( typeof window.performance != "undefined" && window.performance.navigation.type === 2 )
  );
  if ( historyTraversal ) {
    // Handle page restore.
    window.location.reload();
  }
});
