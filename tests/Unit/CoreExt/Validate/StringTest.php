<?php


/**
 * CoreExt_Validate_StringTest class.
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
class CoreExt_Validate_StringTest extends PHPUnit\Framework\TestCase
{
    protected $_validator = null;

    protected function setUp(): void
    {
        $this->_validator = new CoreExt_Validate_String();
    }

    public function testStringSomenteEspacoLancaExcecaoPorSerObrigatorio()
    {
        $this->expectException(\Exception::class);
        // São três espaços ascii 20.
        $this->assertTrue($this->_validator->isValid('   '));
    }

    public function testStringSemAlterarConfiguracaoBasica()
    {
        $this->assertTrue($this->_validator->isValid('abc'));
    }

    public function testStringMenorQueOTamanhoMinimoLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['min' => 5]);
        $this->assertTrue($this->_validator->isValid('Foo'));
    }

    public function testAlfaStringQueOTamanhoMaximoLancaExcecao()
    {
        $this->expectException(\Exception::class);
        $this->_validator->setOptions(['max' => 2]);
        $this->assertTrue($this->_validator->isValid('Foo'));
    }
}
