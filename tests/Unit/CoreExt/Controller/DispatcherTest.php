<?php

/**
 * Configura SCRIPT_FILENAME como forma de assegurar que o nome do script
 * será desconsiderado na definição do controller e da action.
 */
beforeEach(function () {
    $this->_uris = [
        0 => ['uri' => 'http://www.example.com/'],
        1 => ['uri' => 'http://www.example.com/index.php'],
        2 => ['uri' => 'http://www.example.com/controller/action'],
        3 => ['uri' => 'http://www.example.com/index.php/controller/action'],
        4 => [
            'uri' => 'http://www.example.com/module/controller/action',
            'baseurl' => 'http://www.example.com/module',
        ],
        5 => [
            'uri' => 'http://www.example.com/module/index.php/controller/action',
            'baseurl' => 'http://www.example.com/module',
        ],
        6 => [
            'uri' => 'http://www.example.com/module/controller',
            'baseurl' => 'http://www.example.com/module',
        ],
    ];

    $_SERVER['REQUEST_URI'] = $this->_uris[0]['uri'];
    $_SERVER['SCRIPT_FILENAME'] = '/var/www/ieducar/index.php';
    $this->_dispatcher = new CoreExt_Controller_Dispatcher_AbstractStub;

    $this->_setRequestUri = function ($index = 0) {
        $_SERVER['REQUEST_URI'] = array_key_exists($index, $this->_uris) ?
            $this->_uris[$index]['uri'] : $this->_uris[$index = 0]['uri'];

        // Configura a baseurl
        if (isset($this->_uris[$index]['baseurl'])) {
            $this->_dispatcher->getRequest()->setOptions(['baseurl' => $this->_uris[$index]['baseurl']]);
        }
    };

    $this->_getRequestUri = function ($index = 0) {
        return array_key_exists($index, $this->_uris) ?
            $this->_uris[$index]['uri'] : $this->_uris[0]['uri'];
    };
});

test('opcao de configuracao nao existente lanca excecao', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->_dispatcher->setOptions(['foo' => 'bar']);
});

test('dispatcher estabelece controller default', function () {
    $this->assertEquals('index', $this->_dispatcher->getControllerName(), ($this->_getRequestUri)(0));
    ($this->_setRequestUri)(1);
    $this->assertEquals('index', $this->_dispatcher->getControllerName(), ($this->_getRequestUri)(1));
});

test('dispatcher estabelece controller default configurado', function () {
    $this->_dispatcher->setOptions(['controller_default_name' => 'controller']);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), ($this->_getRequestUri)(1));
});

test('dispatcher estabelece action default', function () {
    $this->assertEquals('index', $this->_dispatcher->getActionName(), ($this->_getRequestUri)(0));
    ($this->_setRequestUri)(1);
    $this->assertEquals('index', $this->_dispatcher->getActionName(), ($this->_getRequestUri)(1));
});

test('dispatcher estabelece action default configurada', function () {
    $this->_dispatcher->setOptions(['action_default_name' => 'action']);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), ($this->_getRequestUri)(1));
});

test('dispatcher estabelece controller', function () {
    ($this->_setRequestUri)(2);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), ($this->_getRequestUri)(2));
    ($this->_setRequestUri)(3);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), ($this->_getRequestUri)(3));
    ($this->_setRequestUri)(4);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), ($this->_getRequestUri)(4));
    ($this->_setRequestUri)(5);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), ($this->_getRequestUri)(5));
    ($this->_setRequestUri)(6);
    $this->assertEquals('controller', $this->_dispatcher->getControllerName(), ($this->_getRequestUri)(6));
});

test('dispatcher estabelece action', function () {
    ($this->_setRequestUri)(2);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), ($this->_getRequestUri)(2));
    ($this->_setRequestUri)(3);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), ($this->_getRequestUri)(3));
    ($this->_setRequestUri)(4);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), ($this->_getRequestUri)(4));
    ($this->_setRequestUri)(5);
    $this->assertEquals('action', $this->_dispatcher->getActionName(), ($this->_getRequestUri)(5));
    ($this->_setRequestUri)(6);
    $this->assertEquals('index', $this->_dispatcher->getActionName(), ($this->_getRequestUri)(6));
});
