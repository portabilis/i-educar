addEmailEdit();
var $submitButton      = $j('#btn_enviar');
var $escolaInepIdField = $j('#escola_inep_id');
var $escolaIdField     = $j('#cod_escola');

const DEPENDENCIA_ADMINISTRATIVA = {
  FEDERAL: 1,
  ESTADUAL: 2,
  MUNICIPAL: 3,
  PRIVADA: 4
}

const SITUACAO_FUNCIONAMENTO = {
  EM_ATIVIDADE : 1,
  PARALISADA : 2,
  EXTINTA : 3
}

const UNIDADE_VINCULADA = {
  SEM_VINCULO : 0,
  EDUCACAO_BASICA : 1,
  ENSINO_SUPERIOR : 2
}

const MANTENEDORA_ESCOLA_PRIVADA = {
  GRUPOS_EMPRESARIAIS : 1,
  SINDICATOS_TRABALHISTAS : 2,
  ORGANIZACOES_NAO_GOVERNAMENTAIS : 3,
  INSTITUICOES_SIM_FINS_LUCRATIVOS : 4,
  SISTEMA_S : 5,
  OSCIP : 6
}

const SCHOOL_MANAGER_ROLE = {
    DIRETOR: 1,
}

const SCHOOL_MANAGER_ACCESS_CRITERIA = {
    OUTRO: 7,
}

const LOCAL_FUNCIONAMENTO = {
    PREDIO_ESCOLAR: 3
}

const USO_INTERNET = {
    NAO_POSSUI: 1,
    ALUNOS: 4
};

const EQUIPAMENTOS = {
    COMPUTADORES: 1
};

const EQUIPAMENTOS_ACESSO_INTERNET = {
  COMPUTADORES: '1'
};

var submitForm = function(){
  var canSubmit = validationUtils.validatesFields(true);

  // O campo escolaInepId somente é atualizado ao cadastrar escola,  uma vez que este
  // é atualizado via ajax, e durante o (novo) cadastro a escola ainda não possui id.
  //
  // #TODO refatorar cadastro de escola para que todos campos sejam enviados via ajax,
  // podendo então definir o código escolaInepId ao cadastrar a escola.

  if (canSubmit) {
    acao();
  }
}

function addEmailEdit() {
  let pessoaId = $j('#pessoaj_id').val();
  let url = '"' + '/intranet/empresas_cad.php?idpes=' + pessoaId + '#email ' + '"';
  let editEmail =
  '<span>' +
    '<a href=' + url + 'target="_blank" class="span-busca-cep" style="color: blue; margin-left: 10px;">Clique aqui para editar o e-mail</a>' +
  '</span>';

  $j('#tr_p_email td:last-child').append(editEmail)
}

var handleGetEscola = function(dataResponse) {
  handleMessages(dataResponse.msgs);

  $escolaInepIdField.val(dataResponse.escola_inep_id);
}

var getEscola = function(escolaId) {
  var data = {
    id : escolaId
  };

  var options = {
    url      : getResourceUrlBuilder.buildUrl('/module/Api/escola', 'escola'),
    dataType : 'json',
    data     : data,
    success  : handleGetEscola
  };

  getResource(options);
}

if ($escolaIdField.val()) {
  getEscola($escolaIdField.val());
}

// unbind events
$submitButton.removeAttr('onclick');
$j(document.formcadastro).removeAttr('onsubmit');

// bind events
$submitButton.click(submitForm);

let obrigarCamposCenso = $j('#obrigar_campos_censo').val() == '1';

$j('#local_funcionamento').on('change', function () {
    changeLocalFuncionamento()
});

$j('#predio_compartilhado_outra_escola').on('change', function () {
    changePredioCompartilhadoEscola()
});

function obrigaCampoRegulamentacao() {
  escolaEmAtividade = $j('#situacao_funcionamento').val() == SITUACAO_FUNCIONAMENTO.EM_ATIVIDADE;

  if (escolaEmAtividade) {
    $j('#regulamentacao').makeRequired();
    $j("#regulamentacao").prop('disabled', false);
  } else {
    $j('#regulamentacao').makeUnrequired();
    $j("#regulamentacao").prop('disabled', true);
  }
}

