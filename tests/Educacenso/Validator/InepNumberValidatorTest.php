<?php

use iEducar\Modules\Educacenso\Validator\InepNumberValidator;
use Tests\TestCase;

uses(TestCase::class);

test('o inep vazio ou com doze digitos e valido', function () {
    $this->assertTrue((new InepNumberValidator(null))->isValid());
    $this->assertTrue((new InepNumberValidator('123456789012'))->isValid());
});

test('o inep com outro tamanho e invalido', function () {
    $validator = new InepNumberValidator('12345678');

    $this->assertFalse($validator->isValid());
    $this->assertSame('Verifique se o INEP possui 12 dígitos', $validator->getMessage());
});
