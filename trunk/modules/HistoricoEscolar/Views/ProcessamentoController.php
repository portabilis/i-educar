<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Avaliacao
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

#TODO remover includes desnecessarios
require_once 'CoreExt/View/Helper/UrlHelper.php';
require_once 'CoreExt/View/Helper/TableHelper.php';
require_once 'Core/Controller/Page/ListController.php';
require_once 'App/Model/IedFinder.php';

require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class ProcessamentoController extends Core_Controller_Page_ListController
{
  protected $_dataMapper = 'Avaliacao_Model_NotaAlunoDataMapper';
  protected $_titulo   = 'Processamento histórico';
  protected $_processoAp = 644;
  protected $_formMap  = array();

  protected function setVars()
  {

    $this->ref_cod_aluno = $_GET['aluno_id'];
    $this->ref_cod_instituicao = $_GET['instituicao_id'];
    $this->ref_cod_escola = $_GET['escola_id'];
    $this->ref_cod_curso = $_GET['curso_id'];
    $this->ref_cod_turma = $_GET['turma_id'];
    $this->ref_ref_cod_serie = $this->ref_cod_serie = $_GET['serie_id'];
    $this->ano = $_GET['ano'];

    if ($this->ref_cod_aluno)
    {
      $nome_aluno_filtro = new clsPmieducarAluno();
      $nome_aluno_filtro = $nome_aluno_filtro->lista($int_cod_aluno = $this->ref_cod_aluno);
      $this->nm_aluno = $nome_aluno_filtro[0]['nome_aluno'];
    }
  }


  protected function setSelectionFields()
  {

    #variaveis usadas pelo modulo /intranet/include/pmieducar/educar_campo_lista.php
    $this->verificar_campos_obrigatorios = true;
    $this->add_onchange_events = true;

    $this->campoNumero( "ano", "Ano", date("Y"), 4, 4, true);
    $instituicao_obrigatorio = true;
    $get_escola = $escola_obrigatorio = true;
    $get_curso = true;
    $get_escola_curso_serie = true;
    $get_turma = true;
    $get_alunos_matriculados = true;
    include 'include/pmieducar/educar_campo_lista.php';
  }

  
  public function Gerar()
  {

    $this->setVars();
    $this->setSelectionFields();

    $this->rodape = "";

    $this->largura = '100%';

    $this->appendOutput('<script type="text/javascript" src="scripts/jquery/jquery.js"></script>');
    $this->appendOutput('<script type="text/javascript" src="scripts/jquery/jquery.form.js"></script>');

    $this->appendOutput('<link type="text/css" rel="stylesheet" href="/modules/HistoricoEscolar/Static/styles/processamentoController.css"></script>');

    $this->appendOutput('<script type="text/javascript" charset="utf-8" src="/modules/HistoricoEscolar/Static/scripts/processamentoController.js?timestamp='.date('dmY').'"></script>');
  }
}
?>

