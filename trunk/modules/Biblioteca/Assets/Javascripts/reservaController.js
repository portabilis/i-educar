var PAGE_URL_BASE = 'reserva';
var API_URL_BASE = 'reservaApi';

var RESOURCE_NAME = 'exemplar';
var RESOURCES_NAME = 'exemplares';

var onClickActionEvent = function(event){
  // TODO
};

var onClickSelectAllEvent = function(event){
  // TODO
};

var onClickDestroyEvent = function(event){
  // TODO
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
  $j('<th />').html('Data reserva').addClass('center').appendTo($linha);
  $j('<th />').html('Data prevista devolu&#231;&#227;o').addClass('center').appendTo($linha);
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

    if (value.situacao.flag != 'disponivel')
      $checkbox.attr('disabled', 'disabled');

    var $linha = $j('<tr />');
    $j('<td />').html($checkbox).addClass('center').appendTo($linha);
    $j('<td />').html(value.id).addClass('center').appendTo($linha);

    $j('<td />').html(value.situacao.label).data('situacao', value.situacao).attr('id', 'situacao-' + value.id).addClass('situacao').addClass('center').appendTo($linha);

    $j('<td />').html(value.cliente_reserva).appendTo($linha);
    $j('<td />').html(value.data_reserva).addClass('center').appendTo($linha);
    $j('<td />').html(value.data_devolucao_prevista).addClass('center').appendTo($linha);

    $linha.fadeIn('slow').appendTo($resultTable);
  });// each

  $resultTable.find('tr:even').addClass('even');
  $resultTable.addClass('styled').find('checkbox:first').focus();
}
