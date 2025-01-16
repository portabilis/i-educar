<?php

class CoreExt_Validate_StringTest extends PHPUnit\Framework\TestCase
{
    protected $_validator = null;

    protected function setUp(): void
    {
        $this->_validator = new CoreExt_Validate_String;
    }

    public function test_string_somente_espaco_lanca_excecao_por_ser_obrigatorio()
    {
        $this->expectException(\Exception::class);
        // São três espaços ascii 20.
        $this->assertTrue($this->_validator->isValid('   '));
    }

    public function test_string_sem_alterar_configuracao_basica()
    {
        $this->assertTrue($this->_validator->isValid('abc'));
    }

    public function test_string_menor_que_o_tamanho_minimo_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['min' => 5]);
        $this->assertTrue($this->_validator->isValid('Foo'));
    }

    public function test_alfa_string_que_o_tamanho_maximo_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['max' => 2]);
        $this->assertTrue($this->_validator->isValid('Foo'));
    }
}
