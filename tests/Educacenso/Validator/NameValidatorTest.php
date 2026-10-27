<?php

use iEducar\Modules\Educacenso\Validator\NameValidator;
use Tests\TestCase;

uses(TestCase::class);

test('name without repeated characters', function () {
    $validator = new NameValidator('Lorem Ipsum');

    $this->assertTrue($validator->isValid());
});

test('name with three repeated characters', function () {
    $validator = new NameValidator('Lorem Ipsuuum');

    $this->assertTrue($validator->isValid());
});

test('name with four repeated characters', function () {
    $validator = new NameValidator('Lorem Ipsuuuum');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('Nome não pode ter a repetição de 4 caracteres seguidos.', $validator->getMessage());
});

test('name with maximum 100 characters is valid', function () {
    $name = str_repeat('Ab', 50);
    $validator = new NameValidator($name);

    $this->assertTrue($validator->isValid());
});

test('name longer than 100 characters is invalid', function () {
    $name = str_repeat('AB', 51);
    $validator = new NameValidator($name);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('O Nome deve ter no máximo 100 caracteres', $validator->getMessage());
});
