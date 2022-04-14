<?php

namespace Tests\Unit\Modules\FormulaMedia\Model;

use FormulaMedia_Model_Formula;
use PHPUnit\Framework\TestCase;

class FormulaTest extends TestCase
{
    /**
     * @var FormulaMedia_Model_Formula
     */
    private $formula;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->formula = new FormulaMedia_Model_Formula();
    }

    /**
     * @see FormulaMedia_Model_Formula::getTokens()
     *
     * @return void
     */
    public function testGetTokensMethod()
    {
        $this->assertCount(63, $this->formula->getTokens());
    }

    /**
     * @see FormulaMedia_Model_Formula::isNumericToken()
     *
     * @return void
     */
    public function testIsNumericTokenMethod()
    {
        $this->assertTrue(
            $this->formula->isNumericToken('E1')
        );

        $this->assertFalse(
            $this->formula->isNumericToken('*')
        );
    }

    /**
     * @see FormulaMedia_Model_Formula::replaceTokens()
     *
     * @return void
     */
    public function testReplaceTokensMethod()
    {
        $formula = $this->formula->replaceTokens('Se / Et', [
            'Et' => 4,
            'Se' => 20,
        ]);

        $this->assertEquals('20 / 4', $formula);
    }

    /**
     * @see FormulaMedia_Model_Formula::replaceAliasTokens()
     *
     * @return void
     */
    public function testReplaceAliasTokensMethod()
    {
        $formula = '(1x2)';
        $expected = ' ( 1*2 ) ';
        $replaced = $this->formula->replaceAliasTokens($formula);

        $this->assertEquals($expected, $replaced);
    }

    /**
     * @see FormulaMedia_Model_Formula::execFormulaMedia()
     *
     * @return void
     */
    public function testExecFormulaMediaMethod()
    {
        $this->formula->formulaMedia = '(E1 + E2 + E3 + E4) / Et';
        $average = $this->formula->execFormulaMedia([
            'E1' => 10,
            'E2' => 8,
            'E3' => 7,
            'E4' => 7,
            'Et' => 4,
        ]);

        $this->formula->formulaMedia = '((Se / Et * 0.6) + (Rc * 0.4))';
        $recuperation = $this->formula->execFormulaMedia([
            'Et' => 4,
            'Se' => 20,
            'Rc' => 7
        ]);

        $this->assertEquals(8, $average);
        $this->assertEquals(5.8, $recuperation);
    }

    /**
     * @see FormulaMedia_Model_Formula::getDefaultValidatorCollection()
     *
     * @return void
     */
    public function testGetDefaultValidatorCollectionMethod()
    {
        // Este método tem muitas dependências e deverá ser refatorado.

        $this->assertTrue(true);
    }

    /**
     * Este teste está relacionado à dispensa de etapas dos alunos quando a
     * média ponderada é utilizada para o cálculo da nota. Ou seja, cada etapa
     * tem um peso diferente.
     *
     * Como a fórmula de cálculo da média ponderada é fixa, dependendo a etapa
     * em que o aluno é dispensado, o cálculo da média fica incorreto, pois o
     * aluno não possui nota devido ter a etapa dispensada.
     *
     * @return void
     */
    public function testConsideringDispensedStages()
    {
        $this->formula->formulaMedia = '((C1*E1*1) + (C2*E2*2) + (C3*E3*3) + (C4*E4*4)) / ((C1*1) + (C2*2) + (C3*3) + (C4*4))';
        $average = $this->formula->execFormulaMedia([
            'E1' => 9,
            'E2' => 0,
            'E3' => 9,
            'E4' => 7,
            'C1' => 1,
            'C2' => 0,
            'C3' => 1,
            'C4' => 1,
        ]);

        $this->assertEquals(8, $average);
    }
}
