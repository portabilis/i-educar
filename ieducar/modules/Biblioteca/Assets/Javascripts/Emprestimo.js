var PAGE_URL_BASE      = 'emprestimo';
var API_URL_BASE       = 'emprestimoApi';
var RESOURCE_NAME      = 'exemplar';
var RESOURCES_NAME     = 'exemplares';
var POST_LABEL         = 'Emprestar';
var DELETE_LABEL       = 'Devolver';
var SEARCH_ORIENTATION = '';

var onClickSelectAllEvent = false;
var onClickDeleteEvent    = false;

var onClickActionEvent = function(event){
  var $this = $j(this)
  var $firstChecked = getFirstCheckboxChecked($this);

  if ($firstChecked){
    $j('.disable-on-apply-changes').attr('disabled', 'disabled');
    $this.val('Aguarde emprestando exemplar...');
    postEmprestimo($firstChecked);
  }
};

var postDevolucao = function ($resourceCheckbox) {
  var options = {
    url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'devolucao'),
    dataType : 'json',
    data : {
      instituicao_id : $j('#instituicao_id').val(),
      escola_id : $j('#escola_id').val(),
      biblioteca_id : $j('#biblioteca_id').val(),
      cliente_id : $j('#cliente_id').val(),
      exemplar_id : $resourceCheckbox.data('exemplar_id'),
      tombo_exemplar : $j('#tombo_exemplar').val()
    },

    success : function(dataResponse){
      afterChangeResource($resourceCheckbox);
      handlePost(dataResponse);
    }
  };

  beforeChangeResource($resourceCheckbox);
  postResource(options);
};

var postEmprestimo = function ($resourceCheckbox) {
  var options = {
    url : postResourceUrlBuilder.buildUrl(API_URL_BASE, 'emprestimo'),
    dataType : 'json',
    data : {
      instituicao_id : $j('#instituicao_id').val(),
      escola_id : $j('#escola_id').val(),
      biblioteca_id : $j('#biblioteca_id').val(),
      cliente_id : $j('#cliente_id').val(),
      exemplar_id : $resourceCheckbox.data('exemplar_id'),
      tombo_exemplar : $j('#tombo_exemplar').val()
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
  if (dataResponse.exemplar)
    var $targetElement = $j('#exemplar-' + dataResponse.exemplar.id).closest('tr').first();
  else
    var $targetElement = undefined;

  handleMessages(dataResponse.msgs, $targetElement);
  updateResourceRow(dataResponse.exemplar);
};


function setTableSearchDetails($tableSearchDetails, dataDetails){
  $j('<caption />').html('<strong>Empréstimo de exemplares</strong>').appendTo($tableSearchDetails);

  //set headers table
  var $linha = $j('<tr />');
  $j('<th />').html('Cliente').appendTo($linha);
  $j('<th />').html('Obra').appendTo($linha);
  $j('<th />').html('Tombo exemplar').appendTo($linha);
  $j('<th />').html('Biblioteca').appendTo($linha);

  $linha.appendTo($tableSearchDetails);

  var $linha = $j('<tr />').addClass('even');

  $j('<td />').html($j('#nome_cliente').val()).appendTo($linha);
  $j('<td />').html('').attr('id', 'titulo_obra').appendTo($linha);
  $j('<td />').html($j('#tombo_exemplar').val()).appendTo($linha);

  var $htmlBibliotecaField = $j('#biblioteca_id').children("[selected='selected']").html() ||
                             $j('#tr_biblioteca_nome span:last').html();

  $j('<td />').html(safeToUpperCase($htmlBibliotecaField)).appendTo($linha);

  $linha.appendTo($tableSearchDetails);
  $tableSearchDetails.show();

  $tableSearchDetails.data('details', dataDetails);
}


function handleSearch($resultTable, dataResponse) {
  var $linha = $j('<tr />');
  $j('<th />').html('Selecionar').addClass('center').appendTo($linha);
  $j('<th />').html('Id').addClass('center').appendTo($linha);
  $j('<th />').html('Situação').addClass('center').appendTo($linha);
  $j('<th />').html('Cliente').appendTo($linha);
  $j('<th />').html('Data empréstimo').addClass('center').appendTo($linha);
  $j('<th />').html('Data prevista devolução').addClass('center').appendTo($linha);
  //$j('<th />').html('A&#231;&#227;o').addClass('center').appendTo($linha);
  $linha.appendTo($resultTable);

  var setTituloObra = true;

  //set rows
  $j.each(dataResponse[RESOURCES_NAME], function(index, value){

    if(setTituloObra)
      $j('#titulo_obra').html(value.acervo.titulo);

    var $checkbox = $j('<input />')
                    .attr('type', 'checkbox')
                    .attr('name', 'exemplar')
                    .attr('value', 'sim')
                    .attr('id', 'exemplar-' + value.id)
                    .attr('class', 'exemplar disable-on-apply-changes')
                    .data('exemplar_id', value.id);

    var situacoesEmprestimoPermitida = ['disponivel', 'emprestado', 'reservado', 'emprestado_e_reservado'];

    if ($j.inArray(value.situacao.flag, situacoesEmprestimoPermitida) < 0)
      $checkbox.attr('disabled', 'disabled').removeClass('disable-on-apply-changes');

    var $linha = $j('<tr />');
    $j('<td />').html($checkbox).addClass('center').appendTo($linha);
    $j('<td />').html(value.id).addClass('center').appendTo($linha);

    var $colSituacoes               = $j('<td />').attr('id', 'situacoes-' + value.id).addClass('situacoes center');
    var $colClientes                = $j('<td />').attr('id', 'clientes-' + value.id);
    var $colDatas                   = $j('<td />').attr('id', 'datas-' + value.id).addClass('center');
    var $colDatasPrevistaDisponivel = $j('<td />').attr('id', 'datas-prevista-disponivel-' + value.id).addClass('center');
    //var $colAcoes                   = $j('<td />').attr('id', 'acoes-' + value.id).addClass('center');

    $colSituacoes.appendTo($linha);
    $colClientes.appendTo($linha);
    $colDatas.appendTo($linha);
    $colDatasPrevistaDisponivel.appendTo($linha);
    //$colAcoes.appendTo($linha);

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

      if (value.situacao.flag == 'emprestimodo' && value.cliente && value.cliente.id == $j('#cliente_id').val()) {
        var $linkToDelete = $j("<a href='#' class='disable-on-apply-changes'>Cancelar emprestimo</a>").click(onClickCancelEvent).data('exemplar_id', exemplar.id).data('emprestimo_id', value.emprestimo_id);
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

function canSearch() {
  if ($j('#nome_cliente').val() == '') {
    alert('Selecione um cliente para continuar.');
    return false;
  }

  return true;
}