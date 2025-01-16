<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\NisValidator;
use Tests\TestCase;

class NisValidatorTest extends TestCase
{
    public function test_nis_without_repeated_characters()
    {
        $validator = new NisValidator('12345678901');

        $this->assertTrue($validator->isValid());
    }

    public function test_nis_with_all_characters_zero()
    {
        $validator = new NisValidator('00000000000');

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('Os números do campo: NIS (PIS/PASEP) não podem ser todos zeros.', $validator->getMessage());
    }

    public function test_nis_with_all_characters_one()
    {
        $validator = new NisValidator('11111111111');

        $this->assertTrue($validator->isValid());
    }
}
