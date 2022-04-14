<?php

class CoreExt_Controller_AbstractTest extends PHPUnit\Framework\TestCase
{
    protected $_controller = null;

    protected function setUp(): void
    {
        $this->_controller = new CoreExt_Controller_AbstractStub();
    }

    public function testControllerInstanciaDispatcherStandardPorPadrao()
    {
        $this->assertInstanceOf('CoreExt_Controller_Dispatcher_Standard', $this->_controller->getDispatcher());
    }

    public function testInstanciaTemCoreExtSessionPorPadrao()
    {
        $this->assertInstanceOf('CoreExt_Session', $this->_controller->getSession());
    }
}
