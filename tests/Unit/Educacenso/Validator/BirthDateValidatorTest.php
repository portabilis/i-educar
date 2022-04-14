<?php

namespace Tests\Unit\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\BirthDateValidator;
use Tests\TestCase;

class BirthDateValidatorTest extends TestCase
{
    public function testBirthDateAfterToday()
    {
        $validator = new BirthDateValidator(date('Y-m-d', strtotime('+1 day')));

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('Informe uma data de nascimento menor que o dia de hoje.', $validator->getMessage());
    }

    public function testBirthDateBeforeToday()
    {
        $validator = new BirthDateValidator(date('Y-m-d', strtotime('-1 day')));

        $this->assertTrue($validator->isValid());
    }

    public function testBirthDateIsToday()
    {
        $validator = new BirthDateValidator(date('Y-m-d'));

        $this->assertTrue($validator->isValid());
    }
}
