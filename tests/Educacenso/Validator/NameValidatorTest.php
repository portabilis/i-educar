<?php

namespace Tests\Educacenso\Validator;

use iEducar\Modules\Educacenso\Validator\NameValidator;
use Tests\TestCase;

class NameValidatorTest extends TestCase
{
    public function test_name_without_repeated_characters()
    {
        $validator = new NameValidator('Lorem Ipsum');

        $this->assertTrue($validator->isValid());
    }

    public function test_name_with_three_repeated_characters()
    {
        $validator = new NameValidator('Lorem Ipsuuum');

        $this->assertTrue($validator->isValid());
    }

    public function test_name_with_four_repeated_characters()
    {
        $validator = new NameValidator('Lorem Ipsuuuum');

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('Nome não pode ter a repetição de 4 caracteres seguidos.', $validator->getMessage());
    }

    public function test_name_with_maximum_100_characters_is_valid()
    {
        $name = str_repeat('Ab', 50);
        $validator = new NameValidator($name);

        $this->assertTrue($validator->isValid());
    }

    public function test_name_longer_than_100_characters_is_invalid()
    {
        $name = str_repeat('AB', 51);
        $validator = new NameValidator($name);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('O Nome deve ter no máximo 100 caracteres', $validator->getMessage());
    }
}
