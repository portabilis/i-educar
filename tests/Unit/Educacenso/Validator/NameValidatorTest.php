<?php

namespace Tests\Unit\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\NameValidator;
use Tests\TestCase;

class NameValidatorTest extends TestCase
{
    public function testNameWithoutRepeatedCharacters()
    {
        $validator = new NameValidator('Lorem Ipsum');

        $this->assertTrue($validator->isValid());
    }

    public function testNameWithThreeRepeatedCharacters()
    {
        $validator = new NameValidator('Lorem Ipsuuum');

        $this->assertTrue($validator->isValid());
    }

    public function testNameWithFourRepeatedCharacters()
    {
        $validator = new NameValidator('Lorem Ipsuuuum');

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('Nome não pode ter a repetição de 4 caracteres seguidos.', $validator->getMessage());
    }
}
