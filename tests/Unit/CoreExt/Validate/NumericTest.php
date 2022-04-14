<?php

class CoreExt_Validate_NumericTest extends PHPUnit\Framework\TestCase
{
    protected $_validator = null;

    protected function setUp(): void
    {
        $this->_validator = new CoreExt_Validate_Numeric();
    }

    public function testValorStringVaziaLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->isValid('');
    }

    public function testValorStringEspacoLancaExcecao()
    {
        $this->expectException(\Exception::class);
        // São três espaço ascii 20
        $this->_validator->isValid('   ');
    }

    public function testValorNullLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->isValid(null);
    }

    public function testValorNaoNumericoLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->isValid('zero');
    }

    public function testValorNullNaoLancaExcecaoSeRequiredForFalse()
    {
        $this->_validator->setOptions(['required' => false]);
        $this->assertTrue($this->_validator->isValid(null));
    }

    public function testValorNumericoSemConfigurarOValidador()
    {
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1.5));
        $this->assertTrue($this->_validator->isValid(-1.5));
    }

    public function testValoresDentroDeUmRangeConfiguradoNoValidador()
    {
        $this->_validator->setOptions(['min' => -50, 'max' => 50]);
        $this->assertTrue($this->_validator->isValid(50));
        $this->assertTrue($this->_validator->isValid(50));
        $this->assertTrue($this->_validator->isValid(50.00));
        $this->assertTrue($this->_validator->isValid(-50.00));
        $this->assertTrue($this->_validator->isValid(49.9999));
        $this->assertTrue($this->_validator->isValid(-49.9999));
    }

    public function testValorMenorQueOPermitidoLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['min' => 0]);
        $this->_validator->isValid(-1);
    }

    public function testValorPontoFlutuanteMenorQueOPermitidoLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['min' => 0]);
        $this->_validator->isValid(-1.5);
    }

    public function testValorMaiorQueOPermitidoLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['max' => 0]);
        $this->_validator->isValid(1);
    }

    public function testValorPontoFlutuanteMaiorQueOPermitidoLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['max' => 0]);
        $this->_validator->isValid(1.5);
    }
}
