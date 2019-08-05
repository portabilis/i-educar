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

require_once 'CoreExt/Controller/Request.php';

/**
 * CoreExt_Controller_RequestTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Controller
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Controller_RequestTest extends PHPUnit\Framework\TestCase
{
  protected $_request = NULL;

  protected function setUp(): void
  {
    $this->_request = new CoreExt_Controller_Request();
  }

  /**
   * @expectedException InvalidArgumentException
   */
  public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
  {
    $this->_request->setOptions(array('foo' => 'bar'));
  }

  public function testRetornaNullCasoNaoEstejaSetadoNasSuperglobaisGetPostCookieEServer()
  {
    $this->assertNull($this->_request->get('foo'));
  }

  public function testVariavelEstaSetada()
  {
    $_GET['name'] = 'Foo';
    $this->assertTrue(isset($this->_request->name));
    unset($_GET['name']);
    $this->assertFalse(isset($this->_request->name));
  }

  public function testRecuperaParametroDeRequisicaoGet()
  {
    $_GET['name'] = 'Foo';
    $this->assertEquals($_GET['name'], $this->_request->get('name'));
  }

  public function testRecuperaParametroDeRequisicaoPost()
  {
    $_POST['name'] = 'Foo';
    $this->assertEquals($_POST['name'], $this->_request->get('name'));
  }

  public function testRecuperaParametroDoCookie()
  {
    $_COOKIE['name'] = 'Foo';
    $this->assertEquals($_COOKIE['name'], $this->_request->get('name'));
  }

  public function testRecuperaParametroDoServer() {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/controller';
    $this->assertEquals($_SERVER['REQUEST_URI'], $this->_request->get('REQUEST_URI'));
  }

  public function testConfiguraBaseurlComSchemeEHostPorPadrao() {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/controller';
    $this->assertEquals('http://www.example.com', $this->_request->getBaseurl());
  }
}
