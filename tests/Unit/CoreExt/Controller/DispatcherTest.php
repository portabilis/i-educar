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

require_once __DIR__.'/_stub/Dispatcher.php';

/**
 * CoreExt_Controller_DispatcherTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Controller
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Controller_DispatcherTest extends PHPUnit\Framework\TestCase
{
  protected $_dispatcher = NULL;

  protected $_uris = array(
    0 => array('uri' => 'http://www.example.com/'),
    1 => array('uri' => 'http://www.example.com/index.php'),
    2 => array('uri' => 'http://www.example.com/controller/action'),
    3 => array('uri' => 'http://www.example.com/index.php/controller/action'),
    4 => array(
      'uri' => 'http://www.example.com/module/controller/action',
      'baseurl' => 'http://www.example.com/module'
    ),
    5 => array(
      'uri' => 'http://www.example.com/module/index.php/controller/action',
      'baseurl' => 'http://www.example.com/module'
    ),
    6 => array(
      'uri' => 'http://www.example.com/module/controller',
      'baseurl' => 'http://www.example.com/module'
    )
  );

  /**
   * Configura SCRIPT_FILENAME como forma de assegurar que o nome do script
   * será desconsiderado na definição do controller e da action.
   */
  protected function setUp(): void
  {
    $_SERVER['REQUEST_URI'] = $this->_uris[0]['uri'];
    $_SERVER['SCRIPT_FILENAME'] = '/var/www/ieducar/index.php';
    $this->_dispatcher = new CoreExt_Controller_Dispatcher_AbstractStub();
  }

  protected function _setRequestUri($index = 0)
  {
    $_SERVER['REQUEST_URI'] = array_key_exists($index, $this->_uris) ?
      $this->_uris[$index]['uri'] : $this->_uris[$index = 0]['uri'];

    // Configura a baseurl
    if (isset($this->_uris[$index]['baseurl'])) {
      $this->_dispatcher->getRequest()->setOptions(array('baseurl' => $this->_uris[$index]['baseurl']));
    }
  }

  protected function _getRequestUri($index = 0)
  {
    return array_key_exists($index, $this->_uris) ?
      $this->_uris[$index]['uri'] : $this->_uris[0]['uri'];
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
  {
    $this->_dispatcher->setOptions(array('foo' => 'bar'));
  }

  public function testDispatcherEstabeleceControllerDefault()
  {
    $this->assertEquals('index', $this->_dispatcher->getControllerName(), $this->_getRequestUri(0));
    $this->_setRequestUri(1);
    $this->assertEquals('index', $this->_dispatcher->getControllerName(), $this->_getRequestUri(1));
  }

  public function testDispatcherEstabeleceControllerDefaultConfigurado()
  {
    $this->_dispatcher->setOptions(array('controller_default_name' => 'controller'));
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(1));
  }

  public function testDispatcherEstabeleceActionDefault()
  {
    $this->assertEquals('index', $this->_dispatcher->getActionName(), $this->_getRequestUri(0));
    $this->_setRequestUri(1);
    $this->assertEquals('index', $this->_dispatcher->getActionName(), $this->_getRequestUri(1));
  }

  public function testDispatcherEstabeleceActionDefaultConfigurada()
  {
    $this->_dispatcher->setOptions(array('action_default_name' => 'action'));
    $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(1));
  }

  public function testDispatcherEstabeleceController()
  {
    $this->_setRequestUri(2);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(2));
    $this->_setRequestUri(3);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(3));
    $this->_setRequestUri(4);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(4));
    $this->_setRequestUri(5);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(5));
    $this->_setRequestUri(6);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(6));
  }

  public function testDispatcherEstabeleceAction()
  {
    $this->_setRequestUri(2);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(2));
    $this->_setRequestUri(3);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(3));
    $this->_setRequestUri(4);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(4));
    $this->_setRequestUri(5);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(5));
    $this->_setRequestUri(6);
    $this->assertEquals('index', $this->_dispatcher->getActionName(), $this->_getRequestUri(6));
  }
}
