<?php

use iEducar\Modules\Educacenso\Validator\BirthDateValidator;
use Tests\TestCase;

uses(TestCase::class);

test('birth date after today', function () {
    $validator = new BirthDateValidator(date('Y-m-d', strtotime('+1 day')));

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('Informe uma data de nascimento menor que o dia de hoje.', $validator->getMessage());
});

test('birth date before today', function () {
    $validator = new BirthDateValidator(date('Y-m-d', strtotime('-1 day')));

    $this->assertTrue($validator->isValid());
});

test('birth date is today', function () {
    $validator = new BirthDateValidator(date('Y-m-d'));

    $this->assertTrue($validator->isValid());
});
