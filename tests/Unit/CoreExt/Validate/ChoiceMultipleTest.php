<?php

class CoreExt_Validate_ChoiceMultipleTest extends PHPUnit\Framework\TestCase
{
    protected $_validator = null;

    protected $_choices = [
        'bit' => [0, 1],
        'various' => ['sim', 'não', 'nda']
    ];

    protected function setUp(): void
    {
        $this->_validator = new CoreExt_Validate_ChoiceMultiple();
    }

    public function testEscolhaMultiplaValida()
    {
        $this->_validator->setOptions(['choices' => $this->_choices['bit']]);
        $this->assertTrue($this->_validator->isValid([0, 1]));

        // Testa com valor igual, mas tipo de dado diferente
        $this->assertTrue($this->_validator->isValid(['0', '1']));
    }

    public function testEscolhaMultiplaInvalidaLancaExcecao()
    {
        $this->_validator->setOptions(['choices' => $this->_choices['bit']]);

        try {
            $this->_validator->isValid([0, 2, 3]);
            $this->fail('CoreExt_Validate_ChoiceMultiple deveria ter lançado exceção.');
        } catch (Exception $e) {
            $this->assertEquals('As opções "2, 3" não existem.', $e->getMessage());
        }

        // 'a' e '0a' normalmente seriam avaliados como '0' e '1' mas não queremos
        // esse tipo de comportamento.
        try {
            $this->_validator->isValid([0, 'a', '1a']);
            $this->fail('CoreExt_Validate_ChoiceMultiple deveria ter lançado exceção.');
        } catch (Exception $e) {
            $this->assertEquals('As opções "a, 1a" não existem.', $e->getMessage());
        }
    }
}
