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


function handleSearch(dataResponse) {
  // TODO
}
