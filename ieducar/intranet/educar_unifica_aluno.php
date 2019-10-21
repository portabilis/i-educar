<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Alan Felipe Farias <alan@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */


use App\Models\LogUnification;
use iEducar\Modules\Unification\StudentLogUnification;
use Illuminate\Support\Facades\DB;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';

require_once 'App/Date/Utils.php';
require_once 'App/Unificacao/Aluno.php';

require_once 'ComponenteCurricular/Model/TurmaDataMapper.php';


class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Unifica&ccedil;&atilde;o de alunos');
    $this->processoAp = "999847";
  }
}


class indice extends clsCadastro
{
  var $pessoa_logada;

  var $tabela_alunos = array();
  var $aluno_duplicado;

  function Inicializar()
  {
    $retorno = 'Novo';

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(999847, $this->pessoa_logada, 7,
      'index.php');

    $this->breadcrumb('Cadastrar unificação', [
        url('intranet/educar_index.php') => 'Escola',
    ]);

    $this->url_cancelar = route('student-log-unification.index');
    $this->nome_url_cancelar = "Cancelar";

    return $retorno;
  }

  function Gerar()
  {

      $this->inputsHelper()->dynamic('ano', array('required' => false, 'max_length' => 4));
      $this->inputsHelper()->dynamic('instituicao',  array('required' =>  false, 'show-select' => true));
      $this->inputsHelper()->dynamic('escola',  array('required' =>  false, 'show-select' => true, 'value' => 0));
      $this->inputsHelper()->simpleSearchAluno(null,array('label' => 'Aluno principal' ));
      $this->campoTabelaInicio("tabela_alunos","",array("Aluno duplicado"),$this->tabela_alunos);
      $this->campoTexto( "aluno_duplicado", "Aluno duplicado", $this->aluno_duplicado, 50, 255, false, true, false, '', '', '', 'onfocus' );
      $this->campoTabelaFim();

  }

  function Novo()
  {
    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(999847, $this->pessoa_logada, 7,
      'index.php');

    $cod_aluno_principal = (int) $this->aluno_id;

    if (!$cod_aluno_principal) return;


    $cod_alunos = array();

    //Monta um array com o código dos alunos selecionados na tabela
    foreach ($this->aluno_duplicado as $key => $value) {
      $explode = explode(" ", $value);

      if($explode[0] == $cod_aluno_principal){
        $this->mensagem = 'Impossivel de unificar alunos iguais.<br />';
        return false;
      }

      $cod_alunos[] = (int) $explode[0];
    }

    if (!count($cod_alunos)) {
       $this->mensagem = 'Informe no mínimo um aluno para unificação.<br />';
        return false;
    }

    DB::beginTransaction();
    $unificationId = $this->createLog($cod_aluno_principal, $cod_alunos, $this->pessoa_logada);
    App_Unificacao_Aluno::unifica($cod_aluno_principal, $cod_alunos, $this->pessoa_logada, new clsBanco(), $unificationId);

    try {
        DB::commit();
    } catch (Throwable $throable) {
        DB::rollBack();
        $this->mensagem = 'Não foi possível realizar a unificação';
        return false;
    }

    $this->mensagem = "<span>Alunos unificados com sucesso.</span>";
    $this->simpleRedirect(route('student-log-unification.index'));
  }

  private function createLog($mainId, $duplicatesId, $createdBy)
  {
    $log = new LogUnification();
    $log->type = StudentLogUnification::getType();
    $log->main_id = $mainId;
    $log->duplicates_id = json_encode($duplicatesId);
    $log->created_by = $createdBy;
    $log->updated_by = $createdBy;
    $log->save();
    return $log->id;
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
    var searchPath = '/module/Api/Aluno?oper=get&resource=aluno-search';
    var params     = { query : request.term };

    $j.get(searchPath, params, function(dataResponse) {
      simpleSearch.handleSearch(dataResponse, response);
    });
  };

  function setAutoComplete() {
    $j.each($j('input[id^="aluno_duplicado"]'), function(index, field) {

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


</script>
