<?php

class CoreExt_Controller_Dispatcher_Strategy_FrontStrategyTest extends PHPUnit\Framework\TestCase
{
    protected $_frontController = null;
    protected $_pageStrategy = null;

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
        $this->_frontController->setOptions(['basepath' => $this->_path, 'controller_type' => CoreExt_Controller_Front::CONTROLLER_FRONT]);
        $this->_pageStrategy = new CoreExt_Controller_Dispatcher_Strategy_FrontStrategy($this->_frontController);
    }

    public function testRequisicaoAControllerNaoExistenteLancaExcecao()
    {
        $this->expectException(\CoreExt_Controller_Dispatcher_Exception::class);
        $_SERVER['REQUEST_URI'] = 'http://www.example.com/PageController/view';
        $this->_pageStrategy->dispatch();
    }

    public function testControllerConfiguradoCorretamente()
    {
        $this->assertSame($this->_frontController, $this->_pageStrategy->getController());
    }
}
