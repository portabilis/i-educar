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

$j('#tipo_atendimento').change(function() {
  mostraAtividadesComplementares();
  mostraAtividadesAee();
});

$j('#etapa_educacenso').change(function() {
  mostraCursoTecnico();
});

function mostraAtividadesComplementares(){
  var mostraCampo = $j('#tipo_atendimento').val() == '4';
  if (mostraCampo) {
    $j('#tr_atividades_complementares').show();
  } else {
    $j('#tr_atividades_complementares').hide();
  }
}

function mostraAtividadesAee() {
  var mostraCampo = $j('#tipo_atendimento').val() == '5';
  if (mostraCampo) {
    $j('#tr_atividades_aee').show();
  } else {
    $j('#tr_atividades_aee').hide();
  }
}

function mostraCursoTecnico() {
  var etapasEnsinoTecnico = ['30', '31', '32', '33', '34', '39', '40', '64', '74'];
  var mostraCampo = $j.inArray($j('#etapa_educacenso').val(),etapasEnsinoTecnico) != -1;
  if (mostraCampo) {
    $j('#tr_cod_curso_profissional').show();
  } else {
    $j('#tr_cod_curso_profissional').hide();
  }
}

function validaHorarioInicialFinal() {
  var horarioInicial = $j('#hora_inicial').val().replace(':', '');
  var horarioFinal = $j('#hora_final').val().replace(':', '');
  if (horarioInicial > horarioFinal){
    alert('O horário inicial não pode ser maior que o horário final.');
    return false;
  }
  return true;
}

function validaMinutos() {
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
    if (minutos != '' && !minutosValidos) {
      alert('O campo ' + campo.label + ' não permite minutos diferentes de 0 ou 5.');
      retorno = false;
      return false;
    }
  });
  return retorno;
}

function validaAtividadesComplementares() {
  // recebe - 1 pois a primeira sempre é nula
  var qtdeAtividadesComplementares = $j('#atividades_complementares').val().length - 1;

  if (qtdeAtividadesComplementares > 6) {
    alert('O campo: Atividades complementares, não pode ter mais que 6 opções.');
    return false;
  }
  return true;
}

$j('#ref_cod_curso').on('change', habilitaTurmaMaisEducacao);
$j('#tipo_atendimento').on('change', habilitaTurmaMaisEducacao);
$j('#tipo_mediacao_didatico_pedagogico').on('change', habilitaTurmaMaisEducacao);
$j('#etapa_educacenso').on('change', habilitaTurmaMaisEducacao);

habilitaTurmaMaisEducacao();

function habilitaTurmaMaisEducacao() {
  if (modoCadastro) {
    getDependenciaAdministrativaEscola();
    getModalidadeCurso();
  }

  var didaticoPedagogicoPresencial = $j('#tipo_mediacao_didatico_pedagogico').val() == 1;
  var dependenciaAdministrativaEstadualMunicipal = $j('#dependencia_administrativa').val() == 2 ||
                                                   $j('#dependencia_administrativa').val() == 3;
  var atendimentoClasseHospitalarAee = $j('#tipo_atendimento').val() == 1 ||
                                       $j('#tipo_atendimento').val() == 5;
  var atendimentoAtividadeComplementar = $j('#tipo_atendimento').val() == 4;
  var modalidadeEja = $j('#modalidade_curso').val() == 3;
  var etapaEducacenso = ($j('#etapa_educacenso').val() >= 4 &&
                         $j('#etapa_educacenso').val() <= 38) ||
                        ($j('#etapa_educacenso').val() == 41);  
  var validaTipoAtendimento = !atendimentoAtividadeComplementar ? !modalidadeEja && etapaEducacenso : true;
  
  if (
    didaticoPedagogicoPresencial &&
    dependenciaAdministrativaEstadualMunicipal &&
    !atendimentoClasseHospitalarAee &&
    validaTipoAtendimento
  ) {
    $j("#turma_mais_educacao").attr('disabled', false);
  } else {
    $j("#turma_mais_educacao").attr('disabled', true);
  }
}

