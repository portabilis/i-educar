<?php

use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\Depends;
use Tests\TestCase;

class CoreExt_SessionTest extends TestCase
{
    protected $_session = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_session = new CoreExt_Session(['session_auto_start' => false]);
    }

    public function test_opcao_de_configuracao_nao_existente_lanca_excecao()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->_session->setOptions(['foo' => 'bar']);
    }

    public function test_instancia_tem_session_instancia_storage_default_por_padrao()
    {
        $this->assertInstanceOf('CoreExt_Session_Storage_Default', $this->_session->getSessionStorage());
    }

    public function test_instancia_e_subclasse_de_array_access()
    {
        $this->assertInstanceOf('ArrayAccess', $this->_session);
    }

    public function test_instancia_e_subclasse_de_countable()
    {
        $this->assertInstanceOf('Countable', $this->_session);
    }

    public function test_instancia_e_subclasse_de_iterator()
    {
        $this->assertInstanceOf('Iterator', $this->_session);
    }

    #[BackupGlobals(false)]
    public function test_array_access()
    {
        $this->assertNull($this->_session['foo'], '[foo] is not null');

        $this->_session['bar'] = 'foo';
        $this->assertEquals('foo', $this->_session['bar'], '[bar] != foo');

        // $this->_session->offsetUnset('bar');
        unset($this->_session['bar']);
        $this->assertNull($this->_session['bar'], '[bar] not unset');
    }

    #[BackupGlobals(false)]
    #[Depends('test_array_access')]
    public function test_countable()
    {
        $this->assertEquals(0, count($this->_session));

        $this->_session['foo'] = 'bar';
        $this->assertEquals(1, count($this->_session));
    }

    public function test_overload()
    {
        $this->assertNull($this->_session->foo, '->foo is not null');

        $this->_session->bar = 'foo';
        $this->assertEquals('foo', $this->_session->bar, '->bar != foo');

        unset($this->_session->bar);
        $this->assertNull($this->_session->bar, '->bar not unset');
    }

    /**
     * Como CoreExt_Session_Abstract::offsetSet() converte a chave em string,
     * podemos acessá-los de forma dinâmica na forma $session->$key em um
     * iterador foreach, por exemplo.
     */
    public function test_iterator()
    {
        $expected = [
            '_1' => 'bar1', '_2' => 'bar2', '_3' => 'bar3',
        ];

        $this->_session['_1'] = 'bar1';
        $this->_session['_2'] = 'bar2';

        foreach ($this->_session as $key => $val) {
            $this->assertEquals($expected[$key], $val, sprintf('$expected[%s] != %s', $key, $val));
            $this->assertEquals($this->_session->$key, $val, sprintf('$session->%s != %s', $key, $val));
        }

        $this->_session['_3'] = 'bar3';
        foreach ($this->_session as $key => $val) {
            $this->assertEquals($expected[$key], $val, sprintf('$expected[%s] != %s', $key, $val));
            $this->assertEquals($this->_session->$key, $val, sprintf('$session->%s != %s', $key, $val));
        }
    }
}
