<?php

require_once 'CoreExt/Validate/Numeric.php';
require_once 'CoreExt/Locale.php';

class CoreExt_Validate_NumericTest extends PHPUnit\Framework\TestCase
{
  protected $_validator = NULL;

  protected function setUp()
  {
    $this->_validator = new CoreExt_Validate_Numeric();
  }

  /**
   * @expectedException Exception
   */
  public function testValorStringVaziaLancaExcecao()
  {
    $this->_validator->isValid('');
  }

  /**
   * @expectedException Exception
   */
  public function testValorStringEspacoLancaExcecao()
  {
    // São três espaço ascii 20
    $this->_validator->isValid('   ');
  }

  /**
   * @expectedException Exception
   */
  public function testValorNullLancaExcecao()
  {
    $this->_validator->isValid(NULL);
  }

  /**
   * @expectedException Exception
   */
  public function testValorNaoNumericoLancaExcecao()
  {
    $this->_validator->isValid('zero');
  }

  public function testValorNullNaoLancaExcecaoSeRequiredForFalse()
  {
    $this->_validator->setOptions(array('required' => FALSE));
    $this->assertTrue($this->_validator->isValid(NULL));
  }

  public function testValorNumericoSemConfigurarOValidador()
  {
    $this->assertTrue($this->_validator->isValid(0));
    $this->assertTrue($this->_validator->isValid(1.5));
    $this->assertTrue($this->_validator->isValid(-1.5));
  }

  public function testValoresDentroDeUmRangeConfiguradoNoValidador()
  {
    $this->_validator->setOptions(array('min' => -50, 'max' => 50));
    $this->assertTrue($this->_validator->isValid(50));
    $this->assertTrue($this->_validator->isValid(50));
    $this->assertTrue($this->_validator->isValid(50.00));
    $this->assertTrue($this->_validator->isValid(-50.00));
    $this->assertTrue($this->_validator->isValid(49.9999));
    $this->assertTrue($this->_validator->isValid(-49.9999));
  }

  /**
   * @expectedException Exception
   */
  public function testValorMenorQueOPermitidoLancaExcecao()
  {
    $this->_validator->setOptions(array('min' => 0));
    $this->_validator->isValid(-1);
  }

  /**
   * @expectedException Exception
   */
  public function testValorPontoFlutuanteMenorQueOPermitidoLancaExcecao()
  {
    $this->_validator->setOptions(array('min' => 0));
    $this->_validator->isValid(-1.5);
  }

  /**
   * @expectedException Exception
   */
  public function testValorMaiorQueOPermitidoLancaExcecao()
  {
    $this->_validator->setOptions(array('max' => 0));
    $this->_validator->isValid(1);
  }

  /**
   * @expectedException Exception
   */
  public function testValorPontoFlutuanteMaiorQueOPermitidoLancaExcecao()
  {
    $this->_validator->setOptions(array('max' => 0));
    $this->_validator->isValid(1.5);
  }

  /**
   * @group CoreExt_Locale
   */
  public function testValorNumericoSemConfigurarOValidadorUsandoLocaleComSeparadorDecimalDiferenteDePonto()
  {
    $locale = CoreExt_Locale::getInstance();
    $locale->setLocale('pt_BR');

    if($locale->actualCulture['LC_ALL'] == 'C') {
        $this->markTestSkipped('Locale não instalado.');
    }
    $this->assertTrue($this->_validator->isValid('0,0'));
    $this->assertTrue($this->_validator->isValid('1,5'));
    $this->assertTrue($this->_validator->isValid('-1.5'));
  }

  /**
   * @group CoreExt_Locale
   */
  public function testValorNumericoSemConfigurarOValidadorUsandoLocaleComSeparadorPontoParaDecimal()
  {
    $locale = CoreExt_Locale::getInstance();
    $locale->setLocale('en_US');

    if($locale->actualCulture['LC_ALL'] == 'C') {
        $this->markTestSkipped('Locale não instalado.');
    }
    $this->assertTrue($this->_validator->isValid('0.0'));
    $this->assertTrue($this->_validator->isValid('1.5'));
    $this->assertTrue($this->_validator->isValid('-1.5'));
  }
}