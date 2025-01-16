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

    public function test_opcao_de_configuracao_nao_existente_lanca_excecao()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->_frontController->setOptions(['foo' => 'bar']);
    }

    public function test_controller_tem_objetos_request_dispatcher_e_view_padroes()
    {
        $this->assertInstanceOf('CoreExt_Controller_Request', $this->_frontController->getRequest());
        $this->assertInstanceOf('CoreExt_Controller_Dispatcher_Interface', $this->_frontController->getDispatcher());
        $this->assertInstanceOf('CoreExt_View', $this->_frontController->getView());
    }

    public function test_request_customizado_e_registrado_em_controller()
    {
        $request = new CoreExt_Controller_Request;
        $this->_frontController->setRequest($request);
        $this->assertSame($request, $this->_frontController->getRequest());
    }
}
