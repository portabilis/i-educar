<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\BirthDateValidator;
use Tests\TestCase;

class BirthDateValidatorTest extends TestCase
{
    public function test_birth_date_after_today()
    {
        $validator = new BirthDateValidator(date('Y-m-d', strtotime('+1 day')));

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('Informe uma data de nascimento menor que o dia de hoje.', $validator->getMessage());
    }

    public function test_birth_date_before_today()
    {
        $validator = new BirthDateValidator(date('Y-m-d', strtotime('-1 day')));

        $this->assertTrue($validator->isValid());
    }

    public function test_birth_date_is_today()
    {
        $validator = new BirthDateValidator(date('Y-m-d'));

        $this->assertTrue($validator->isValid());
    }
}
