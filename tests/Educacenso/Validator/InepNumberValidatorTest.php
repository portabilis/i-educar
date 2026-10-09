<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\InepNumberValidator;
use Tests\TestCase;

class InepNumberValidatorTest extends TestCase
{
    public function test_o_inep_vazio_ou_com_doze_digitos_e_valido(): void
    {
        $this->assertTrue((new InepNumberValidator(null))->isValid());
        $this->assertTrue((new InepNumberValidator('123456789012'))->isValid());
    }

    public function test_o_inep_com_outro_tamanho_e_invalido(): void
    {
        $validator = new InepNumberValidator('12345678');

        $this->assertFalse($validator->isValid());
        $this->assertSame('Verifique se o INEP possui 12 dígitos', $validator->getMessage());
    }
}
