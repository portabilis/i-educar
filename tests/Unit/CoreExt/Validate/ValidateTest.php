<?php


/**
 * CoreExt_ValidateTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     CoreExt_Validate
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class CoreExt_ValidateTest extends PHPUnit\Framework\TestCase
{
    protected $_validator = null;

    protected function setUp(): void
    {
        $this->_validator = new CoreExt_ValidateStub();
    }

    public function testOpcaoDeConfiguracaoNaoExistenteLancaExcecao()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->_validator->setOptions(['invalidOption' => true]);
    }

    public function testConfiguraOpcaoDoValidator()
    {
        $this->_validator->setOptions(['required' => false]);

        $options = $this->_validator->getOptions();
        $this->assertFalse($options['required']);

        $this->assertFalse($this->_validator->getOption('required'));
    }

    public function testValorStringSomenteEspacoRequerido()
    {
        $this->expectException(\Exception::class);
        // Um espaço ASCII
        $this->assertTrue($this->_validator->isValid(' '));
    }

    public function testValorNuloLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->assertTrue($this->_validator->isValid(null));
    }

    public function testValorArrayVazioLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->assertTrue($this->_validator->isValid([]));
    }

    public function testValorNaoObrigatorioComConfiguracaoNaInstanciacao()
    {
        $validator = new CoreExt_Validate_String(['required' => false]);
        $this->assertTrue($validator->isValid(''));
    }

    public function testValorNaoObrigatorioComConfiguracaoViaMetodo()
    {
        $this->_validator->setOptions(['required' => false]);
        $this->assertTrue($this->_validator->isValid(''));
    }
}
