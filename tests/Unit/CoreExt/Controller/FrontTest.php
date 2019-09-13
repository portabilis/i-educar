<?php

use PHPUnit\Framework\TestCase;

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
class CoreExt_Controller_FrontTest extends PHPUnit\Framework\TestCase
{
  protected $_frontController = NULL;
  protected $_path = NULL;

  public function __construct($name = null, array $data = [], $dataName = '')
  {
      parent::__construct($name, $data, $dataName);
    $this->_path = realpath(dirname(__FILE__) . '/_stub');
  }

  protected function setUp(): void
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
    $this->assertInstanceOf('CoreExt_Controller_Request', $this->_frontController->getRequest());
    $this->assertInstanceOf('CoreExt_Controller_Dispatcher_Interface', $this->_frontController->getDispatcher());
    $this->assertInstanceOf('CoreExt_View', $this->_frontController->getView());
  }

  public function testRequestCustomizadoERegistradoEmController()
  {
    require_once 'CoreExt/Controller/Request.php';
    $request = new CoreExt_Controller_Request();
    $this->_frontController->setRequest($request);
    $this->assertSame($request, $this->_frontController->getRequest());
  }
}