function habilitaCampoOrgaoVinculadoEscola() {
  if ($j('#dependencia_administrativa').val() != DEPENDENCIA_ADMINISTRATIVA.PRIVADA) {
    $j("#orgao_vinculado_escola").prop('disabled', false);
    $j("#orgao_vinculado_escola").trigger("chosen:updated");
  } else {
    $j("#orgao_vinculado_escola").prop('disabled', true);
    $j("#orgao_vinculado_escola").trigger("chosen:updated");
  }
}

function obrigaCampoOrgaoVinculadoEscola() {
  if (obrigarCamposCenso && $j('#dependencia_administrativa').val() != DEPENDENCIA_ADMINISTRATIVA.PRIVADA) {
    $j("#orgao_vinculado_escola").makeUnrequired();
    $j("#orgao_vinculado_escola").makeRequired();
  } else {
    $j("#orgao_vinculado_escola").makeUnrequired();
  }
}

function habilitaCampoEsferaAdministrativa() {
  let regulamentacao = $j('#regulamentacao').val();

  if (regulamentacao === '0') {
    $j("#esfera_administrativa").prop('disabled', true);
    $j('#esfera_administrativa').makeUnrequired();
    $j("#esfera_administrativa").val('');
  } else {
    $j("#esfera_administrativa").prop('disabled', false);
    if (obrigarCamposCenso) {
      $j('#esfera_administrativa').makeRequired();
    }
  }
}

function changeLocalFuncionamento(){
    var disabled = $j.inArray(LOCAL_FUNCIONAMENTO.PREDIO_ESCOLAR.toString(), $j('#local_funcionamento').val()) == -1;
    $j('#condicao').prop("disabled",disabled);
    $j('#predio_compartilhado_outra_escola').prop("disabled",disabled);
    $j('#condicao').makeUnrequired();
    $j('#predio_compartilhado_outra_escola').makeUnrequired();
    $j('#dependencia_numero_salas_existente').makeUnrequired();
    $j('#codigo_inep_escola_compartilhada').makeUnrequired();
    if (!disabled && obrigarCamposCenso) {
        $j('#condicao').makeRequired();
        $j('#predio_compartilhado_outra_escola').makeRequired();
        $j('#dependencia_numero_salas_existente').makeRequired();
        $j('#codigo_inep_escola_compartilhada').makeRequired();
    }
}

function changePredioCompartilhadoEscola() {
    var disabled = $j('#predio_compartilhado_outra_escola').val() != 1;
    $j('#codigo_inep_escola_compartilhada').prop("disabled",disabled);
    $j('#codigo_inep_escola_compartilhada2').prop("disabled",disabled);
    $j('#codigo_inep_escola_compartilhada3').prop("disabled",disabled);
    $j('#codigo_inep_escola_compartilhada4').prop("disabled",disabled);
    $j('#codigo_inep_escola_compartilhada5').prop("disabled",disabled);
    $j('#codigo_inep_escola_compartilhada6').prop("disabled",disabled);
}

function changePossuiDependencias() {
    var disabled = $j('#possui_dependencias').val() != 1;
    $j('#salas_gerais').prop("disabled",disabled);
    $j('#salas_funcionais').prop("disabled",disabled);
    $j('#banheiros').prop("disabled",disabled);
    $j('#laboratorios').prop("disabled",disabled);
    $j('#salas_atividades').prop("disabled",disabled);
    $j('#dormitorios').prop("disabled",disabled);
    $j('#areas_externas').prop("disabled",disabled);
    $j("#salas_gerais,#salas_funcionais,#banheiros,#laboratorios,#salas_atividades,#dormitorios,#areas_externas").trigger("chosen:updated");
}

const link = '<span> Caso não encontre a pessoa jurídica, cadastre em </span><a href="empresas_cad.php" target="_blank">Pessoas > Cadastros > Pessoas jurídicas.</a>';
$j('#pessoaj_idpes').after(link);

//abas

