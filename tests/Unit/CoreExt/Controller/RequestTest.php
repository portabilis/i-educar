<?php

beforeEach(function () {
    $this->_request = new CoreExt_Controller_Request;
});

test('opcao de configuracao nao existente lanca excecao', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->_request->setOptions(['foo' => 'bar']);
});

test('retorna null caso nao esteja setado nas superglobais get post cookie e server', function () {
    $this->assertNull($this->_request->get('foo'));
});

test('variavel esta setada', function () {
    $_GET['name'] = 'Foo';
    $this->assertTrue(isset($this->_request->name));
    unset($_GET['name']);
    $this->assertFalse(isset($this->_request->name));
});

test('recupera parametro de requisicao get', function () {
    $_GET['name'] = 'Foo';
    $this->assertEquals($_GET['name'], $this->_request->get('name'));
});

test('recupera parametro de requisicao post', function () {
    $_POST['name'] = 'Foo';
    $this->assertEquals($_POST['name'], $this->_request->get('name'));
});

test('recupera parametro do cookie', function () {
    $_COOKIE['name'] = 'Foo';
    $this->assertEquals($_COOKIE['name'], $this->_request->get('name'));
});

test('recupera parametro do server', function () {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/controller';
    $this->assertEquals($_SERVER['REQUEST_URI'], $this->_request->get('REQUEST_URI'));
});

test('configura baseurl com scheme e host por padrao', function () {
    $_SERVER['REQUEST_URI'] = 'http://www.example.com/controller';
    $this->assertEquals('http://www.example.com', $this->_request->getBaseurl());
});
