<?php

use App\Models\Individual;
use App\Models\LogUnification;
use iEducar\Modules\Unification\PersonLogUnification;
use Illuminate\Support\Facades\DB;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';

require_once 'lib/App/Unificacao/Pessoa.php';
require_once 'lib/CoreExt/Exception.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Unificação de pessoas');
    $this->processoAp = "9998878";
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $tabela_pessoas = array();
  var $pessoa_duplicada;

  function Formular()
  {
    $this->breadcrumb('Unificação de pessoas', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
  }

  function Inicializar()
  {
    $retorno = 'Novo';

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(9998878, $this->pessoa_logada, 7,
      'index.php');

    return $retorno;
  }

  function Gerar()
  {
      $this->acao_enviar = 'showConfirmationMessage()';
      $this->inputsHelper()->dynamic('ano', array('required' => false, 'max_length' => 4));
      $this->inputsHelper()->simpleSearchPessoa(null,array('label' => 'Pessoa principal' ));
      $this->campoTabelaInicio("tabela_pessoas","",array("Pessoa duplicada"),$this->tabela_pessoas);
      $this->campoTexto( "pessoa_duplicada", "Pessoa duplicada", $this->pessoa_duplicada, 50, 255, false, true, false, '', '', '', 'onfocus' );
      $this->campoTabelaFim();
  }

  function Novo()
  {
    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(9998878, $this->pessoa_logada, 7,
      'index.php');

    $codPessoaPrincipal = (int) $this->pessoa_id;

    if (!$codPessoaPrincipal) return;


    $codPessoas = array();

    //Monta um array com o código dos pessoas selecionados na tabela
    foreach ($this->pessoa_duplicada as $key => $value) {
      $explode = explode(" ", $value);

      if($explode[0] == $codPessoaPrincipal){
        $this->mensagem = 'Impossivel de unificar pessoas iguais.<br />';
        return false;
      }

      $codPessoas[] = (int) $explode[0];
    }

    if (!count($codPessoas)) {
       $this->mensagem = 'Informe no mínimo um pessoa para unificação.<br />';
        return false;
    }

    $unificationId = $this->createLog($codPessoaPrincipal, $codPessoas, $this->pessoa_logada);
    $unificador = new App_Unificacao_Pessoa($codPessoaPrincipal, $codPessoas, $this->pessoa_logada, new clsBanco(), $unificationId);

    try {
        $unificador->unifica();
    } catch (CoreExt_Exception $exception) {
        $this->mensagem = $exception->getMessage();
        return FALSE;
    }

    $this->mensagem = "<span>Pessoas unificadas com sucesso.</span>";
    return true;
  }

  private function createLog($mainId, $duplicatesId, $createdBy)
  {
    $log = new LogUnification();
    $log->type = PersonLogUnification::getType();
    $log->main_id = $mainId;
    $log->duplicates_id = json_encode($duplicatesId);
    $log->created_by = $createdBy;
    $log->updated_by = $createdBy;
    $log->duplicates_name = json_encode($this->getNamesOfUnifiedPeople($duplicatesId));
    $log->save();
    return $log->id;
  }

  /**
   * Retorna os nomes das pessoas unificadas
   *
   * @param integer[] $duplicatesId
   * @return string[]
  */
  private function getNamesOfUnifiedPeople($duplicatesId)
  {
      $names = [];

      foreach ($duplicatesId as $personId){
          $names[] = Individual::findOrFail($personId)->real_name;
      }

      return $names;
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">

  var handleSelect = function(event, ui){
    $j(event.target).val(ui.item.label);
    return false;
  };

  var search = function(request, response) {
    var searchPath = '/module/Api/Pessoa?oper=get&resource=pessoa-search';
    var params     = { query : request.term };

    $j.get(searchPath, params, function(dataResponse) {
      simpleSearch.handleSearch(dataResponse, response);
    });
  };

  function setAutoComplete() {
    $j.each($j('input[id^="pessoa_duplicada"]'), function(index, field) {

      $j(field).autocomplete({
        source    : search,
        select    : handleSelect,
        minLength : 1,
        autoFocus : true
      });

    });
  }

  setAutoComplete();

  // bind events

  var $addPontosButton = $j('#btn_add_tab_add_1');

  $addPontosButton.click(function(){
    setAutoComplete();
  });

$j('#btn_enviar').val('Unificar');

  function showConfirmationMessage() {
      makeDialog({
          content: 'O processo de unificação de pessoas não poderá ser desfeito. Deseja continuar?',
          title: 'Atenção!',
          maxWidth: 860,
          width: 860,
          close: function () {
              $j('#dialog-container').dialog('destroy');
          },
          buttons: [{
              text: 'Confirmar',
              click: function () {
                  acao();
                  $j('#dialog-container').dialog('destroy');
              }
          }, {
              text: 'Cancelar',
              click: function () {
                  $j('#dialog-container').dialog('destroy');
              }
          }]
      });
  }

  function makeDialog(params) {
      var container = $j('#dialog-container');

      if (container.length < 1) {
          $j('body').append('<div id="dialog-container" style="width: 500px;"></div>');
          container = $j('#dialog-container');
      }

      if (container.hasClass('ui-dialog-content')) {
          container.dialog('destroy');
      }

      container.empty();
      container.html(params.content);

      delete params['content'];

      container.dialog(params);
  }
</script>
