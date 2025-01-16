<?php

class CoreExt_Validate_NumericTest extends PHPUnit\Framework\TestCase
{
    protected $_validator = null;

    protected function setUp(): void
    {
        $this->_validator = new CoreExt_Validate_Numeric;
    }

    public function test_valor_string_vazia_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->isValid('');
    }

    public function test_valor_string_espaco_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        // São três espaço ascii 20
        $this->_validator->isValid('   ');
    }

    public function test_valor_null_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->isValid(null);
    }

    public function test_valor_nao_numerico_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->isValid('zero');
    }

    public function test_valor_null_nao_lanca_excecao_se_required_for_false()
    {
        $this->_validator->setOptions(['required' => false]);
        $this->assertTrue($this->_validator->isValid(null));
    }

    public function test_valor_numerico_sem_configurar_o_validador()
    {
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1.5));
        $this->assertTrue($this->_validator->isValid(-1.5));
    }

    public function test_valores_dentro_de_um_range_configurado_no_validador()
    {
        $this->_validator->setOptions(['min' => -50, 'max' => 50]);
        $this->assertTrue($this->_validator->isValid(50));
        $this->assertTrue($this->_validator->isValid(50));
        $this->assertTrue($this->_validator->isValid(50.00));
        $this->assertTrue($this->_validator->isValid(-50.00));
        $this->assertTrue($this->_validator->isValid(49.9999));
        $this->assertTrue($this->_validator->isValid(-49.9999));
    }

    public function test_valor_menor_que_o_permitido_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['min' => 0]);
        $this->_validator->isValid(-1);
    }

    public function test_valor_ponto_flutuante_menor_que_o_permitido_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['min' => 0]);
        $this->_validator->isValid(-1.5);
    }

    public function test_valor_maior_que_o_permitido_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['max' => 0]);
        $this->_validator->isValid(1);
    }

    public function test_valor_ponto_flutuante_maior_que_o_permitido_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['max' => 0]);
        $this->_validator->isValid(1.5);
    }
}
