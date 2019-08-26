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
require_once 'CoreExt/Controller/Dispatcher/Strategy/FrontStrategy.php';

/**
 * CoreExt_Controller_Dispatcher_Strategy_FrontStrategyTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     CoreExt_Controller
 * @subpackage  UnitTests
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class CoreExt_Controller_Dispatcher_Strategy_FrontStrategyTest extends PHPUnit\Framework\TestCase
{
  protected $_frontController = NULL;
  protected $_pageStrategy = NULL;

  /**
   * @var string
   */
  private $requestUri;

  public function __construct($name = null, array $data = [], $dataName = '')
  {
      parent::__construct($name, $data, $dataName);
      $this->_path = realpath(dirname(__FILE__) . '/../../_stub');
      $this->requestUri = $_SERVER['REQUEST_URI'] ?? null;
  }

  protected function setUp(): void
  {
    $this->_frontController = CoreExt_Controller_Front::getInstance();
    $this->_frontController->setOptions(array('basepath' => $this->_path, 'controller_type' => CoreExt_Controller_Front::CONTROLLER_FRONT));
    $this->_pageStrategy = new CoreExt_Controller_Dispatcher_Strategy_FrontStrategy($this->_frontController);
  }

  /**
   * @expectedException CoreExt_Controller_Dispatcher_Exception
   */
  public function testRequisicaoAControllerNaoExistenteLancaExcecao()
  {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/PageController/view';
    $this->_pageStrategy->dispatch();
  }

  public function testControllerConfiguradoCorretamente()
  {
    $this->assertSame($this->_frontController, $this->_pageStrategy->getController());
  }
}
