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
                               'dependencia_lavanderia',
                               'dependencia_unidade_climatizada'];

$escolaInepIdField.closest('tr').hide();

var submitForm = function(){
  var canSubmit = validationUtils.validatesFields();

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
  var data = {
    id             : $escolaIdField.val(),
    escola_inep_id : $escolaInepIdField.val()
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
            }
        );
    }
);

//abas

// hide nos campos das outras abas (deixando só os campos da primeira aba)
if (!$j('#cnpj').is(':visible')){

  $j('td .formdktd').append('<div id="tabControl"><ul><li><div id="tab1" class="escolaTab"> <span class="tabText">Dados gerais</span></div></li><li><div id="tab2" class="escolaTab"> <span class="tabText">Infraestrutura</span></div></li><li><div id="tab3" class="escolaTab"> <span class="tabText">Depend\u00eancias</span></div></li><li><div id="tab4" class="escolaTab"> <span class="tabText">Equipamentos</span></div></li><li><div id="tab5" class="escolaTab"> <span class="tabText">Dados do ensino</span></div></li></ul></div>');
  $j('td .formdktd b').remove();
  $j('#tab1').addClass('escolaTab-active').removeClass('escolaTab');

  // Atribui um id a linha, para identificar até onde/a partir de onde esconder os campos
  $j('#condicao').closest('tr').attr('id','tcondicao');
  $j('#marcar_todas_dependencias').closest('tr').attr('id','tmarcar_todas_dependencias');
  $j('#televisoes').closest('tr').attr('id','ttelevisoes');
  $j('#atendimento_aee').closest('tr').attr('id','tatendimento_aee');

  // Pega o número dessa linha
  linha_inicial_infra = $j('#tcondicao').index()-1;
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

      if($j('#dependencia_administrativa').val() == 4){
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
});