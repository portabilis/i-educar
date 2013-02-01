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

class Portabilis_Controller_ErrorCoreController extends Core_Controller_Page_ViewController
{
  protected $_titulo = 'Error';

  public function __construct() {
    parent::__construct();
    $this->loadAssets();
  }

  /* overwrite Core/Controller/Page/Abstract.php para renderizar html
     sem necessidade de usuário estar logado */
  public function generate(CoreExt_Controller_Page_Interface $instance)
  {
    $this->setHeader();

    $viewBase         = new Core_View($instance);
    $viewBase->titulo = $this->_titulo;
    $viewBase->addForm($instance);

    $html = $viewBase->MakeHeadHtml();

    foreach ($viewBase->clsForm as $form) {
      $html .= $form->Gerar();
    }

    $html .= $form->getAppendedOutput();
    $html .= $viewBase->MakeFootHtml();

    echo $html;
  }

  protected function loadAssets() {
    $styles = array(
      'styles/reset.css',
      'styles/portabilis.css',
      'styles/min-portabilis.css',
      '/modules/Error/Assets/Stylesheets/Error.css'
    );

    Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
  }


  protected function setHeader() {
    die('setHeader must be overwritten!');
  }

  public function Gerar() {
    die('Gerar must be overwritten!');
  }
}
