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

require_once 'Portabilis/Controller/Page/ListController.php';
require_once 'lib/Portabilis/View/Helper/Application.php';

require_once 'Portabilis/Business/Professor.php';

// incluido, pois em include/pmieducar/educar_campo_lista.php acessado tal classe
require_once 'include/pmieducar/clsPermissoes.inc.php';

class DiarioController extends Portabilis_Controller_Page_ListController
{
  protected $_dataMapper = 'Avaliacao_Model_NotaAlunoDataMapper';
  protected $_titulo   = 'Lan&ccedil;amento por turma';
  protected $_processoAp = 644;
  protected $_formMap  = array();

  // TODO migrar para novo padrão campos seleção, usando "$this->inputsHelper()->..."
  protected function inputs() {

    /*
    #variaveis usadas pelo modulo /intranet/include/pmieducar/educar_campo_lista.php
    $this->verificar_campos_obrigatorios = true;
    $this->add_onchange_events           = true;

    $this->campoNumero( "ano", "Ano", date("Y"), 4, 4, true);
    $get_escola = $escola_obrigatorio = $listar_escolas_alocacao_professor      = true;
    $get_curso = $curso_obrigatorio = $listar_somente_cursos_funcao_professor   = true;
    $get_escola_curso_serie = $escola_curso_serie_obrigatorio                   = true;
    $get_turma = $turma_obrigatorio = $listar_turmas_periodo_alocacao_professor = true;

    $get_componente_curricular = $listar_componentes_curriculares_professor     = true;

    $get_etapa = $etapa_obrigatorio                                             = true;
    $get_alunos_matriculados                                                    = true;

    include 'include/pmieducar/educar_campo_lista.php';

    //var_dump(Portabilis_Business_Professor::turmasAlocado(1, 13102, 1180, 6, 14869));
    */

    $this->inputsHelper()->input('ano');

    $inputs = array('instituicao', 'escola', 'curso', 'serie', 'turma', 'etapa', 'matricula', 'componenteCurricular');
    $this->inputsHelper()->dynamic($inputs);
  }


  public function Gerar() {
    $this->inputs();

    // assets
    $this->loadResourceAssets($this->getDispatcher());
  }
}
?>