function getDependenciaAdministrativaEscola(){
  var options = {
    dataType : 'json',
    url : getResourceUrlBuilder.buildUrl(
      '/module/Api/Escola',
      'escola-dependencia-administrativa',
      {escola_id : $j('#ref_cod_escola').val()}
    ),
    async : false,
    success : function(dataResponse) {
      if (dataResponse.dependencia_administrativa) {
        $j('#dependencia_administrativa').val(dataResponse.dependencia_administrativa);
      }
    }
  }
  getResource(options);
}

function getModalidadeCurso(){
  var options = {
    dataType : 'json',
    url : getResourceUrlBuilder.buildUrl(
      '/module/Api/Curso',
      'modalidade-curso',
      {curso_id : $j('#ref_cod_curso').val()}
    ),
    async : false,
    success : function(dataResponse) {
      if (dataResponse.modalidade_curso) {
        $j('#modalidade_curso').val(dataResponse.modalidade_curso);
      }
    }
  }
  getResource(options);
}

$j(document).ready(function() {

  // on click das abas

  // DADOS GERAIS
  $j('#tab1').click( 
    function(){

      $j('.turmaTab-active').toggleClass('turmaTab-active turmaTab');
      $j('#tab1').toggleClass('turmaTab turmaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (index>=linha_inicial_tipo-1){
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
      mostraAtividadesAee();
      mostraCursoTecnico();
    });

  // fix checkboxs
  $j('.tablecadastro >tbody  > tr').each(function(index, row) {
    if (index>=linha_inicial_tipo){
      $j('#'+row.id).find('input:checked').val('on');
    }
  });

  $j("#etapa_educacenso").change(function() {
    changeEtapaTurmaField();
  });

  var changeEtapaTurmaField = function() {
    var etapa = $j("#etapa_educacenso").val();

    if (etapa == 12 || etapa == 13) {
      $j("#etapa_educacenso2 > option").each(function() {
        var etapasCorrespondentes = ['4','5','6','7','8','9','10','11'];
        if ($j.inArray(this.value, etapasCorrespondentes) !== -1){
          this.show();
        } else {
          this.hide();
        }
      });
    } else if (etapa == 22 || etapa == 23) {
      $j("#etapa_educacenso2 > option").each(function() {
        var etapasCorrespondentes = ['14','15','16','17','18','19','20','21','41'];
        if ($j.inArray(this.value, etapasCorrespondentes) !== -1){
          this.show();
        } else {
          this.hide();
        }
      });
    } else if (etapa == 24) {
      $j("#etapa_educacenso2 > option").each(function() {
        var etapasCorrespondentes = ['4','5','6','7','8','9','10','11','14','15','16','17','18','19','20','21','41'];
        if ($j.inArray(this.value, etapasCorrespondentes) !== -1){
          this.show();
        } else {
          this.hide();
        }
      });
    } else if (etapa == 72) {
      $j("#etapa_educacenso2 > option").each(function() {
        var etapasCorrespondentes = ['69','70'];
        if ($j.inArray(this.value, etapasCorrespondentes) !== -1){
          this.show();
        } else {
          this.hide();
        }
      });
    } else if (etapa == 56) {
      $j("#etapa_educacenso2 > option").each(function() {
        var etapasCorrespondentes = ['1','2','4','5','6','7','8','9','10','11','14','15','16','17','18','19','20','21','41'];
        if ($j.inArray(this.value, etapasCorrespondentes) !== -1){
          this.show();
        } else {
          this.hide();
        }
      });
    } else if (etapa == 64) {
      $j("#etapa_educacenso2 > option").each(function() {
        var etapasCorrespondentes = ['39','40'];
        if ($j.inArray(this.value, etapasCorrespondentes) !== -1){
          this.show();
        } else {
          this.hide();
        }
      });
    } else {
      $j("#etapa_educacenso2").prop('disabled', 'disabled');
      $j("#etapa_educacenso2").val(null);
      return;
    }
    $j("#etapa_educacenso2").prop('disabled', false);
  }

  changeEtapaTurmaField();

});