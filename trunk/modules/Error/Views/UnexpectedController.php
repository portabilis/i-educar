<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
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
 * @package   Portabilis
 * @subpackage  lib
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Core/View.php';
require_once 'Core/Controller/Page/ViewController.php';
require_once 'lib/Portabilis/View/Helper/Application.php';

class UnexpectedController extends Core_Controller_Page_ViewController
{
  protected $_dataMapper  = ''; #Avaliacao_Model_NotaComponenteDataMapper';
  protected $_processoAp  = 0;
  #protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';


  public function __construct() {
    parent::__construct();
    $this->loadAssets();
  }

  /* overwrite Core/Controller/Page/Abstract.php para renderizar html
     sem necessidade de usuário estar logado */
  public function generate(CoreExt_Controller_Page_Interface $instance)
  {
    header("HTTP/1.1 500 Internal Server Error");

    $viewBase         = new Core_View($instance);
    $viewBase->titulo = 'i-Educar - Erro inesperado';
    $viewBase->addForm($instance);

    $html = $viewBase->MakeHeadHtml();

    foreach ($viewBase->clsForm as $form) {
      $html .= $form->Gerar();
    }

    $html .= $form->getAppendedOutput();
    $html .= $viewBase->MakeFootHtml();

    echo $html;
  }

  public function Gerar() {
    $linkToSupport = $GLOBALS['coreExt']['Config']->modules->error->link_to_support;

    echo "
      <div id='error'>
        <div class='content'>
         <h1>Erro inesperado</h1>

         <p class='explanation'>Desculpe-nos, algum erro inesperado ocorreu, <strong>por favor tente novamente mais tarde.</strong>
          <ul>
            <li><a href='/intranet/index.php'>Voltar para o sistema</a></li>
            <li>Caso o erro persista, por favor, <a target='_blank' href='$linkToSupport'>solicite suporte</a>.</li>
          </ul>
        </p>
        </div>
      </div>";
  }

  protected function loadAssets() {

    $styles = array(
      '/modules/Error/Assets/Stylesheets/Error.css',
      'styles/reset.css',
      'styles/portabilis.css',
      'styles/min-portabilis.css'
    );

    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
  }
}
