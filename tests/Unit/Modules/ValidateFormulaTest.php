<?php

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
