<?php

class FormulaTest extends UnitBaseTest
{
    protected $_entity = null;

    protected $_values = [
        'E1' => 5,
        'E2' => 5,
        'E3' => 5,
        'E4' => 5,
        'Et' => 4,
        'Se' => 20,
        'Rc' => 0
    ];

    protected function setUp(): void
    {
        $this->_entity = new FormulaMedia_Model_Formula();
    }

    public function testSubstituiCorretamenteAsTokens()
    {
        $formula = $this->_entity->replaceTokens('Se / Et', $this->_values);
        $this->assertEquals('20 / 4', $formula);
    }

    public function testFormulaDeMediaRetornaValorNumerico()
    {
        $this->_entity->formulaMedia = '(E1 + E2 + E3 + E4) / Et';
        $this->assertEquals(5, $this->_entity->execFormulaMedia($this->_values));
    }

    public function testFormulaDeRecuperacaoRetornaValorNumerico()
    {
        $this->_entity->formulaMedia = '((Se / Et * 0.6) + (Rc * 0.4))';
        $values = $this->_values;
        $values['Rc'] = 7;
        $nota = $this->_entity->execFormulaMedia($values);
        $this->assertEqualsWithDelta(5.8, $nota, 0.3);
    }

    public function testEntityValidators()
    {
        // Valores de retorno
        $returnValue = [['cod_instituicao' => 1, 'nm_instituicao' => 'Instituição']];

        // Mock para instituição
        $mock = $this->getCleanMock('clsPmieducarInstituicao');
        $mock->expects($this->any())
            ->method('lista')
            ->will($this->returnValue($returnValue));

        $this->_entity->addClassToStorage('clsPmieducarInstituicao', $mock);

        $validators = $this->_entity->getDefaultValidatorCollection();
        $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['instituicao']);
        $this->assertInstanceOf('CoreExt_Validate_String', $validators['nome']);
        $this->assertInstanceOf('FormulaMedia_Validate_Formula', $validators['formulaMedia']);
        $this->assertInstanceOf('CoreExt_Validate_Choice', $validators['tipoFormula']);

        // Se o tipo da fórmula for de média final, o validador irá lançar uma
        // exceção com a token Rc (Recuperação)
        try {
            $validators['formulaMedia']->isValid('Se + Rc / 4');
            $this->fail('Fórmula deveria ter lançado exceção (Se + Rc / 4) pois o '
                . 'validador está com a configuração padrão');
        } catch (Exception $e) {
        }

        // Configura a instância de FormulaMedia_Model_Formula para ser do tipo
        // "média recuperação", para verificar o validador.
        // Referências podem ter seus valores atribuídos apenas na instanciação
        // sendo assim imutáveis. Por isso, um novo objeto.
        $this->_entity = new FormulaMedia_Model_Formula(['tipoFormula' => 2]);
        $validators = $this->_entity->getDefaultValidatorCollection();

        try {
            $validators['formulaMedia']->isValid('Se + Rc / 4');
        } catch (Exception $e) {
            $this->fail('Fórmula não deveria ter lançado exceção.');
        }
    }
}
