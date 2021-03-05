<?php


/**
 * CoreExt_Controller_Strategy_PageStrategyTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     CoreExt_Controller
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class CoreExt_Controller_Dispatcher_Strategy_PageStrategyTest extends PHPUnit\Framework\TestCase
{
    protected $_frontController = null;
    protected $_pageStrategy = null;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_path = realpath(dirname(__FILE__) . '/../../_stub');
    }

    protected function setUp(): void
    {
        $this->_frontController = CoreExt_Controller_Front::getInstance();
        $this->_frontController->setOptions(['basepath' => $this->_path]);
        $this->_pageStrategy = new CoreExt_Controller_Dispatcher_Strategy_PageStrategy($this->_frontController);
    }

    public function testRequisicaoAControllerNaoExistenteLancaExcecao()
    {
        $this->expectException(\CoreExt_Exception_FileNotFoundException::class);
        $_SERVER['REQUEST_URI'] = 'http://www.example.com/PageController/view';
        $this->_pageStrategy->dispatch();
    }

    public function testControllerConfiguradoCorretamente()
    {
        $this->assertSame($this->_frontController, $this->_pageStrategy->getController());
    }
}
