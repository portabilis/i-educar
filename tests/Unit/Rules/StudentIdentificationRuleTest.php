<?php

use App\Rules\StudentIdentificationRule;

// ciclo 1: Ausência dos dois identificadores
test('falha na validacao quando CPF e INEP estao ausentes', function () {
    $data = [
        'cpf' => null,
        'inep' => null,
    ];

    $regra = new StudentIdentificationRule();
    $regra->setData($data);

    expect($regra->passes('cpf', $data['cpf']))->toBeFalse();
});

// ciclo 2: CPF ausente e INEP preenchido
test('passa na validacao quando CPF esta ausente mas INEP esta preenchido', function () {
    $data = [
        'cpf' => null,
        'inep' => '123456789012',
    ];

    $regra = new StudentIdentificationRule();
    $regra->setData($data);

    expect($regra->passes('cpf', $data['cpf']))->toBeTrue();
});