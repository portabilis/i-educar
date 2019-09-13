<?php

namespace Tests\Unit\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\NisValidator;
use Tests\TestCase;

class NisValidatorTest extends TestCase
{
    public function testNisWithoutRepeatedCharacters()
    {
        $validator = new NisValidator('12345678901');

        $this->assertTrue($validator->isValid());
    }

    public function testNisWithAllCharactersZero()
    {
        $validator = new NisValidator('00000000000');

        $this->assertFalse($validator->isValid());
        $this->assertContains('Os números do campo: NIS (PIS/PASEP) não podem ser todos zeros.', $validator->getMessage());
    }

    public function testNisWithAllCharactersOne()
    {
        $validator = new NisValidator('11111111111');

        $this->assertTrue($validator->isValid());
    }
}
