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

require_once 'CoreExt/View/Helper/UrlHelper.php';
require_once 'CoreExt/View/Helper/TableHelper.php';
require_once 'Core/Controller/Page/ListController.php';
require_once 'App/Model/IedFinder.php';

require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class PromocaoController extends Core_Controller_Page_ListController
{
  protected $_dataMapper = 'Avaliacao_Model_NotaAlunoDataMapper';
  protected $_titulo   = 'Lan&ccedil;amento por turma';
  protected $_processoAp = 644;
  protected $_formMap  = array();

  protected function setSelectionFields()
  {

    #variaveis usadas pelo modulo /intranet/include/pmieducar/educar_campo_lista.php
    $this->verificar_campos_obrigatorios = True;
    $this->add_onchange_events = True;

    $instituicao_obrigatorio = true;
    $this->campoNumero('ano_escolar', 'ano_escolar', date('Y'), 4, 4, true, '');

    #$get_escola  = $listar_escolas_alocacao_professor = TRUE;
    #$get_curso = $listar_somente_cursos_funcao_professor = TRUE;
    #$get_escola_curso_serie = TRUE;
    #$get_turma = $listar_turmas_periodo_alocacao_professor = TRUE;
    #$get_alunos_matriculados = true;

    include 'include/pmieducar/educar_campo_lista.php';
  }

  
  public function Gerar()
  {

    $this->setSelectionFields();

    $this->rodape = "";

    $this->largura = '100%';

    $this->appendOutput('<script type="text/javascript" src="scripts/jquery/jquery.js"></script>');
    $this->appendOutput('<script type="text/javascript" src="scripts/jquery/jquery.form.js"></script>');

    $this->appendOutput('<link type="text/css" rel="stylesheet" href="/modules/Avaliacao/Static/styles/promocaoController.css"></script>');

    $this->appendOutput('<script type="text/javascript" charset="utf-8" src="/modules/Avaliacao/Static/scripts/promocaoController.js?timestamp='.date('dmY').'"></script>');
  }
}
?>

