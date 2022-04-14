<?php

class CoreExt_Controller_FrontTest extends PHPUnit\Framework\TestCase
{
    protected $_frontController = null;
    protected $_path = null;

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

    public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->_frontController->setOptions(['foo' => 'bar']);
    }

    public function testControllerTemObjetosRequestDispatcherEViewPadroes()
    {
        $this->assertInstanceOf('CoreExt_Controller_Request', $this->_frontController->getRequest());
        $this->assertInstanceOf('CoreExt_Controller_Dispatcher_Interface', $this->_frontController->getDispatcher());
        $this->assertInstanceOf('CoreExt_View', $this->_frontController->getView());
    }

    public function testRequestCustomizadoERegistradoEmController()
    {
        $request = new CoreExt_Controller_Request();
        $this->_frontController->setRequest($request);
        $this->assertSame($request, $this->_frontController->getRequest());
    }
}
