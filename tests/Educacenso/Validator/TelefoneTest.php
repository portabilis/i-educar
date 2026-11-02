<?php

use iEducar\Modules\Educacenso\Validator\Telefone;
use Tests\TestCase;

uses(TestCase::class);

test('quantidade de digitos', function () {
    $telefoneValidator = new Telefone(null, '123');
    $this->assertFalse($telefoneValidator->isValid());

    $telefoneValidator = new Telefone(null, '1234567890');
    $this->assertFalse($telefoneValidator->isValid());

    $telefoneValidator = new Telefone(null, '912345678');
    $this->assertTrue($telefoneValidator->isValid());
});

test('primeiro digito', function () {
    $telefoneValidator = new Telefone(null, '012345678');
    $this->assertFalse($telefoneValidator->isValid());

    $telefoneValidator = new Telefone(null, '912345678');
    $this->assertTrue($telefoneValidator->isValid());

    $telefoneValidator = new Telefone(null, '12345678');
    $this->assertTrue($telefoneValidator->isValid());
});

test('digitos sequenciais', function () {
    $telefoneValidator = new Telefone(null, '11111111');
    $this->assertFalse($telefoneValidator->isValid());

    $telefoneValidator = new Telefone(null, '12121212');
    $this->assertTrue($telefoneValidator->isValid());
});

test('retorna nome campo', function () {
    $nomeCampo = 'nomeTeste';
    $telefoneValidator = new Telefone($nomeCampo, '11111111');
    $telefoneValidator->isValid();

    $this->assertStringContainsString($nomeCampo, implode(' ', $telefoneValidator->getMessage()));
});
