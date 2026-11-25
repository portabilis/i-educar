<?php

use App\Rules\StateRegistrationFormatRule;

test('aceita a inscrição no formato 000.000.000 com ou sem dígito', function (string $value) {
    expect((new StateRegistrationFormatRule)->passes('inscricao', $value))->toBeTruthy();
})->with([
    '123.456.789',
    '123.456.789-0',
]);

test('recusa a inscrição fora do formato', function (string $value) {
    expect((new StateRegistrationFormatRule)->passes('inscricao', $value))->toBeFalsy();
})->with([
    '',
    '123456789',
    '12.345.678-9',
    '123.456.78',
    'abc.def.ghi',
    'abc123.456.789xyz',
    '123.456.789-01',
]);

test('explica que a inscrição é inválida', function () {
    expect((new StateRegistrationFormatRule)->message())->toBe('O número de inscrição é inválido.');
});
