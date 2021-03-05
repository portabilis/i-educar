<?php


/**
 * ValidateFormulaTest class.
 *
 * @author      Eriksen Costa Paixão <eriksen.paixao_bs@cobra.com.br>
 *
 * @category    i-Educar
 *
 * @license     @@license@@
 *
 * @package     FormulaMedia
 * @subpackage  UnitTests
 *
 * @since       Classe disponível desde a versão 1.1.0
 *
 * @version     @@package_version@@
 */
class ValidateFormulaTest extends UnitBaseTest
{
    public function testFormulaValida()
    {
        $formula = 'Se / Et';
        $validator = new FormulaMedia_Validate_Formula();
        $this->assertTrue($validator->isValid($formula));
    }

    public function testFormulaValidaUsandoAliasDeMultiplicacao()
    {
        $formula = 'Se x 0.99 / Et';
        $validator = new FormulaMedia_Validate_Formula();
        $this->assertTrue($validator->isValid($formula));
    }

    public function testFormulaValidaComNumericos()
    {
        $formula = 'Se * 0.5 / Et';
        $validator = new FormulaMedia_Validate_Formula();
        $this->assertTrue($validator->isValid($formula));
    }

    public function testFormulaInvalidaQuandoUtilizaTokenNaoPermitido()
    {
        $this->expectException(\Exception::class);
        $formula = 'Rc * 0.4 + Se * 0.6';
        $validator = new FormulaMedia_Validate_Formula();
        $this->assertTrue($validator->isValid($formula));
    }

    public function testFormulaValidaUsandoParenteses()
    {
        $formula = '(Rc * 0.4) + (Se * 0.6)';
        $validator = new FormulaMedia_Validate_Formula(['excludeToken' => null]);
        $this->assertTrue($validator->isValid($formula));
    }

    public function testFormulaInvalidaPorErroDeSintaxe()
    {
        $this->expectException(\Error::class);
        $formula = '(Rc * 0.4) + (Se * 0.6) ()';
        $validator = new FormulaMedia_Validate_Formula(['excludeToken' => null]);
        $this->assertTrue($validator->isValid($formula));
    }
}
