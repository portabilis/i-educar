<?php

class CoreExt_Controller_RequestTest extends PHPUnit\Framework\TestCase
{
    protected $_request = null;

    protected function setUp(): void
    {
        $this->_request = new CoreExt_Controller_Request;
    }

    public function test_opcao_de_configuracao_nao_existente_lanca_excecao()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->_request->setOptions(['foo' => 'bar']);
    }

    public function test_retorna_null_caso_nao_esteja_setado_nas_superglobais_get_post_cookie_e_server()
    {
        $this->assertNull($this->_request->get('foo'));
    }

    public function test_variavel_esta_setada()
    {
        $_GET['name'] = 'Foo';
        $this->assertTrue(isset($this->_request->name));
        unset($_GET['name']);
        $this->assertFalse(isset($this->_request->name));
    }

    public function test_recupera_parametro_de_requisicao_get()
    {
        $_GET['name'] = 'Foo';
        $this->assertEquals($_GET['name'], $this->_request->get('name'));
    }

    public function test_recupera_parametro_de_requisicao_post()
    {
        $_POST['name'] = 'Foo';
        $this->assertEquals($_POST['name'], $this->_request->get('name'));
    }

    public function test_recupera_parametro_do_cookie()
    {
        $_COOKIE['name'] = 'Foo';
        $this->assertEquals($_COOKIE['name'], $this->_request->get('name'));
    }

    public function test_recupera_parametro_do_server()
    {
        $_SERVER['REQUEST_URI'] = 'http://www.example.com/controller';
        $this->assertEquals($_SERVER['REQUEST_URI'], $this->_request->get('REQUEST_URI'));
    }

    public function test_configura_baseurl_com_scheme_e_host_por_padrao()
    {
        $_SERVER['REQUEST_URI'] = 'http://www.example.com/controller';
        $this->assertEquals('http://www.example.com', $this->_request->getBaseurl());
    }
}
