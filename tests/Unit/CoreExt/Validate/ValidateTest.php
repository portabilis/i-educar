<?php

class CoreExt_ValidateTest extends PHPUnit\Framework\TestCase
{
    protected $_validator = null;

    protected function setUp(): void
    {
        $this->_validator = new CoreExt_ValidateStub;
    }

    public function test_opcao_de_configuracao_nao_existente_lanca_excecao()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->_validator->setOptions(['invalidOption' => true]);
    }

    public function test_configura_opcao_do_validator()
    {
        $this->_validator->setOptions(['required' => false]);

        $options = $this->_validator->getOptions();
        $this->assertFalse($options['required']);

        $this->assertFalse($this->_validator->getOption('required'));
    }

    public function test_valor_string_somente_espaco_requerido()
    {
        $this->expectException(\Exception::class);
        // Um espaço ASCII
        $this->assertTrue($this->_validator->isValid(' '));
    }

    public function test_valor_nulo_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->assertTrue($this->_validator->isValid(null));
    }

    public function test_valor_array_vazio_lanca_excecao()
    {
        $this->expectException(\Exception::class);
        $this->assertTrue($this->_validator->isValid([]));
    }

    public function test_valor_nao_obrigatorio_com_configuracao_na_instanciacao()
    {
        $validator = new CoreExt_Validate_String(['required' => false]);
        $this->assertTrue($validator->isValid(''));
    }

    public function test_valor_nao_obrigatorio_com_configuracao_via_metodo()
    {
        $this->_validator->setOptions(['required' => false]);
        $this->assertTrue($this->_validator->isValid(''));
    }
}