// hide nos campos das outras abas (deixando só os campos da primeira aba)
if (!$j('#pessoaj_idpes').is(':visible')) {

  $j('td .formdktd:first').append('<div id="tabControl"><ul><li><div id="tab1" class="escolaTab"> <span class="tabText">Dados gerais</span></div></li><li><div id="tab2" class="escolaTab"> <span class="tabText">Infraestrutura</span></div></li><li><div id="tab3" class="escolaTab"> <span class="tabText">Depend\u00eancias</span></div></li><li><div id="tab4" class="escolaTab"> <span class="tabText">Equipamentos</span></div></li><li><div id="tab5" class="escolaTab"> <span class="tabText">Recursos</span></div></li><li><div id="tab6" class="escolaTab"> <span class="tabText">Dados do ensino</span></div></li></ul></div>');
  $j('td .formdktd b').remove();
  $j('#tab1').addClass('escolaTab-active').removeClass('escolaTab');

  // Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
  $j('#local_funcionamento').closest('tr').attr('id','tlocal_funcionamento');
  $j('#atendimento_aee').closest('tr').attr('id','tatendimento_aee');

  // Pega o número dessa linha
  linha_inicial_infra = $j('#tlocal_funcionamento').index()-2;
  linha_inicial_dependencia = $j('#tr_possui_dependencias').index()-2;
  linha_inicial_equipamento = $j('#tr_equipamentos').index()-2;
  linha_inicial_recursos = $j('#tr_quantidade_profissionais').index()-2;
  linha_inicial_dados = $j('#tatendimento_aee').index()-2;

  // Adiciona um ID à linha que termina o formulário para parar de esconder os campos
  $j('.tableDetalheLinhaSeparador').closest('tr').attr('id','stop');
  $j('.tablecadastro >tbody  > tr').each(function(index, row) {
    if (index>=linha_inicial_infra){
      if (row.id!='stop')
        row.hide();
      else{
        return false;
      }
    }
  });
}

