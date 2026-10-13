<?php

beforeEach(function () {
    $this->_path = realpath(dirname(__FILE__) . '/../../_stub');
    $this->requestUri = $_SERVER['REQUEST_URI'] ?? null;
    $this->_frontController = CoreExt_Controller_Front::getInstance();
    $this->_frontController->setOptions(['basepath' => $this->_path, 'controller_type' => CoreExt_Controller_Front::CONTROLLER_FRONT]);
    $this->_pageStrategy = new CoreExt_Controller_Dispatcher_Strategy_FrontStrategy($this->_frontController);
});

test('requisicao a controller nao existente lanca excecao', function () {
    $this->expectException(CoreExt_Controller_Dispatcher_Exception::class);
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/PageController/view';
    $this->_pageStrategy->dispatch();
});

test('controller configurado corretamente', function () {
    $this->assertSame($this->_frontController, $this->_pageStrategy->getController());
});
