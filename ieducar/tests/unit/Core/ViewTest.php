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
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_View
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'Core/_stub/View.php';
require_once 'Core/Controller/_stub/Page/Abstract.php';

/**
 * Core_ViewTest class.
 *
 * @author    Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Core_View
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Core_ViewTest extends UnitBaseTest
{
  protected $_pageController = NULL;
  protected $_view = NULL;

  public function __construct()
  {
    $this->_pageController = new Core_Controller_Page_AbstractStub();
    $this->_pageController->setOptions(array('processoAp' => 1, 'titulo' => 'foo'));
  }

  protected function setUp()
  {
    $this->_view = new Core_ViewStub($this->_pageController);
  }

  public function testTituloConfiguradoComValorDeConfiguracaoGlobal()
  {
    global $coreExt;
    $instituicao = $coreExt['Config']->app->template->vars->instituicao;

    $this->_view->MakeAll();
    $this->assertEquals($instituicao . ' | foo', $this->_view->getTitulo());
  }

  public function testProcessoApConfiguradoPeloValorDePageController()
  {
    $this->_view->MakeAll();
    $this->assertEquals(1, $this->_view->getProcessoAp());
  }
}