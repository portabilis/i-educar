var PAGE_URL_BASE      = 'ordenacaoalunos';
var API_URL_BASE       = 'ordenacaoalunosApi';
var RESOURCE_NAME      = 'ordenacao';
var RESOURCES_NAME     = 'exemplares';
var POST_LABEL         = 'Ordenar';
var DELETE_LABEL       = '';
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