$j(document).ready(function() {

  // on click das abas

  // DADOS GERAIS
  $j('#tab1').click(
    function(){

      $j('.escolaTab-active').toggleClass('escolaTab-active escolaTab');
      $j('#tab1').toggleClass('escolaTab escolaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (index>=linha_inicial_infra){
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

  // INFRA
  $j('#tab2').click(
    function(){
      $j('.escolaTab-active').toggleClass('escolaTab-active escolaTab');
      $j('#tab2').toggleClass('escolaTab escolaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_infra && index < linha_inicial_dependencia){
            row.show();
          }else if (index>0){
            row.hide();
          }
        }else
          return false;
      });
      changeLocalFuncionamento();
      changePredioCompartilhadoEscola();
    });

  // DEPENDENCIAS
  $j('#tab3').click(
    function(){
      $j('.escolaTab-active').toggleClass('escolaTab-active escolaTab');
      $j('#tab3').toggleClass('escolaTab escolaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_dependencia && index < linha_inicial_equipamento){
            row.show();
          }else if (index>0){
            row.hide();
          }
        }else
          return false;
      });
      habilitaCamposNumeroSalas();
    });

  // EQUIPAMENTOS
  $j('#tab4').click(
    function(){
      $j('.escolaTab-active').toggleClass('escolaTab-active escolaTab');
      $j('#tab4').toggleClass('escolaTab escolaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_equipamento && index < linha_inicial_recursos){
            row.show();
          }else if (index>0){
            row.hide();
          }
        }else
          return false;
      });
      habilitaCampoAcessoInternet();
      habilitaCampoEquipamentosAcessoInternet();
      habilitaCampoRedeLocal();
      habilitaCamposQuantidadeComputadoresAlunos();
    });

  // Dados educacionais
  $j('#tab5').click(
    function(){
      $j('.escolaTab-active').toggleClass('escolaTab-active escolaTab');
      $j('#tab5').toggleClass('escolaTab escolaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_recursos && index < linha_inicial_dados){
            row.show();
          }else if (index>0){
            row.hide();
          }
        }else
          return false;
      });
   });

  // Dados educacionais
  $j('#tab6').click(
    function(){
      $j('.escolaTab-active').toggleClass('escolaTab-active escolaTab');
      $j('#tab6').toggleClass('escolaTab escolaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_dados){
            row.show();
          }else if (index>0){
            row.hide();
          }
        }else
          return false;
      });

        habilitarCampoUnidadeVinculada();
        mostrarCamposDaUnidadeVinculada();
        obrigarCamposDaUnidadeVinculada();
        obrigarCnpjMantenedora();
        habilitaCampoEducacaoIndigena();
        habilitaCampoLinguaMinistrada();
        habilitaReservaVagasCotas();
      });

  // fix checkboxs
  $j('input:checked').val('on');

  let verificaCamposDepAdm = () => {
    $j('#categoria_escola_privada').makeUnrequired();
    $j('#conveniada_com_poder_publico').makeUnrequired();
    $j('#mantenedora_escola_privada').makeUnrequired();
    $j('#categoria_escola_privada').prop('disabled', true);
    $j('#conveniada_com_poder_publico').prop('disabled', true);
    $j('#mantenedora_escola_privada').prop('disabled', true);
    $j("#mantenedora_escola_privada").trigger("chosen:updated");
    $j('#cnpj_mantenedora_principal').prop('disabled', true);

    if (obrigarCamposCenso && $j('#situacao_funcionamento').val() == '1' && $j('#dependencia_administrativa').val() == DEPENDENCIA_ADMINISTRATIVA.PRIVADA){
      $j('#conveniada_com_poder_publico').makeRequired();
      $j('#mantenedora_escola_privada').makeRequired();
    }

    if ($j('#situacao_funcionamento').val() == '1' && $j('#dependencia_administrativa').val() == DEPENDENCIA_ADMINISTRATIVA.PRIVADA){
      $j('#conveniada_com_poder_publico').prop('disabled', false);
      $j('#mantenedora_escola_privada').prop('disabled', false);
      $j("#mantenedora_escola_privada").trigger("chosen:updated");
      $j('#cnpj_mantenedora_principal').prop('disabled', false);
    }

    if (obrigarCamposCenso && $j('#dependencia_administrativa').val() == DEPENDENCIA_ADMINISTRATIVA.PRIVADA){
      $j('#categoria_escola_privada').makeRequired();
    }

    if ($j('#dependencia_administrativa').val() == DEPENDENCIA_ADMINISTRATIVA.PRIVADA){
      $j('#categoria_escola_privada').prop('disabled', false);
    }
  }

  $j('#dependencia_administrativa').change(
    function (){
      verificaCamposDepAdm();
      habilitaCampoOrgaoVinculadoEscola();
      obrigaCampoOrgaoVinculadoEscola();
    }
  );

  habilitaCampoOrgaoVinculadoEscola();
  obrigaCampoOrgaoVinculadoEscola();
  obrigaCampoRegulamentacao();
  changePossuiDependencias();

  $j('#possui_dependencias').change(
    function (){
        changePossuiDependencias();
    }
  );

  $j('#unidade_vinculada_outra_instituicao').change(
    function (){
      mostrarCamposDaUnidadeVinculada();
      obrigarCamposDaUnidadeVinculada();
    }
  );

  function mostrarCamposDaUnidadeVinculada() {
    if ($j('#unidade_vinculada_outra_instituicao').val() == UNIDADE_VINCULADA.EDUCACAO_BASICA) {
      $j('#inep_escola_sede').prop('disabled', false);
      $j('#codigo_ies').prop('disabled', true);
      $j('#codigo_ies').val('');
      $j('#codigo_ies_id').val('');
    } else if($j('#unidade_vinculada_outra_instituicao').val() == UNIDADE_VINCULADA.ENSINO_SUPERIOR) {
      $j('#codigo_ies').prop('disabled', false);
      $j('#inep_escola_sede').prop('disabled', true);
      $j('#inep_escola_sede').val('');
    } else {
      $j('#inep_escola_sede').prop('disabled', true);
      $j('#codigo_ies').prop('disabled', true);
      $j('#inep_escola_sede').val('');
      $j('#codigo_ies').val('');
      $j('#codigo_ies_id').val('');
    }
  }

  function habilitarCampoUnidadeVinculada() {
    escolaEmAtividade = $j('#situacao_funcionamento').val() == SITUACAO_FUNCIONAMENTO.EM_ATIVIDADE;

    if (escolaEmAtividade) {
      $j("#unidade_vinculada_outra_instituicao").prop('disabled', false);
      if (obrigarCamposCenso) {
        $j("#unidade_vinculada_outra_instituicao").makeRequired();
      }
    } else {
      $j("#unidade_vinculada_outra_instituicao").val('');
      $j("#unidade_vinculada_outra_instituicao").prop('disabled', true);
      $j("#unidade_vinculada_outra_instituicao").makeUnrequired();
    }
  }

  function obrigarCamposDaUnidadeVinculada() {
    if ($j('#unidade_vinculada_outra_instituicao').val() == UNIDADE_VINCULADA.EDUCACAO_BASICA && obrigarCamposCenso) {
      $j('#inep_escola_sede').makeRequired();
      $j('#codigo_ies').makeUnrequired();
    } else if($j('#unidade_vinculada_outra_instituicao').val() == UNIDADE_VINCULADA.ENSINO_SUPERIOR && obrigarCamposCenso) {
      $j('#codigo_ies').makeRequired();
      $j('#inep_escola_sede').makeUnrequired();
    } else {
      $j('#inep_escola_sede').makeUnrequired();
      $j('#codigo_ies').makeUnrequired();
    }
  }


  $j('#mantenedora_escola_privada').change(
    function (){
      obrigarCnpjMantenedora();
    }
  );

  function obrigarCnpjMantenedora() {
    dependenciaPrivada = $j('#dependencia_administrativa').val() == DEPENDENCIA_ADMINISTRATIVA.PRIVADA;
    mantenedoraSemFinsLucrativos = $j.inArray(MANTENEDORA_ESCOLA_PRIVADA.INSTITUICOES_SIM_FINS_LUCRATIVOS.toString(), $j('#mantenedora_escola_privada').val()) != -1;
    escolaRegulamentada = $j('#regulamentacao').val() == 1;
    emAtividade = $j('#situacao_funcionamento').val() == SITUACAO_FUNCIONAMENTO.EM_ATIVIDADE;

    $j('#cnpj_mantenedora_principal').makeUnrequired();

    if (obrigarCamposCenso && dependenciaPrivada && mantenedoraSemFinsLucrativos && escolaRegulamentada && emAtividade) {
      $j('#cnpj_mantenedora_principal').makeRequired();
    }
  }

  $j('#situacao_funcionamento').change(
    function(){
      verificaCamposDepAdm();
      obrigaCampoRegulamentacao();
      habilitarCampoUnidadeVinculada();
    }
  );

  $j('#regulamentacao').change(
    function(){
      habilitaCampoEsferaAdministrativa();
    }
  );

  verificaCamposDepAdm();
  habilitaCampoEsferaAdministrativa();

  let verificaLatitudeLongitude = () => {
    let regex = new RegExp('^(\\-?\\d+(\\.\\d+)?)\\.\\s*(\\-?\\d+(\\.\\d+)?)\$');

    let longitude = $j('#longitude').val();

    if (longitude && !regex.exec(longitude)) {
      messageUtils.error('Longitude informada inválida.');
      $j('#longitude').val('').focus();
      longitude = '';
    }

    let latitude = $j('#latitude').val();
    if (latitude && !regex.exec(latitude)) {
      messageUtils.error('Latitude informada inválida.');
      $j('#latitude').val('').focus();
      latitude = '';
    }
    $j('#latitude').makeUnrequired();
    $j('#longitude').makeUnrequired();

    if (obrigarCamposCenso && (latitude || longitude)) {
      $j('#latitude').makeRequired();
      $j('#longitude').makeRequired();
    }

  }

  $j('#latitude').on('change', verificaLatitudeLongitude);
  $j('#longitude').on('change', verificaLatitudeLongitude);
});

