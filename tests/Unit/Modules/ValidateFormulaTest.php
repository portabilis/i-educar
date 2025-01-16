<?php

class ValidateFormulaTest extends UnitBaseTest
{
    public function test_formula_valida()
    {
        $formula = 'Se / Et';
        $validator = new FormulaMedia_Validate_Formula;
        $this->assertTrue($validator->isValid($formula));
    }

    public function test_formula_valida_usando_alias_de_multiplicacao()
    {
        $formula = 'Se x 0.99 / Et';
        $validator = new FormulaMedia_Validate_Formula;
        $this->assertTrue($validator->isValid($formula));
    }

    public function test_formula_valida_com_numericos()
    {
        $formula = 'Se * 0.5 / Et';
        $validator = new FormulaMedia_Validate_Formula;
        $this->assertTrue($validator->isValid($formula));
    }

    public function test_formula_invalida_quando_utiliza_token_nao_permitido()
    {
        $this->expectException(\Exception::class);
        $formula = 'Rc * 0.4 + Se * 0.6';
        $validator = new FormulaMedia_Validate_Formula;
        $this->assertTrue($validator->isValid($formula));
    }

    public function test_formula_valida_usando_parenteses()
    {
        $formula = '(Rc * 0.4) + (Se * 0.6)';
        $validator = new FormulaMedia_Validate_Formula(['excludeToken' => null]);
        $this->assertTrue($validator->isValid($formula));
    }

    public function test_formula_invalida_por_erro_de_sintaxe()
    {
        $this->expectException(\Error::class);
        $formula = '(Rc * 0.4) + (Se * 0.6) ()';
        $validator = new FormulaMedia_Validate_Formula(['excludeToken' => null]);
        $this->assertTrue($validator->isValid($formula));
    }
}
