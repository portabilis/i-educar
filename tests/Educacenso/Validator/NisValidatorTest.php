<?php

use iEducar\Modules\Educacenso\Validator\NisValidator;
use Tests\TestCase;

uses(TestCase::class);

test('nis without repeated characters', function () {
    $validator = new NisValidator('12345678901');

    $this->assertTrue($validator->isValid());
});

test('nis with all characters zero', function () {
    $validator = new NisValidator('00000000000');

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('Os números do campo: NIS (PIS/PASEP) não podem ser todos zeros.', $validator->getMessage());
});

test('nis with all characters one', function () {
    $validator = new NisValidator('11111111111');

    $this->assertTrue($validator->isValid());
});
