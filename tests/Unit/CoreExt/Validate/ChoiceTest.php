<?php

class CoreExt_Validate_ChoiceTest extends PHPUnit\Framework\TestCase
{
    protected $_validator = null;

    protected $_choices = [
        'bit' => [0, 1],
        'various' => ['sim', 'não', 'nda']
    ];

    protected function setUp(): void
    {
        $this->_validator = new CoreExt_Validate_Choice();
    }

    public function testValidaSeNenhumaOpcaoPadraoForInformada()
    {
        $this->assertTrue($this->_validator->isValid(0));
    }

    public function testEscolhaValida()
    {
        $this->_validator->setOptions(['choices' => $this->_choices['bit']]);
        $this->assertTrue($this->_validator->isValid(0), 'Falhou na asserção "0" numérico.');
        $this->assertTrue($this->_validator->isValid(1), 'Falhou na asserção "1" numérico.');

        // Teste para verificar como reage a tipos diferentes
        $this->assertTrue($this->_validator->isValid('0'), 'Falhou na asserção "0" string.');
        $this->assertTrue($this->_validator->isValid('1'), 'Falhou na asserção "1" string.');

        $this->_validator->setOptions(['choices' => $this->_choices['various']]);
        $this->assertTrue($this->_validator->isValid('sim'));
        $this->assertTrue($this->_validator->isValid('não'));
        $this->assertTrue($this->_validator->isValid('nda'));
    }

    public function testEscolhaInvalidaLancaExcecao()
    {
        $this->_validator->setOptions(['choices' => $this->_choices['bit']]);

        try {
            $this->_validator->isValid(2);
            $this->fail('CoreExt_Validate_Choice deveria ter lançado exceção.');
        } catch (Exception $e) {
            $this->assertEquals('A opção "2" não existe.', $e->getMessage());
        }

        // 'a' normalmente seria avaliado como 0, mas queremos garantir que isso
        // não ocorra, por isso transformamos tudo em string em _validate().
        try {
            $this->_validator->isValid('a');
            $this->fail('CoreExt_Validate_Choice deveria ter lançado exceção.');
        } catch (Exception $e) {
            $this->assertEquals('A opção "a" não existe.', $e->getMessage());
        }

        try {
            $this->_validator->isValid('0a');
            $this->fail('CoreExt_Validate_Choice deveria ter lançado exceção.');
        } catch (Exception $e) {
            $this->assertEquals('A opção "0a" não existe.', $e->getMessage());
        }
    }
}
