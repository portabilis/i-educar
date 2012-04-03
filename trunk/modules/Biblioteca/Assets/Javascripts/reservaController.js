var PAGE_URL_BASE = 'reserva';
var API_URL_BASE = 'reservaApi';

var RESOURCE_NAME = 'exemplar';
var RESOURCES_NAME = 'exemplares';

var ACTION_NAME = 'Reservar';

var onClickDestroyEvent = false;

var onClickActionEvent = function(event){
  var $this = $j(this);
  var $firstChecked = $j('input.exemplar:checked:first');

  if ($firstChecked.length < 1)
    handleMessages([{type : 'error', msg : 'Selecione algum exemplar.'}], $this, true);
  else{
    $j('.disable-on-apply-changes').attr('disabled', 'disabled');
    $this.val('Aguarde reservando...');
    postReserva($firstChecked);
  }
};

var onClickSelectAllEvent = function(event){
  // TODO
  console.log('#TODO onClickSelectAllEvent');
};

var postReserva = function ($resourceElement) {
  // TODO
  console.log('#TODO postProcessamento');

  var options = {
    url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'reserva'),
    dataType : 'json',
    data : {
      ref_cod_instituicao : $j('#ref_cod_instituicao').val(),
      ref_cod_escola : $j('#ref_cod_escola').val(),
      ref_cod_biblioteca : $j('#ref_cod_biblioteca').val(),
      ref_cod_cliente : $j('#ref_cod_cliente').val(),
      ref_cod_acervo : $j('#ref_cod_acervo').val(),
      exemplar_id : $resourceElement.data('exemplar_id')
    },

    success : function(dataResponse){
      afterChangeResource($resourceElement);
      handlePost(dataResponse);
    }
  };

  appendImgLoadingTo($resourceElement);
  postResource(options, handleErrorPost);
};

var handlePost = function(dataResponse){
  console.log('#TODO handlePost');

  //try{
    var $checkbox = $j('exemplar-' + dataResponse.id);
    var $targetElement = $j('#exemplar-'+dataResponse.id).closest('tr').first();
    handleMessages(dataResponse.msgs, $targetElement);
    updateResourceRow(dataResponse);

  /*}
  catch(error){
    //TODO causar excessao dentro do try, corrigir erro não declaração showNewSearchButton
    showNewSearchButton();
    handleMessages([{type : 'error', msg : 'Ocorreu um erro ao enviar o processamento, por favor tente novamente, detalhes: ' + error}], '');

    safeLog(dataResponse);
  }*/
};


function setTableSearchDetails($tableSearchDetails, dataDetails){
  $j('<caption />').html('<strong>Reserva exemplares</strong>').appendTo($tableSearchDetails);

  //set headers table
  var $linha = $j('<tr />');
  $j('<th />').html('Cliente').appendTo($linha);
  $j('<th />').html('Obra').appendTo($linha);
  $j('<th />').html('Biblioteca').appendTo($linha);
  $j('<th />').html('Escola').appendTo($linha);

  $linha.appendTo($tableSearchDetails);

  var $linha = $j('<tr />').addClass('even');

  $j('<td />').html($j('#nome_cliente').val()).appendTo($linha);
  $j('<td />').html($j('#titulo_obra').val()).appendTo($linha);

  //field biblioteca pode ser diferente de select caso usuario comum
  var $htmlBibliotecaField = $j('#ref_cod_biblioteca').children("[selected='selected']").html() ||
                         $j('#tr_nm_biblioteca span:last').html();
  $j('<td />').html(safeToUpperCase($htmlBibliotecaField)).appendTo($linha);

  //field escola pode ser diferente de select caso usuario comum
  var $htmlEscolaField = $j('#ref_cod_escola').children("[selected='selected']").html() ||
                         $j('#tr_nm_escola span:last').html();
  $j('<td />').html(safeToUpperCase($htmlEscolaField)).appendTo($linha);

  $linha.appendTo($tableSearchDetails);
  $tableSearchDetails.show();

  $tableSearchDetails.data('details', dataDetails);
}


function handleSearch($resultTable, dataResponse) {
  var $linha = $j('<tr />');
  $j('<th />').html('Selecionar').addClass('center').appendTo($linha);
  $j('<th />').html('Id').addClass('center').appendTo($linha);
  $j('<th />').html('Situa&#231;&#227;o').addClass('center').appendTo($linha);
  $j('<th />').html('Cliente').appendTo($linha);
  $j('<th />').html('Data').addClass('center').appendTo($linha);
  $j('<th />').html('Data prevista dispon&iacute;vel').addClass('center').appendTo($linha);
  $linha.appendTo($resultTable);

  //set rows
  $j.each(dataResponse[RESOURCES_NAME], function(index, value){

    var $checkbox = $j('<input />')
                    .attr('type', 'checkbox')
                    .attr('name', 'reservar-exempar')
                    .attr('value', 'sim')
                    .attr('id', 'exemplar-' + value.id)
                    .attr('class', 'exemplar disable-on-apply-changes')
                    .data('exemplar_id', value.id);

    var situacoesReservaPermitida = ['emprestado', 'reservado', 'emprestado_e_reservado'];

    if ($j.inArray(value.situacao.flag, situacoesReservaPermitida) < 0)
      $checkbox.attr('disabled', 'disabled').removeClass('disable-on-apply-changes');

    var $linha = $j('<tr />');
    $j('<td />').html($checkbox).addClass('center').appendTo($linha);
    $j('<td />').html(value.id).addClass('center').appendTo($linha);

    var $colSituacao               = $j('<td />').attr('id', 'situacao-' + value.id).addClass('situacao center');
    var $colCliente                = $j('<td />').attr('id', 'clientes-' + value.id);
    var $colData                   = $j('<td />').attr('id', 'datas-' + value.id).addClass('center');
    var $colDataPrevistaDisponivel = $j('<td />').attr('id', 'datas-prevista-disponivel-' + value.id).addClass('center');

    $colSituacao.appendTo($linha);
    $colCliente.appendTo($linha);
    $colData.appendTo($linha);
    $colDataPrevistaDisponivel.appendTo($linha);

    $linha.appendTo($resultTable);
    updateResourceRow(value);
  });// each

  $resultTable.find('tr:even').addClass('even');
  $resultTable.addClass('styled').find('checkbox:first').focus();
}


function updateResourceRow(exemplar){
  console.log(exemplar);
  var $linha = $j('#exemplar-' + exemplar.id).closest('tr');

  var $colSituacao               = $j('#situacao-' + exemplar.id).html('');
  var $colCliente                = $j('#clientes-' + exemplar.id).html('');
  var $colData                   = $j('#datas-' + exemplar.id).html('');
  var $colDataPrevistaDisponivel = $j('#datas-prevista-disponivel-' + exemplar.id).html('');

  $j.each(exemplar.pendencias, function(index, value){
    $j('<p />').html(value.situacao.label || '-').appendTo($colSituacao);
    $j('<p />').html(value.nome_cliente || '-').appendTo($colCliente);
    $j('<p />').html(value.data || '-').appendTo($colData);
    $j('<p />').html(value.data_prevista_disponivel || '-').appendTo($colDataPrevistaDisponivel);
  });

  if (exemplar.pendencias.length < 1)
    $j('<p />').html(exemplar.situacao.label || '-').appendTo($colSituacao);

  $colSituacao.data('situacao', exemplar.situacao_exemplar);
}
