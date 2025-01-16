<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\Telefone;
use Tests\TestCase;

class TelefoneTest extends TestCase
{
    public function test_quantidade_de_digitos()
    {
        $telefoneValidator = new Telefone(null, '123');
        $this->assertFalse($telefoneValidator->isValid());

        $telefoneValidator = new Telefone(null, '1234567890');
        $this->assertFalse($telefoneValidator->isValid());

        $telefoneValidator = new Telefone(null, '912345678');
        $this->assertTrue($telefoneValidator->isValid());
    }

    public function test_primeiro_digito()
    {
        $telefoneValidator = new Telefone(null, '012345678');
        $this->assertFalse($telefoneValidator->isValid());

        $telefoneValidator = new Telefone(null, '912345678');
        $this->assertTrue($telefoneValidator->isValid());

        $telefoneValidator = new Telefone(null, '12345678');
        $this->assertTrue($telefoneValidator->isValid());
    }

    public function test_digitos_sequenciais()
    {
        $telefoneValidator = new Telefone(null, '11111111');
        $this->assertFalse($telefoneValidator->isValid());

        $telefoneValidator = new Telefone(null, '12121212');
        $this->assertTrue($telefoneValidator->isValid());
    }

    public function test_retorna_nome_campo()
    {
        $nomeCampo = 'nomeTeste';
        $telefoneValidator = new Telefone($nomeCampo, '11111111');
        $telefoneValidator->isValid();

        $this->assertStringContainsString($nomeCampo, implode(' ', $telefoneValidator->getMessage()));
    }
}
