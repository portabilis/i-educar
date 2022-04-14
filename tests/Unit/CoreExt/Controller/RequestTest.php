<?php

class CoreExt_Controller_RequestTest extends PHPUnit\Framework\TestCase
{
    protected $_request = null;

    protected function setUp(): void
    {
        $this->_request = new CoreExt_Controller_Request();
    }

    public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->_request->setOptions(['foo' => 'bar']);
    }

    public function testRetornaNullCasoNaoEstejaSetadoNasSuperglobaisGetPostCookieEServer()
    {
        $this->assertNull($this->_request->get('foo'));
    }

    public function testVariavelEstaSetada()
    {
        $_GET['name'] = 'Foo';
        $this->assertTrue(isset($this->_request->name));
        unset($_GET['name']);
        $this->assertFalse(isset($this->_request->name));
    }

    public function testRecuperaParametroDeRequisicaoGet()
    {
        $_GET['name'] = 'Foo';
        $this->assertEquals($_GET['name'], $this->_request->get('name'));
    }

    public function testRecuperaParametroDeRequisicaoPost()
    {
        $_POST['name'] = 'Foo';
        $this->assertEquals($_POST['name'], $this->_request->get('name'));
    }

    public function testRecuperaParametroDoCookie()
    {
        $_COOKIE['name'] = 'Foo';
        $this->assertEquals($_COOKIE['name'], $this->_request->get('name'));
    }

    public function testRecuperaParametroDoServer()
    {
        $_SERVER['REQUEST_URI'] = 'http://www.example.com/controller';
        $this->assertEquals($_SERVER['REQUEST_URI'], $this->_request->get('REQUEST_URI'));
    }

    public function testConfiguraBaseurlComSchemeEHostPorPadrao()
    {
        $_SERVER['REQUEST_URI'] = 'http://www.example.com/controller';
        $this->assertEquals('http://www.example.com', $this->_request->getBaseurl());
    }
}
