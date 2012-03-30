var PAGE_URL_BASE = 'reserva';
var API_URL_BASE = 'reservaApi';

var RESOURCE_NAME = 'exemplar';
var RESOURCES_NAME = 'exemplares';

var onClickDestroyEvent = false;

var onClickActionEvent = function(event){
  $this = $j(this);
  var $firstChecked = $j('input.exemplar:checked:first');

  if ($firstChecked.length < 1)
    handleMessages([{type : 'error', msg : 'Selecione algum exemplar.'}], $this, true);
  else{
    $j('.disable-on-apply-changes').attr('disabled', 'disabled');
    $this.val('Aguarde reservando...');
    postResource($firstChecked);
  }
};

var onClickSelectAllEvent = function(event){
  // TODO
  console.log('#TODO onClickSelectAllEvent');
};

var postResource = function ($resourceElement) {
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
      exemplar_id : $j('#exemplar_id').val()
    },

    success : function(dataResponse){
      removeImgLoadingFor($resourceElement);
      handlePost(dataResponse);
    }
  };

  appendImgLoadingTo($resourceElement);
  postResource(options, handleErrorPost);
};

var handlePost = function(dataResponse){
  console.log('#TODO handlePost');
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
                    .data('id', value.id);

    var situacoesReservaPermitida = ['emprestado', 'reservado', 'emprestado_e_reservado'];

    if ($j.inArray(value.situacao.flag, situacoesReservaPermitida) < 0)
      $checkbox.attr('disabled', 'disabled');

    var $linha = $j('<tr />');
    $j('<td />').html($checkbox).addClass('center').appendTo($linha);
    $j('<td />').html(value.id).addClass('center').appendTo($linha);

    var $colSituacao = $j('<td />').attr('id', 'situacao-' + value.id).addClass('situacao center');
    var $colCliente                = $j('<td />');
    var $colData                   = $j('<td />').addClass('center');
    var $colDataPrevistaDisponivel = $j('<td />').addClass('center');

    $j.each(value.pendencias, function(indexPendencia, valuePendencia){
      $j('<p />').html(valuePendencia.situacao.label || '-').appendTo($colSituacao);
      $j('<p />').html(valuePendencia.nome_cliente || '-').appendTo($colCliente);
      $j('<p />').html(valuePendencia.data || '-').appendTo($colData);
      $j('<p />').html(valuePendencia.data_prevista_disponivel || '-').appendTo($colDataPrevistaDisponivel);
    });

    if (value.pendencias.length < 1)
      $j('<p />').html(value.situacao.label || '-').appendTo($colSituacao);

    $colSituacao.data('situacao', value.situacao_exemplar);

    $colSituacao.appendTo($linha);
    $colCliente.appendTo($linha);
    $colData.appendTo($linha);
    $colDataPrevistaDisponivel.appendTo($linha);

    $linha.fadeIn('slow').appendTo($resultTable);
  });// each

  $resultTable.find('tr:even').addClass('even');
  $resultTable.addClass('styled').find('checkbox:first').focus();
}
