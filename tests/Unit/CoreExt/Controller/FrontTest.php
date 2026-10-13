<?php

beforeEach(function () {
    $this->_path = realpath(dirname(__FILE__) . '/_stub');
    $this->_frontController = CoreExt_Controller_Front::getInstance();
    $this->_frontController->resetOptions();
});

test('opcao de configuracao nao existente lanca excecao', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->_frontController->setOptions(['foo' => 'bar']);
});

test('controller tem objetos request dispatcher e view padroes', function () {
    $this->assertInstanceOf('CoreExt_Controller_Request', $this->_frontController->getRequest());
    $this->assertInstanceOf('CoreExt_Controller_Dispatcher_Interface', $this->_frontController->getDispatcher());
    $this->assertInstanceOf('CoreExt_View', $this->_frontController->getView());
});

test('request customizado e registrado em controller', function () {
    $request = new CoreExt_Controller_Request;
    $this->_frontController->setRequest($request);
    $this->assertSame($request, $this->_frontController->getRequest());
});
