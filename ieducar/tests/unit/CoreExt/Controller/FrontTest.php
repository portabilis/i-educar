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
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Controller
 * @subpackage  UnitTests
 * @since       Arquivo disponível desde a versão 1.1.0
 * @version     $Id$
 */

require_once 'CoreExt/Controller/Front.php';

/**
 * CoreExt_Controller_FrontTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Controller
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Controller_FrontTest extends UnitBaseTest
{
  protected $_frontController = NULL;
  protected $_path = NULL;

  public function __construct()
  {
    $this->_path = realpath(dirname(__FILE__) . '/_stub');
  }

  protected function setUp()
  {
    $this->_frontController = CoreExt_Controller_Front::getInstance();
    $this->_frontController->resetOptions();
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
  {
    $this->_frontController->setOptions(array('foo' => 'bar'));
  }

  public function testControllerTemObjetosRequestDispatcherEViewPadroes()
  {
    $this->assertType('CoreExt_Controller_Request', $this->_frontController->getRequest());
    $this->assertType('CoreExt_Controller_Dispatcher_Interface', $this->_frontController->getDispatcher());
    $this->assertType('CoreExt_View', $this->_frontController->getView());
  }

  public function testRequestCustomizadoERegistradoEmController()
  {
    require_once 'CoreExt/Controller/Request.php';
    $request = new CoreExt_Controller_Request();
    $this->_frontController->setRequest($request);
    $this->assertSame($request, $this->_frontController->getRequest());
  }

  public function testDespachaARequisicaoParaOPageControllerPorPadrao()
  {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/PageController/index';
    $options = array('basepath' => $this->_path);
    $this->_frontController->setOptions($options);
    $this->_frontController->dispatch();
  }

  public function testDespachaARequisicaoParaOPageControllerMesmoComNomeDoScriptNaUrl()
  {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/index.php/PageController/index';
    $_SERVER['SCRIPT_FILENAME'] = '/var/www/ieducar/module/index.php';
    $options = array('basepath' => $this->_path);
    $this->_frontController->setOptions($options);
    $this->_frontController->dispatch();
  }

  /**
   * @expectedException CoreExt_Controller_Dispatcher_Exception
   */
  public function testDespachaARequisicaoParaFrontControllerLancaExcecao()
  {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/PageController/index';
    $options = array('basepath' => $this->_path, 'controller_type' => CoreExt_Controller_Front::CONTROLLER_FRONT);
    $this->_frontController->setOptions($options);
    $this->_frontController->dispatch();
  }

  public function testDespachaARequisicaoParaOPageControllerEmDiretorioAbaixoDoDocumentRoot()
  {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/module/PageController/index';
    $request = new CoreExt_Controller_Request(array('baseurl' => 'http://www.example.com/module'));
    $options = array('basepath' => $this->_path);
    $this->_frontController->setOptions($options);
    $this->_frontController->setRequest($request);
    $this->_frontController->dispatch();
  }
}