const cnpj = document.getElementById('cnpj');

if (cnpj !== null) {
  document.getElementById('cnpj').readOnly = true;
}

function getRedeEnsino(xml_escola_rede_ensino)
{
    var campoRedeEnsino = document.getElementById('ref_cod_escola_rede_ensino');
    var DOM_array = xml_escola_rede_ensino.getElementsByTagName( "escola_rede_ensino" );

    if(DOM_array.length)
    {
        campoRedeEnsino.length = 1;
        campoRedeEnsino.options[0].text = 'Selecione uma rede de ensino';
        campoRedeEnsino.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoRedeEnsino.options[campoRedeEnsino.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_escola_rede_ensino"),false,false);
        }
    }
    else
        campoRedeEnsino.options[0].text = 'A instituição não possui nenhuma rede de ensino';
}

function getCurso(xml_curso)
{
    var campoCurso = document.getElementById('ref_cod_curso');
    var DOM_array = xml_curso.getElementsByTagName( "curso" );

    if(DOM_array.length)
    {
        campoCurso.length = 1;
        campoCurso.options[0].text = 'Selecione um curso';
        campoCurso.disabled = false;

        for( var i = 0; i < DOM_array.length; i++ )
        {
            campoCurso.options[campoCurso.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_curso"),false,false);
        }
    }
    else
        campoCurso.options[0].text = 'A instituição não possui nenhum curso';
}


if ( document.getElementById('ref_cod_instituicao') )
{
    document.getElementById('ref_cod_instituicao').onchange = function()
    {
        var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

        var campoRedeEnsino = document.getElementById('ref_cod_escola_rede_ensino');
        campoRedeEnsino.length = 1;
        campoRedeEnsino.disabled = true;
        campoRedeEnsino.options[0].text = 'Carregando rede de ensino';

        var campoCurso = document.getElementById('ref_cod_curso');
        campoCurso.length = 1;
        campoCurso.disabled = true;
        campoCurso.options[0].text = 'Carregando curso';

        var xml_escola_rede_ensino = new ajax( getRedeEnsino );
        xml_escola_rede_ensino.envia( "educar_escola_rede_ensino_xml.php?ins="+campoInstituicao );

        var xml_curso = new ajax( getCurso );
        xml_curso.envia( "educar_curso_xml2.php?ins="+campoInstituicao );

        if (this.value == '')
        {
            $('img_rede_ensino').style.display = 'none;';
        }
        else
        {
            $('img_rede_ensino').style.display = '';
        }

    }
}

var search = function (request, response) {
    var searchPath = '/module/Api/Servidor?oper=get&resource=servidor-search',
        params = {
            query: request.term
        };

    $j.get(searchPath, params, function (dataResponse) {
        simpleSearch.handleSearch(dataResponse, response);
    });
};

var handleSelect = function (event, ui) {
    var target = $j(event.target),
        id = target.attr('id'),
        idNum = id.match(/\[(\d+)\]/),
        refIdServidor = $j('input[id="servidor_id[' + idNum[1] + ']"]'),
        refInepServidor = $j('input[id="managers_inep_id[' + idNum[1] + ']"]'),
        refEmail = $j('input[id="managers_email[' + idNum[1] + ']"]');

    target.val(ui.item.label);
    refIdServidor.val(ui.item.value);

    var searchPath = '/module/Api/Servidor?oper=get&resource=dados-servidor',
        params = {
            servidor_id: ui.item.value
        };

    $j.get(searchPath, params, function (dataResponse) {
        refInepServidor.val(dataResponse.result.inep);
        refEmail.val(dataResponse.result.email);
    });

    return false;
};

function setAutoComplete() {
    $j.each($j('input[id^="servidor"]'), function (index, field) {
        $j(field).autocomplete({
            source: search,
            select: handleSelect,
            minLength: 1,
            autoFocus: true,
            autoSelect: true,
        });

        $j(field).attr('placeholder', 'Digite um nome para buscar');
    });

    $j('input[id^="servidor"]').blur(function() {
        validateServidor(this)
    });
};

setAutoComplete();

function validateServidor(field){
    var id = $j(field).attr('id'),
        idNum = id.match(/\[(\d+)\]/),
        refIdServidor = $j('input[id="servidor_id[' + idNum[1] + ']"]');

    if ($j(field).val() === '') {
        refIdServidor.val('')
    } else {
        if (refIdServidor.val() === '') {
            messageUtils.error('O campo: <b>Nome do(a) gestor(a)</b> deve ser preenchido com o cadastro de um servidor pré-cadastrado', field);
        }
    }
}

$j('#btn_add_tab_add_1').click(function () {
    setAutoComplete();
    addEventManegerInep();
});

$j.each($j('input[id^="managers_access_criteria_description"]'), function (index, field) {
    $j(field).val(decodeURIComponent($j(field).val().replace(/\+/g, ' ')));
});

$j.each($j('input[id^="managers_email"]'), function (index, field) {
    $j(field).val(decodeURIComponent($j(field).val().replace(/\+/g, ' ')));
});

$j('input[id^="managers_inep_id"]').keyup(function(){
    var oldValue = this.value;

    this.value = this.value.replace(/[^0-9\.]/g, '');
    this.value = this.value.replace('.', '');

    if (oldValue != this.value)
        messageUtils.error('Informe apenas números.', this);
});

addEventManegerInep();

function validateManagerInep(field) {
    if ($j(field).val().length != 12 && $j(field).val().length != 0) {
        messageUtils.error("O campo: Código INEP do gestor(a) deve conter 12 dígitos.");
        $j(field).addClass('error');
    }
}

function addEventManegerInep() {
    $j.each($j('input[id^="managers_inep_id"]'), function (index, field) {
        field.on('blur', function () {
            validateManagerInep(this);
        });
    });
}

function habilitaCamposNumeroSalas() {
    let disabled = $j('#numero_salas_utilizadas_dentro_predio').val() == '' &&
        $j('#numero_salas_utilizadas_fora_predio').val() == '';

    $j('#numero_salas_climatizadas').prop('disabled', disabled);
    $j('#numero_salas_acessibilidade').prop('disabled', disabled);
}

$j('#numero_salas_utilizadas_dentro_predio,#numero_salas_utilizadas_fora_predio').blur(function () {
    habilitaCamposNumeroSalas();
});

function habilitaCampoAcessoInternet() {
    let disabled = $j.inArray(USO_INTERNET.NAO_POSSUI.toString(), $j('#uso_internet').val()) != -1;
    $j('#acesso_internet').prop('disabled', disabled);

    if (!disabled && obrigarCamposCenso) {
        $j('#acesso_internet').makeRequired();
    } else {
        $j('#acesso_internet').makeUnrequired();
    }
}

function habilitaCampoEquipamentosAcessoInternet() {
    let disabled = $j.inArray(USO_INTERNET.ALUNOS.toString(), $j('#uso_internet').val()) == -1;

    $j('#equipamentos_acesso_internet').prop('disabled', disabled);
    $j("#equipamentos_acesso_internet").trigger("chosen:updated");

    if (disabled) {
        $j('#equipamentos_acesso_internet').makeUnrequired();
    } else if(obrigarCamposCenso) {
        $j('#equipamentos_acesso_internet').makeRequired();
    }
}

$j('#uso_internet').on('change', function () {
    habilitaCampoAcessoInternet();
    habilitaCampoEquipamentosAcessoInternet();
});

function habilitaCampoRedeLocal() {
    let disabled = $j.inArray(EQUIPAMENTOS.COMPUTADORES.toString(), $j('#equipamentos').val()) == -1;

    if (disabled) {
        makeUnrequired('rede_local');
    } else if(obrigarCamposCenso){
        makeRequired('rede_local');
    }

    $j('#rede_local').prop('disabled', disabled);

    $j("#rede_local").trigger("chosen:updated");
}
function habilitaCamposQuantidadeComputadoresAlunos() {
    let disabled = $j.inArray(EQUIPAMENTOS_ACESSO_INTERNET.COMPUTADORES, $j('#equipamentos_acesso_internet').val()) == -1;

    $j('#quantidade_computadores_alunos_mesa, #quantidade_computadores_alunos_portateis, #quantidade_computadores_alunos_tablets').prop('disabled', disabled);
    $j("#quantidade_computadores_alunos_mesa, #quantidade_computadores_alunos_portateis, #quantidade_computadores_alunos_tablets").trigger("chosen:updated");
}

$j('#equipamentos').on('change', function () {
    habilitaCampoRedeLocal();
});

$j('#equipamentos_acesso_internet').on('change', function () {
  habilitaCamposQuantidadeComputadoresAlunos();
});

function habilitaCampoEducacaoIndigena() {
    var escolaIndigena = $j('#educacao_indigena').val() == 1;
    if(escolaIndigena && obrigarCamposCenso){
        makeRequired('lingua_ministrada');
    }else{
        makeUnrequired('lingua_ministrada');
        makeUnrequired('codigo_lingua_indigena');
    }

    $j('#lingua_ministrada').prop('disabled', !escolaIndigena);
    habilitaCampoLinguaMinistrada();
}

function habilitaCampoLinguaMinistrada() {
    var linguaIndigena = $j('#lingua_ministrada').val() == 2;
    if(linguaIndigena && obrigarCamposCenso){
        makeRequired('codigo_lingua_indigena');
    }else{
        makeUnrequired('codigo_lingua_indigena');
    }

    $j('#codigo_lingua_indigena').prop('disabled', !linguaIndigena);
    $j("#codigo_lingua_indigena").trigger("chosen:updated");
}

$j('#educacao_indigena').on('change', function() {
    habilitaCampoEducacaoIndigena()
});

$j('#lingua_ministrada').on('change', function() {
    habilitaCampoLinguaMinistrada()
});

function habilitaReservaVagasCotas() {
    var fazExameSelecao = $j('#exame_selecao_ingresso').val() == 1;
    if(fazExameSelecao && obrigarCamposCenso){
        makeRequired('reserva_vagas_cotas');
    }else{
        makeUnrequired('reserva_vagas_cotas');
    }

    $j('#reserva_vagas_cotas').prop('disabled', !fazExameSelecao);
    $j("#reserva_vagas_cotas").trigger("chosen:updated");
}

$j('#exame_selecao_ingresso').on('change', function() {
    habilitaReservaVagasCotas()
});
