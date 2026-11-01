<?php

beforeEach(function () {
    $this->_controller = new CoreExt_Controller_AbstractStub;
});

test('controller instancia dispatcher standard por padrao', function () {
    $this->assertInstanceOf('CoreExt_Controller_Dispatcher_Standard', $this->_controller->getDispatcher());
});

test('instancia tem core ext session por padrao', function () {
    $this->assertInstanceOf('CoreExt_Session', $this->_controller->getSession());
});
