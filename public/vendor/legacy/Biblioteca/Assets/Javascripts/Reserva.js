var PAGE_URL_BASE      = 'reserva';
var API_URL_BASE       = 'reservaApi';
var RESOURCE_NAME      = 'exemplar';
var RESOURCES_NAME     = 'exemplares';
var POST_LABEL         = 'Reservar';
var DELETE_LABEL       = 'Cancelar';
var SEARCH_ORIENTATION = '';

var onClickSelectAllEvent = false;
var onClickDeleteEvent    = false;

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

var postReserva = function ($resourceCheckbox) {
  var options = {
    url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'reserva'),
    dataType : 'json',
    data : {
      ref_cod_instituicao : $j('#ref_cod_instituicao').val(),
      ref_cod_escola : $j('#ref_cod_escola').val(),
      ref_cod_biblioteca : $j('#ref_cod_biblioteca').val(),
      ref_cod_cliente : $j('#ref_cod_cliente').val(),
      ref_cod_acervo : $j('#ref_cod_acervo').val(),
      exemplar_id : $resourceCheckbox.data('exemplar_id')
    },

    success : function(dataResponse){
      afterChangeResource($resourceCheckbox);
      handlePost(dataResponse);
    }
  };

  beforeChangeResource($resourceCheckbox);
  postResource(options);
};

var handlePost = function(dataResponse){
  var $targetElement = $j('#exemplar-'+dataResponse.id).closest('tr').first();
  handleMessages(dataResponse.msgs, $targetElement);
  updateResourceRow(dataResponse);
};

var onClickCancelEvent = function(event) {
  if (confirm("Confirma cancelamento da reserva?")) {
    var $this = $j(this);

    //var $checkbox = $this.closest('tr').find("input[type='checkbox']").first();
    deleteReserva($this);
  }
}

var deleteReserva = function($deleteLink) {
  var options = {
    url : deleteResourceUrlBuilder.buildUrl(API_URL_BASE, 'reserva', {
      ref_cod_instituicao : $j('#ref_cod_instituicao').val(),
      ref_cod_escola : $j('#ref_cod_escola').val(),
      ref_cod_biblioteca : $j('#ref_cod_biblioteca').val(),
      ref_cod_cliente : $j('#ref_cod_cliente').val(),
      ref_cod_acervo : $j('#ref_cod_acervo').val(),
      exemplar_id : $deleteLink.data('exemplar_id'),
      reserva_id : $deleteLink.data('reserva_id')
    }),
    dataType : 'json',
    data : {
    },
    success : function(dataResponse){
      afterChangeResource($deleteLink);
      handleDeleteReserva(dataResponse);
    }
  };

  beforeChangeResource($deleteLink);
  deleteResource(options);
}

var handleDeleteReserva = function(dataResponse) {
  safeLog(dataResponse);

  //try{
    var $targetElement = $j('#exemplar-' + dataResponse.id).closest('tr').first();
    handleMessages(dataResponse.msgs, $targetElement);
    updateResourceRow(dataResponse);
  //}
  //catch(error){
    //showNewSearchButton();
    //handleMessages([{type : 'error', msg : 'Ocorreu um erro ao remover o recurso, por favor tente novamente, detalhes: ' + error}], '');

    //safeLog(dataResponse);
  //}
}

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
  $j('<th />').html('Data prevista disponível').addClass('center').appendTo($linha);
  $j('<th />').html('A&#231;&#227;o').addClass('center').appendTo($linha);
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

    var situacoesReservaPermitida = ['disponivel', 'emprestado', 'reservado', 'emprestado_e_reservado'];

    if ($j.inArray(value.situacao.flag, situacoesReservaPermitida) < 0)
      $checkbox.attr('disabled', 'disabled').removeClass('disable-on-apply-changes');

    var $linha = $j('<tr />');
    $j('<td />').html($checkbox).addClass('center').appendTo($linha);
    $j('<td />').html(value.id).addClass('center').appendTo($linha);

    var $colSituacoes               = $j('<td />').attr('id', 'situacoes-' + value.id).addClass('situacoes center');
    var $colClientes                = $j('<td />').attr('id', 'clientes-' + value.id);
    var $colDatas                   = $j('<td />').attr('id', 'datas-' + value.id).addClass('center');
    var $colDatasPrevistaDisponivel = $j('<td />').attr('id', 'datas-prevista-disponivel-' + value.id).addClass('center');
    var $colAcoes                   = $j('<td />').attr('id', 'acoes-' + value.id).addClass('center');

    $colSituacoes.appendTo($linha);
    $colClientes.appendTo($linha);
    $colDatas.appendTo($linha);
    $colDatasPrevistaDisponivel.appendTo($linha);
    $colAcoes.appendTo($linha);

    $linha.appendTo($resultTable);
    updateResourceRow(value);
  });// each

  $resultTable.find('tr:even').addClass('even');
  $resultTable.addClass('styled').find('checkbox:first').focus();

  var $checkboxes = $resultTable.find("input[type='checkbox']");
  $checkboxes.change(function(){
    $checkboxes.not(this).removeAttr('checked');
  });
}


function updateResourceRow(exemplar){
  var $colSituacoes               = $j('#situacoes-' + exemplar.id).html('');
  var $colClientes                = $j('#clientes-' + exemplar.id).html('');
  var $colDatas                   = $j('#datas-' + exemplar.id).html('');
  var $colDatasPrevistaDisponivel = $j('#datas-prevista-disponivel-' + exemplar.id).html('');
  var $colAcoes                   = $j('#acoes-' + exemplar.id).html('');

  if ($j.isArray(exemplar.pendencias)) {
    $j.each(exemplar.pendencias, function(index, value){
      $j('<p />').html(value.situacao.label || '-').appendTo($colSituacoes);
      $j('<p />').html(value.nome_cliente || '-').appendTo($colClientes);
      $j('<p />').html(value.data || '-').appendTo($colDatas);
      $j('<p />').html(value.data_prevista_disponivel || '-').appendTo($colDatasPrevistaDisponivel);

      if (value.situacao.flag == 'reservado' && value.cliente && value.cliente.id == $j('#ref_cod_cliente').val()) {
        var $linkToDelete = $j("<a href='#' class='disable-on-apply-changes'>Cancelar reserva</a>").click(onClickCancelEvent).data('exemplar_id', exemplar.id).data('reserva_id', value.reserva_id);
        $j('<p />').html($linkToDelete).appendTo($colAcoes);
      }
      else
        $j('<p />').html('-').appendTo($colAcoes);
    });

   if (exemplar.pendencias.length < 1)
      $j('<p />').html(exemplar.situacao.label || '-').appendTo($colSituacoes);
  }

  $colSituacoes.data('situacao', exemplar.situacao_exemplar);
}
