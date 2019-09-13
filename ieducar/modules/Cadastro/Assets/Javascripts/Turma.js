//abas

$j('td .formdktd').append('<div id="tabControl"><ul><li><div id="tab1" class="turmaTab"> <span class="tabText">Dados gerais</span></div></li><li><div id="tab2" class="turmaTab"> <span class="tabText">Dados adicionais</span></div></li></ul></div>');
$j('td .formdktd b').remove();
$j('.tablecadastro td .formdktd div').remove();
$j('#tab1').addClass('turmaTab-active').removeClass('turmaTab');

// Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
$j('#codigo_inep_educacenso').closest('tr').attr('id','tr_codigo_inep_educacenso');

// Adiciona um ID à linha que termina o formulário para parar de esconder os campos
$j('.tableDetalheLinhaSeparador').closest('tr').attr('id','stop');

// Pega o número dessa linha
linha_inicial_tipo = $j('#tr_codigo_inep_educacenso').index()-2;

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

let verificaEtapaEducacenso = ()=>{
  $j('#etapa_educacenso').makeUnrequired();
  if ($j('#tipo_atendimento').val() &&
      $j('#tipo_atendimento').val() != "4" &&
      $j('#tipo_atendimento').val() != "5") {
    if (obrigarCamposCenso) {
      $j('#etapa_educacenso').makeRequired();
    }
  }
}

let verificaLocalFuncionamentoDiferenciado = () => {
  $j('#local_funcionamento_diferenciado').makeUnrequired();
  let habilitaCampo = [1, 2].includes(+($j('#tipo_mediacao_didatico_pedagogico').val()));
  $j('#local_funcionamento_diferenciado').prop('disabled', !habilitaCampo);

  if (habilitaCampo) {
    if (obrigarCamposCenso) {
      $j('#local_funcionamento_diferenciado').makeRequired();
    }
  } else {
    $j('#local_funcionamento_diferenciado').val();
  }
}

$j('#tipo_atendimento').change(function() {
  mostraAtividadesComplementares();
  verificaEtapaEducacenso();
  habilitaEtapaEducacenso();
});
verificaEtapaEducacenso();
verificaLocalFuncionamentoDiferenciado();

$j('#etapa_educacenso').change(function() {
  mostraCursoTecnico();;
});

function mostraAtividadesComplementares(){
  var mostraCampo = $j('#tipo_atendimento').val() == '4';
  $j('#atividades_complementares').makeUnrequired();
  if (mostraCampo) {
    $j('#atividades_complementares').removeAttr('disabled');
    $j('#atividades_complementares').trigger('chosen:updated');;
    if (obrigarCamposCenso) {
      $j('#atividades_complementares').makeRequired();
    }
  } else {
    $j('#atividades_complementares').attr('disabled', 'disabled');
    $j('#atividades_complementares').val([]).trigger('chosen:updated');
  }
}

function mostraCursoTecnico() {
  var etapasEnsinoTecnico = ['30', '31', '32', '33', '34', '39', '40', '64', '74'];
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
  var atividadeComplementar = $j("#tipo_atendimento").val() == 4;
  var atendimentoEducacionalEspecializado = $j("#tipo_atendimento").val() == 5;

  $j("#etapa_educacenso").prop('disabled', false);

  if (atividadeComplementar || atendimentoEducacionalEspecializado) {
    $j("#etapa_educacenso").prop('disabled', true).val("");
  }
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

function preencheEtapasNaTurma(etapas) {
  $j.each( etapas, function( key, etapa ) {
    $j('input[name^="data_inicio[' + key + '"]').val(formatDate(etapa.data_inicio));
    $j('input[name^="data_fim[' + key + '"]').val(formatDate(etapa.data_fim));
    $j('input[name^="dias_letivos[' + key + '"]').val(etapa.dias_letivos);
  });
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
    }
  );

  // Adicionais
  $j('#tab2').click(
    function(){
      $j('.turmaTab-active').toggleClass('turmaTab-active turmaTab');
      $j('#tab2').toggleClass('turmaTab turmaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_tipo){
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
      mostraAtividadesComplementares();
      mostraCursoTecnico();
      habilitaEtapaEducacenso();
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

  $j('#ref_cod_serie, #ano_letivo').on('change', function(){
    let escola_id = $j('#ref_cod_escola').val();
    let serie_id = $j('#ref_cod_serie').val();
    let ano = $j('#ano_letivo').val();
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
  });

  var getLinhaComponente = function(componente) {
    return  `
    <div style="margin-bottom: 10px; float: left" class="linha-disciplina">
      <label style="display: block; float: left; width: 250px;"><input type="checkbox" name="disciplinas[${componente.id}]" class="check-disciplina" id="disciplinas[]" value="${componente.id}">${componente.nome}</label>
      <label style="display: block; float: left; width: 100px;"><input type="text" name="carga_horaria[${componente.id}]" value="" size="5" maxlength="7"></label>
      <label style="display: block; float: left;width: 200px;"><input type="checkbox" name="usar_componente[${componente.id}]" value="1">(${componente.carga_horaria} h)</label>
      <label style="display: block; float: left;"><input type="checkbox" name="docente_vinculado[${componente.id}]" value="1"></label>
    </div>
    <br style="clear: left" />`;
  }

  var preencheComponentesCurriculares = function(data) {
    let componentesCurriculares = data.componentes_curriculares;
    var conteudo = '';

    if (componentesCurriculares.length) {
      conteudo += `<div style="margin-bottom: 10px; float: left">
                     <span style="display: block; float: left; width: 250px;">Nome</span>
                     <span style="display: block; float: left; width: 250px;">Abreviatura</span>
                     <label> <span style="display: block; float: left; width: 100px">Carga hor&aacute;ria </span></label>
                     <label> <span style="display: block; float: left; width: 200px">Usar padr&atilde;o do componente?</span></label>
                     <label> <span style="display: block; float: left">Possui docente vinculado?</span></label>
                   </div>
                   <br style="clear: left" />`;

      componentesCurriculares.forEach((componente) => {
        conteudo += getLinhaComponente(componente);
      });
    } else {
      $j('#disciplinas').html('A série/ano escolar não possui componentes curriculares cadastrados.');
    }

    if (conteudo) {
      $j('#disciplinas').html(
        `<table cellspacing="0" cellpadding="0" border="0">
            <tr align="left"><td>${conteudo}</td></tr>
        </table>`
      );
    }
  }
});
