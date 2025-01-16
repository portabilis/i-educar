<?php

use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CoreExt_Session_Storage_DefaultTest extends TestCase
{
    protected $_storage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_storage = new CoreExt_Session_Storage_Default;
    }

    public function test_instancia_e_subclasse_de_countable()
    {
        $this->assertInstanceOf('Countable', $this->_storage);
    }

    public function test_escreve_dados_na_session()
    {
        $this->_storage->write('foo', 'bar');
        $this->_storage->write('foo/1', 'bar/1');
        $this->_storage->write('foo/2', 'bar/2');
        $this->_storage->write('foo/3', 'bar/3');

        $this->assertEquals('bar', Session::get('foo'));
        $this->assertEquals('bar/1', Session::get('foo/1'));
        $this->assertEquals('bar/2', Session::get('foo/2'));
        $this->assertEquals('bar/3', Session::get('foo/3'));
    }

    /**
     * @depends test_escreve_dados_na_session
     */
    public function test_ler_dados_armazenados_na_session()
    {
        $this->_storage->write('foo', 'bar');
        $this->_storage->write('foo/1', 'bar/1');
        $this->_storage->write('foo/2', 'bar/2');
        $this->_storage->write('foo/3', 'bar/3');

        $this->assertEquals('bar', $this->_storage->read('foo'));
        $this->assertEquals('bar/1', $this->_storage->read('foo/1'));
        $this->assertEquals('bar/2', $this->_storage->read('foo/2'));
        $this->assertEquals('bar/3', $this->_storage->read('foo/3'));
    }

    public function test_remove_indice_da_session()
    {
        $this->_storage->remove('bar/3');
        $this->assertNull($this->_storage->read('bar/3'));
    }

    public function test_indice_nao_existente_na_session_retorna_null()
    {
        $this->assertNull($this->_storage->read('null'));
    }
}
