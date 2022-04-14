// variaveis usadas pelo modulo Frontend/Process.js

processOptions.validatesResourcesAfterSearch = false;

// #DEPRECADO, migrar para novo padrao processOptions
const PAGE_URL_BASE      = 'promocao';
const API_URL_BASE       = 'promocaoApi';
const RESOURCE_NAME      = 'promocao';
const RESOURCES_NAME     = 'quantidade_matriculas';
let POST_LABEL           = '';
let DELETE_LABEL         = '';
let SEARCH_ORIENTATION   = '';

// funcoes usados pelo modulo Frontend/Process.js
let onClickSelectAllEvent = false;
let onClickActionEvent    = false;
let onClickDeleteEvent    = false;

//url builders

let setTableSearchDetails = function(){};

let postPromocaoMatricula = function(){
  let $proximoMatriculaIdField = $j('#proximo-matricula-id');
  $proximoMatriculaIdField.data('initial_matricula_id', $proximoMatriculaIdField.val());

  if (validatesIfValueIsNumeric($proximoMatriculaIdField.val())) {
    let options = {
      url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'promocao', {matricula_id : $proximoMatriculaIdField.val()}),
      dataType : 'json',
      data : {
        instituicao_id : $j('#instituicao_id').val(),
        ano : $j('#ano').val(),
        escola : $j('#escola').val(),
        curso : $j('#curso').val(),
        serie : $j('#serie').val(),
        turma : $j('#turma').val(),
        regras_avaliacao_id : $j('#regras_avaliacao_id').val()
      },
      success : handlePostPromocaoMatricula,
      error : handlePostPromocaoMatricula
    };
    postResource(options);
  }
};

let deleteOldComponentesCurriculares = function() {
  let options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, 'old_componentes_curriculares', {ano : $j('#ano').val()}),
    dataType : 'json',
    data : {},
    success : handleDelete
  };

  deleteResource(options);
};

//callback handlers

function handlePostPromocaoMatricula(dataResponse){
  handleMessages(dataResponse.msgs);

  let $proximoMatriculaIdField = $j('#proximo-matricula-id');

  let $proximaMatricula = ((dataResponse.any_error_msg) ?  (parseInt($proximoMatriculaIdField.val()) + parseInt(1)) : dataResponse.result.proximo_matricula_id);

  $proximoMatriculaIdField.val($proximaMatricula);

  if($j('#continuar-processo').is(':checked') &&
     $j.isNumeric($proximoMatriculaIdField.val()) &&
     $proximoMatriculaIdField.data('initial_matricula_id') != $proximoMatriculaIdField.val()){
    $j('#promover-matricula').click();
  }
  else if(($j('#continuar-processo').is(':checked') &&
         $proximoMatriculaIdField.data('initial_matricula_id') == $proximoMatriculaIdField.val()) ||
         ! $j.isNumeric($proximoMatriculaIdField.val())){
    alert('Processo finalizado');
  }
}


function handleDelete(dataResponse){
  handleMessages(dataResponse.msgs);
}

function handleSearch($resultTable, dataResponse) {

  let $text = $j('<div />');

  $j('<span />')
    .html('Quantidade de matrículas ativas na rede : ' + '<b>' + dataResponse.quantidade_matriculas + '<b><br />')
    .attr('class','qnt-matriculas')
    .appendTo($text);

  $j('<span />')
    .html('Próxima matrícula:')
    .attr('class','qnt-matriculas')
    .appendTo($text);

  $j('<input />').attr('type', 'text')
    .attr('name', 'proximo-matricula-id')
    .attr('id', 'proximo-matricula-id')
    .attr('class','proximo-matricula')
    .val('0').appendTo($text);

  $j('<br />').appendTo($text);

  $j('<input />')
    .attr('type', 'checkbox')
    .attr('id', 'continuar-processo')
    .attr('name', 'continuar-processo')
    .attr('class','continuar-processo')
    .appendTo($text);

  $j('<span />')
    .html('Continuar processo <br />')
    .attr('class','qnt-matriculas')
    .appendTo($text);

  $j('<input />').attr('id', 'promover-matricula')
            .attr('href', '#')
            .attr('type','button')
            .attr('class','btn-green')
            .attr('value','Iniciar processo')
            .bind('click', postPromocaoMatricula)
            .appendTo($text);

  $j('<span />').html(' ').appendTo($text);

  $j('<input />').attr('id', 'delete-old-componentes-curriculares')
            .attr('href', '#')
            .attr('type','button')
            .attr('class','btn-danger')
            .attr('value','Limpar antigos componentes curriculares')
            .bind('click', deleteOldComponentesCurriculares)
            .appendTo($text);

  $j('<td />').html($text).appendTo($j('<tr />').appendTo($resultTable));
}
