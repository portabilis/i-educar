var $submitButton      = $j('#btn_enviar');
var $escolaInepIdField = $j('#escola_inep_id');
var $escolaIdField     = $j('#cod_escola');
var $arrayCheckDependencias = ['dependencia_sala_diretoria',
                               'dependencia_sala_professores',
                               'dependencia_sala_secretaria',
                               'dependencia_laboratorio_informatica',
                               'dependencia_laboratorio_ciencias',
                               'dependencia_sala_aee',
                               'dependencia_quadra_coberta',
                               'dependencia_quadra_descoberta',
                               'dependencia_cozinha',
                               'dependencia_biblioteca',
                               'dependencia_sala_leitura',
                               'dependencia_parque_infantil',
                               'dependencia_bercario',
                               'dependencia_banheiro_fora',
                               'dependencia_banheiro_dentro',
                               'dependencia_banheiro_infantil',
                               'dependencia_banheiro_deficiente',
                               'dependencia_banheiro_chuveiro',
                               'dependencia_vias_deficiente',
                               'dependencia_refeitorio',
                               'dependencia_dispensa',
                               'dependencia_aumoxarifado',
                               'dependencia_auditorio',
                               'dependencia_patio_coberto',
                               'dependencia_patio_descoberto',
                               'dependencia_alojamento_aluno',
                               'dependencia_alojamento_professor',
                               'dependencia_area_verde',
                               'dependencia_lavanderia'];

$escolaInepIdField.closest('tr').hide();

var submitForm = function(){
  var canSubmit = validationUtils.validatesFields(true);

  // O campo escolaInepId somente é atualizado ao cadastrar escola,  uma vez que este
  // é atualizado via ajax, e durante o (novo) cadastro a escola ainda não possui id.
  //
  // #TODO refatorar cadastro de escola para que todos campos sejam enviados via ajax,
  // podendo então definir o código escolaInepId ao cadastrar a escola.

  if (canSubmit && $escolaIdField.val())
    putEscola();
  else if (canSubmit)
    acao();
}

var handleGetEscola = function(dataResponse) {
  handleMessages(dataResponse.msgs);

  $escolaInepIdField.val(dataResponse.escola_inep_id);
}

