// variaveis usadas pelo modulo Frontend/Process.js

processOptions.validatesResourcesAfterSearch = false;

// #DEPRECADO, migrar para novo padrao processOptions
var PAGE_URL_BASE      = 'promocao';
var API_URL_BASE       = 'promocaoApi';
var RESOURCE_NAME      = 'promocao';
var RESOURCES_NAME     = 'quantidade_matriculas';
var POST_LABEL         = '';
var DELETE_LABEL       = '';
var SEARCH_ORIENTATION = '';

// funcoes usados pelo modulo Frontend/Process.js
var onClickSelectAllEvent = false;
var onClickActionEvent    = false;
var onClickDeleteEvent    = false;

//url builders

var setTableSearchDetails = function(){ }

var postPromocaoMatricula = function(){
  var $proximoMatriculaIdField = $j('#proximo-matricula-id');
  $proximoMatriculaIdField.data('initial_matricula_id', $proximoMatriculaIdField.val())

  if (validatesIfValueIsNumeric($proximoMatriculaIdField.val())) {
    var options = {
      url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'promocao', {matricula_id : $proximoMatriculaIdField.val()}),
      dataType : 'json',
      data : {
        instituicao_id : $j('#instituicao_id').val(),
        ano_escolar : $j('#ano_escolar').val(),
      },
      success : handlePostPromocaoMatricula
    };

    postResource(options);
  }
}


var deleteOldComponentesCurriculares = function() {
  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, 'old_componentes_curriculares', {ano_escolar : $j('#ano_escolar').val()}),
    dataType : 'json',
    data : {},
    success : handleDelete
  };

  deleteResource(options);
}


//callback handlers

function handlePostPromocaoMatricula(dataResponse){
  handleMessages(dataResponse.msgs);

  var $proximoMatriculaIdField = $j('#proximo-matricula-id');
  $proximoMatriculaIdField.val(dataResponse.result.proximo_matricula_id);

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
  var $text = $j('<p />').html('Quantidade de matrículas em andamento: ' +
                              dataResponse.quantidade_matriculas + '<br />');

  $j('<input />').attr('type', 'checkbox').attr('id', 'continuar-processo').attr('name', 'continuar-processo').appendTo($text);
  $j('<span />').html('Continuar processo <br />').appendTo($text);

  $j('<span />').html('proxima matricula:').appendTo($text);
  $j('<input />').attr('type', 'text').attr('name', 'proximo-matricula-id').attr('id', 'proximo-matricula-id').val('0').appendTo($text);

  $j('<br />').appendTo($text);

  $j('<a />').attr('id', 'promover-matricula')
            .attr('href', '#')
            .html('Iniciar processo')
            .attr('style', 'text-decoration:underline')
            .bind('click', postPromocaoMatricula)
            .appendTo($text);

  $j('<span />').html(' ').appendTo($text);

  $j('<a />').attr('id', 'delete-old-componentes-curriculares')
            .attr('href', '#')
            .html('Limpar antigos componentes curriculares')
            .attr('style', 'text-decoration:underline')
            .bind('click', deleteOldComponentesCurriculares)
            .appendTo($text);

  $j('<span />').html(' ').appendTo($text);

  $j('<a />').attr('id', 'clear-messages')
            .attr('href', '#')
            .html('Limpar mensagens')
            .attr('style', 'text-decoration:underline')
            .bind('click', function(){
              $j('#feedback-messages').children().remove();
              $j('#feedback-messages-success').children().remove();
            })
            .appendTo($text);

  $j('<td />').html($text).appendTo($j('<tr />').appendTo($resultTable));
}