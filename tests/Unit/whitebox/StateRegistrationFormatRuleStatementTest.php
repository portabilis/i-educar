<?php

use App\Rules\StateRegistrationFormatRule;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

uses(TestCase::class);

const STATE_REGISTRATION_MESSAGE = 'O número de inscrição é inválido.';

test('CT-A1 passes() executa o comando C1 e aceita o formato 000.000.000-0', function () {
    $rule = new StateRegistrationFormatRule;

    expect($rule->passes('state_registration_id', '123.456.789-0'))->toBeTruthy();
});

test('CT-A2 passes() executa o comando C1 e rejeita um valor fora do formato', function () {
    $rule = new StateRegistrationFormatRule;

    expect($rule->passes('state_registration_id', 'abc'))->toBeFalsy();
});

test('CT-A3 message() executa o comando C2', function () {
    $rule = new StateRegistrationFormatRule;

    expect($rule->message())->toBe(STATE_REGISTRATION_MESSAGE);
});

test('CT-A4 pelo Validator do Laravel, passes() e message() são executados em sequência', function () {
    $validator = Validator::make(
        ['state_registration_id' => 'abc'],
        ['state_registration_id' => [new StateRegistrationFormatRule]]
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('state_registration_id'))->toBe(STATE_REGISTRATION_MESSAGE);

    $aceito = Validator::make(
        ['state_registration_id' => '123.456.789'],
        ['state_registration_id' => [new StateRegistrationFormatRule]]
    );

    expect($aceito->passes())->toBeTrue();
});

test('CT-A5 passes() devolve int (1/0) e não bool, apesar do @return bool', function () {
    $rule = new StateRegistrationFormatRule;

    expect($rule->passes('state_registration_id', '123.456.789-0'))->toBe(1)
        ->and($rule->passes('state_registration_id', 'abc'))->toBe(0);
});


test('CT-A6 ACHADO: regex sem âncoras aceita valores com lixo ao redor da máscara', function (string $valor) {
    $rule = new StateRegistrationFormatRule;

    expect($rule->passes('state_registration_id', $valor))->toBeTruthy();
})->with([
    'prefixo com letras' => ['abc123.456.789'],
    'sufixo com letras' => ['123.456.789xyz'],
    'dígito verificador com 2 dígitos' => ['123.456.789-12'],
    'mais de 3 dígitos no último grupo' => ['123.456.7890'],
]);
