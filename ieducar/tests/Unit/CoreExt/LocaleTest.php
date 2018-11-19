<?php

require_once 'CoreExt/Locale.php';

class CoreExt_LocaleTest extends UnitBaseTest
{
  protected $_instance = NULL;

  protected function setUp()
  {
    $this->_instance = CoreExt_Locale::getInstance();
    $this->_instance->resetLocale();
  }

  public function testFloatComOLocaleDefault()
  {
    $float = 3.5;
    $this->assertEquals('3.5', (string) $float);
  }

  public function testFloatComUmLocaleQueUsaVirgulaParaSepararDecimais()
  {
    $this->_instance->setCulture('pt_BR')->setLocale();
    if (strpos($this->_instance->actualCulture['LC_NUMERIC'], 'pt_BR') === false) {
        $this->markTestSkipped('Locale pt_BR não instalado.');
    }
    $float = 3.5;
    $this->assertEquals('3,5', (string) $float);
  }

  public function testResetDeLocale()
  {
    $this->_instance->setLocale('pt_BR');
    if (strpos($this->_instance->actualCulture['LC_NUMERIC'], 'pt_BR') === false) {
        $this->markTestSkipped('Locale pt_BR não instalado.');
    }
    $float = 3.5;
    $this->assertEquals('3,5', (string) $float);
    $this->_instance->resetLocale();
    $this->assertEquals('3.5', (string) $float);
  }

  public function testInformacaoDeNumericosDoLocale()
  {
    $cultureInfo = $this->_instance->getCultureInfo();
    $this->assertEquals(18, count($cultureInfo));
    $this->assertEquals('.', $this->_instance->getCultureInfo('decimal_point'));

    $this->_instance->setLocale('pt_BR');
    if (strpos($this->_instance->actualCulture['LC_NUMERIC'], 'pt_BR') === false) {
        $this->markTestSkipped('Locale pt_BR não instalado.');
    }
    $this->assertEquals(',', $this->_instance->getCultureInfo('decimal_point'));
  }
}