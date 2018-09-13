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

/**
 * DiarioController class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */

class DiarioController extends Portabilis_Controller_Page_ListController
{
  protected $_titulo     = 'Lan&ccedil;amento por turma';
  protected $_processoAp = 642;

  public function Gerar() {

    $userId        = Portabilis_Utils_User::currentUserId();
    $componenteRequired = $isProfessor   = Portabilis_Business_Professor::isProfessor(false, $userId);

    $this->inputsHelper()->input('ano', 'ano');
    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'curso', 'serie', 'turma', 'etapa'));
    $this->inputsHelper()->dynamic(array('componenteCurricularForDiario'), array('required' => $componenteRequired));
    $this->inputsHelper()->dynamic(array('matricula'), array('required' => FALSE ));

    $navegacaoTab = array('1' => 'Horizontal(padr&atilde;o)',
                          '2' => 'Vertical',);

    $options      = array('label'     =>'Navega&ccedil;&atilde;o do cursor(tab)',
                          'resources' => $navegacaoTab,
                          'required'  => false,
                          'inline'    => true,
                          'value'     => $navegacaoTab[1]);

    $this->inputsHelper()->select('navegacao_tab', $options);

    $this->inputsHelper()->hidden('mostrar_botao_replicar_todos', array('value' => $teste = $GLOBALS['coreExt']['Config']->app->faltas_notas->mostrar_botao_replicar));

    $this->loadResourceAssets($this->getDispatcher());
  }

  protected function _preRender(){

    parent::_preRender();

    Portabilis_View_Helper_Application::loadStylesheet($this, 'intranet/styles/localizacaoSistema.css');

    $localizacao = new LocalizacaoSistema();

    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""                                  => "Lan&ccedil;amento de notas"
    ));
    $this->enviaLocalizacao($localizacao->montar(), true);
  }
}
?>