var handlePutEscola = function(dataResponse) {
  handleMessages(dataResponse.msgs);

  // submete formulário somente após put (para não interromper requisição ajax)
  acao();
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

var putEscola = function() {
  var inep = $escolaInepIdField.val().length == 8 ? $escolaInepIdField.val() : '';
  var data = {
    id             : $escolaIdField.val(),
    escola_inep_id : inep
  };

  var options = {
    url      : putResourceUrlBuilder.buildUrl('/module/Api/escola', 'escola'),
    dataType : 'json',
    data     : data,
    success  : handlePutEscola
  };

  putResource(options);
}

if ($escolaIdField.val()) {
  getEscola($escolaIdField.val());
  $escolaInepIdField.closest('tr').show();
}

// unbind events
$submitButton.removeAttr('onclick');
$j(document.formcadastro).removeAttr('onsubmit');

// bind events
$submitButton.click(submitForm);

$j('#marcar_todas_dependencias').click(
    function(){
        var check = $j('#marcar_todas_dependencias').is(':checked');
        $arrayCheckDependencias.each(
            function(idElement){
                $j( '#' + idElement).prop("checked",check);
                var on = check ? 'on' : '';
                $j( '#' + idElement).val(on);
            }
        );
    }
);

let obrigarCamposCenso = $j('#obrigar_campos_censo').val() == '1';

$j('#local_funcionamento').change(
  function(){
      var disabled = this.value != 3;
      $j('#condicao').prop("disabled",disabled);
      $j('#codigo_inep_escola_compartilhada').prop("disabled",disabled);
      $j('#codigo_inep_escola_compartilhada2').prop("disabled",disabled);
      $j('#codigo_inep_escola_compartilhada3').prop("disabled",disabled);
      $j('#codigo_inep_escola_compartilhada4').prop("disabled",disabled);
      $j('#codigo_inep_escola_compartilhada5').prop("disabled",disabled);
      $j('#codigo_inep_escola_compartilhada6').prop("disabled",disabled);
      $j('#condicao').makeUnrequired();
      $j('#dependencia_numero_salas_existente').makeUnrequired();
      if (!disabled && obrigarCamposCenso) {
        $j('#condicao').makeRequired();
        $j('#dependencia_numero_salas_existente').makeRequired();
      }
  }
).trigger('change');

$j('#educacao_indigena').change(
  function(){
      var escolaIndigena = this.value == 1;
      if(escolaIndigena){
        makeRequired('lingua_ministrada');
        $j('#lingua_ministrada').prop('disabled', false);
      }else{
        makeUnrequired('lingua_ministrada');
        makeUnrequired('codigo_lingua_indigena');
        $j('#lingua_ministrada').prop('disabled', true);
        $j('#codigo_lingua_indigena').prop('disabled', true);
        $j('#lingua_ministrada').val(1)
      }
  }
);
$j('#lingua_ministrada').change(
  function(){
      var linguaIndigena = this.value == 2;
      if(linguaIndigena){
        makeRequired('codigo_lingua_indigena');
        $j('#codigo_lingua_indigena').prop('disabled', false);
      }else{
        makeUnrequired('codigo_lingua_indigena');
        $j('#codigo_lingua_indigena').prop('disabled', true);
      }
  }
);

$j('#computadores').change(
  function(){
      var possuiComputadores = this.value > 0;
      $j('#acesso_internet').prop('disabled', !possuiComputadores);
      $j('#acesso_internet').makeUnrequired();
      if (possuiComputadores && obrigarCamposCenso) {
        $j('#acesso_internet').makeRequired();
      }
  }
).trigger('change');

//abas

// hide nos campos das outras abas (deixando só os campos da primeira aba)
if (!$j('#cnpj').is(':visible')){

  $j('td .formdktd').append('<div id="tabControl"><ul><li><div id="tab1" class="escolaTab"> <span class="tabText">Dados gerais</span></div></li><li><div id="tab2" class="escolaTab"> <span class="tabText">Infraestrutura</span></div></li><li><div id="tab3" class="escolaTab"> <span class="tabText">Depend\u00eancias</span></div></li><li><div id="tab4" class="escolaTab"> <span class="tabText">Equipamentos</span></div></li><li><div id="tab5" class="escolaTab"> <span class="tabText">Dados do ensino</span></div></li></ul></div>');
  $j('td .formdktd b').remove();
  $j('#tab1').addClass('escolaTab-active').removeClass('escolaTab');

  // Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
  $j('#local_funcionamento').closest('tr').attr('id','tlocal_funcionamento');
  $j('#marcar_todas_dependencias').closest('tr').attr('id','tmarcar_todas_dependencias');
  $j('#televisoes').closest('tr').attr('id','ttelevisoes');
  $j('#atendimento_aee').closest('tr').attr('id','tatendimento_aee');

  // Pega o número dessa linha
  linha_inicial_infra = $j('#tlocal_funcionamento').index()-1;
  linha_inicial_dependencia = $j('#tmarcar_todas_dependencias').index()-1;
  linha_inicial_equipamento = $j('#ttelevisoes').index()-1;
  linha_inicial_dados = $j('#tatendimento_aee').index()-1;

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
    });

  // EQUIPAMENTOS
  $j('#tab4').click(
    function(){
      $j('.escolaTab-active').toggleClass('escolaTab-active escolaTab');
      $j('#tab4').toggleClass('escolaTab escolaTab-active')
      $j('.tablecadastro >tbody  > tr').each(function(index, row) {
        if (row.id!='stop'){
          if (index>=linha_inicial_equipamento && index < linha_inicial_dados){
            row.show();
          }else if (index>0){
            row.hide();
          }
        }else
          return false;
      });
    });

  // Dados educacionais
  $j('#tab5').click(
    function(){
      $j('.escolaTab-active').toggleClass('escolaTab-active escolaTab');
      $j('#tab5').toggleClass('escolaTab escolaTab-active')
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

      if($j('#dependencia_administrativa').val() == 4 && $j('#situacao_funcionamento').val() == '1'){
            $j('#categoria_escola_privada').closest('tr').show();
            $j('#conveniada_com_poder_publico').closest('tr').show();
            $j('#mantenedora_escola_privada').closest('tr').show();
            $j('#cnpj_mantenedora_principal').closest('tr').show();
        }else{
            $j('#categoria_escola_privada').closest('tr').hide();
            $j('#conveniada_com_poder_publico').closest('tr').hide();
            $j('#mantenedora_escola_privada').closest('tr').hide();
            $j('#cnpj_mantenedora_principal').closest('tr').hide();
        }
      });

  // fix checkboxs
  $j('input:checked').val('on');

  let verificaCamposDepAdm = () => {
    $j('#categoria_escola_privada').makeUnrequired();
    $j('#conveniada_com_poder_publico').makeUnrequired();
    $j('#mantenedora_escola_privada').makeUnrequired();
    $j('#cnpj_mantenedora_principal').makeUnrequired();
    if (obrigarCamposCenso && $j('#situacao_funcionamento').val() == '1' && $j('#dependencia_administrativa').val() == '4'){
      $j('#categoria_escola_privada').makeRequired();
      $j('#conveniada_com_poder_publico').makeRequired();
      $j('#mantenedora_escola_privada').makeRequired();
      $j('#cnpj_mantenedora_principal').makeRequired();
    }
  }

  $j('#dependencia_numero_salas_existente').on('change', () => {
    if ($j('#dependencia_numero_salas_existente').val() && ! parseInt($j('#dependencia_numero_salas_existente').val()) > 0) {
      messageUtils.error('O campo: Número de salas de aula existentes na escola, deve ser preenchido com um número maior que zero.');
      $j('#dependencia_numero_salas_existente').val('').focus().addClass('error');
    } else {
      $j('#dependencia_numero_salas_existente').removeClass('error');
    }
  });

  $j('#dependencia_administrativa').on('change', verificaCamposDepAdm);
  $j('#situacao_funcionamento').on('change', verificaCamposDepAdm);
  verificaCamposDepAdm();

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

document.getElementById('cnpj').readOnly = true;

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
