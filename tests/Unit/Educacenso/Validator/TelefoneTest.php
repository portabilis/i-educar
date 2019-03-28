<?php

namespace Tests\Unit\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\Telefone;
use Tests\TestCase;

class TelefoneTest extends TestCase
{
    public function testQuantidadeDeDigitos()
    {
        $telefoneValidator = new Telefone(null, '123');
        $this->assertFalse($telefoneValidator->isValid());

        $telefoneValidator = new Telefone(null, '1234567890');
        $this->assertFalse($telefoneValidator->isValid());

        $telefoneValidator = new Telefone(null, '912345678');
        $this->assertTrue($telefoneValidator->isValid());
    }

    public function testPrimeiroDigito()
    {
        $telefoneValidator = new Telefone(null, '012345678');
        $this->assertFalse($telefoneValidator->isValid());

        $telefoneValidator = new Telefone(null, '912345678');
        $this->assertTrue($telefoneValidator->isValid());

        $telefoneValidator = new Telefone(null, '12345678');
        $this->assertTrue($telefoneValidator->isValid());
    }

    public function testDigitosSequenciais()
    {
        $telefoneValidator = new Telefone(null, '11111111');
        $this->assertFalse($telefoneValidator->isValid());

        $telefoneValidator = new Telefone(null, '12121212');
        $this->assertTrue($telefoneValidator->isValid());
    }

    public function testRetornaNomeCampo()
    {
        $nomeCampo = 'nomeTeste';
        $telefoneValidator = new Telefone($nomeCampo, '11111111');
        $telefoneValidator->isValid();

        $this->assertContains($nomeCampo, implode(' ', $telefoneValidator->getMessage()));
    }
}
