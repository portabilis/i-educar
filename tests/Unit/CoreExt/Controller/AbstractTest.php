<?php

class CoreExt_Controller_AbstractTest extends PHPUnit\Framework\TestCase
{
    protected $_controller = null;

    protected function setUp(): void
    {
        $this->_controller = new CoreExt_Controller_AbstractStub;
    }

    public function test_controller_instancia_dispatcher_standard_por_padrao()
    {
        $this->assertInstanceOf('CoreExt_Controller_Dispatcher_Standard', $this->_controller->getDispatcher());
    }

    public function test_instancia_tem_core_ext_session_por_padrao()
    {
        $this->assertInstanceOf('CoreExt_Session', $this->_controller->getSession());
    }
}
