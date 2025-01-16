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
}
