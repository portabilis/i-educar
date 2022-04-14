<?php

class CoreExt_Controller_DispatcherTest extends PHPUnit\Framework\TestCase
{
    protected $_dispatcher = null;

    protected $_uris = [
        0 => ['uri' => 'http://www.example.com/'],
        1 => ['uri' => 'http://www.example.com/index.php'],
        2 => ['uri' => 'http://www.example.com/controller/action'],
        3 => ['uri' => 'http://www.example.com/index.php/controller/action'],
        4 => [
            'uri' => 'http://www.example.com/module/controller/action',
            'baseurl' => 'http://www.example.com/module'
        ],
        5 => [
            'uri' => 'http://www.example.com/module/index.php/controller/action',
            'baseurl' => 'http://www.example.com/module'
        ],
        6 => [
            'uri' => 'http://www.example.com/module/controller',
            'baseurl' => 'http://www.example.com/module'
        ]
    ];

    /**
     * Configura SCRIPT_FILENAME como forma de assegurar que o nome do script
     * será desconsiderado na definição do controller e da action.
     */
    protected function setUp(): void
    {
        $_SERVER['REQUEST_URI'] = $this->_uris[0]['uri'];
        $_SERVER['SCRIPT_FILENAME'] = '/var/www/ieducar/index.php';
        $this->_dispatcher = new CoreExt_Controller_Dispatcher_AbstractStub();
    }

    protected function _setRequestUri($index = 0)
    {
        $_SERVER['REQUEST_URI'] = array_key_exists($index, $this->_uris) ?
            $this->_uris[$index]['uri'] : $this->_uris[$index = 0]['uri'];

        // Configura a baseurl
        if (isset($this->_uris[$index]['baseurl'])) {
            $this->_dispatcher->getRequest()->setOptions(['baseurl' => $this->_uris[$index]['baseurl']]);
        }
    }

    protected function _getRequestUri($index = 0)
    {
        return array_key_exists($index, $this->_uris) ?
            $this->_uris[$index]['uri'] : $this->_uris[0]['uri'];
    }

    public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->_dispatcher->setOptions(['foo' => 'bar']);
    }

    public function testDispatcherEstabeleceControllerDefault()
    {
        $this->assertEquals('index', $this->_dispatcher->getControllerName(), $this->_getRequestUri(0));
        $this->_setRequestUri(1);
        $this->assertEquals('index', $this->_dispatcher->getControllerName(), $this->_getRequestUri(1));
    }

    public function testDispatcherEstabeleceControllerDefaultConfigurado()
    {
        $this->_dispatcher->setOptions(['controller_default_name' => 'controller']);
        $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(1));
    }

    public function testDispatcherEstabeleceActionDefault()
    {
        $this->assertEquals('index', $this->_dispatcher->getActionName(), $this->_getRequestUri(0));
        $this->_setRequestUri(1);
        $this->assertEquals('index', $this->_dispatcher->getActionName(), $this->_getRequestUri(1));
    }

    public function testDispatcherEstabeleceActionDefaultConfigurada()
    {
        $this->_dispatcher->setOptions(['action_default_name' => 'action']);
        $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(1));
    }

    public function testDispatcherEstabeleceController()
    {
        $this->_setRequestUri(2);
        $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(2));
        $this->_setRequestUri(3);
        $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(3));
        $this->_setRequestUri(4);
        $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(4));
        $this->_setRequestUri(5);
        $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(5));
        $this->_setRequestUri(6);
        $this->assertEquals('controller', $this->_dispatcher->getControllerName(), $this->_getRequestUri(6));
    }

    public function testDispatcherEstabeleceAction()
    {
        $this->_setRequestUri(2);
        $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(2));
        $this->_setRequestUri(3);
        $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(3));
        $this->_setRequestUri(4);
        $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(4));
        $this->_setRequestUri(5);
        $this->assertEquals('action', $this->_dispatcher->getActionName(), $this->_getRequestUri(5));
        $this->_setRequestUri(6);
        $this->assertEquals('index', $this->_dispatcher->getActionName(), $this->_getRequestUri(6));
    }
}
