<?php

use App\Rules\PhoneFormatRule;

test('aceita telefone fixo e celular, com ou sem máscara', function (string $telefone) {
    expect((new PhoneFormatRule)->passes('phone', $telefone))->toBeTrue();
})->with([
    'fixo com máscara' => ['(11) 3456-7890'],
    'celular com máscara' => ['(11) 98765-4321'],
    'fixo sem máscara' => ['1134567890'],
    'celular sem máscara' => ['11987654321'],
    'fixo com máscara parcial' => ['11 3456-7890'],
    'fixo sem espaço após o DDD' => ['(61)3345-6789'],
    'celular com pontos como separador' => ['11.98765.4321'],
]);

test('rejeita telefone incompleto ou longo demais', function (string $telefone) {
    expect((new PhoneFormatRule)->passes('phone', $telefone))->toBeFalse();
})->with([
    'exemplo da issue #1075' => ['(11) 123'],
    'somente o DDD' => ['(11)'],
    'fixo sem DDD (8 dígitos)' => ['3456-7890'],
    'celular sem DDD (9 dígitos)' => ['98765-4321'],
    'um dígito a menos que o fixo (9 dígitos)' => ['(11) 3456-789'],
    'um dígito a mais que o celular (12 dígitos)' => ['(11) 98765-43210'],
    'doze dígitos sem máscara' => ['119876543210'],
    'valor vazio' => [''],
]);

test('rejeita caracteres que não fazem parte da máscara', function (string $telefone) {
    expect((new PhoneFormatRule)->passes('phone', $telefone))->toBeFalse();
})->with([
    'letras no lugar de dígitos' => ['abcdefghij'],
    'letra no meio do número' => ['(11) 3456-78a0'],
    'dígitos suficientes com sinal de mais' => ['+11 3456-7890'],
    'dígitos suficientes com barra' => ['11/3456-7890'],
]);

test('rejeita valor que não é texto nem número', function (mixed $valor) {
    expect((new PhoneFormatRule)->passes('phone', $valor))->toBeFalse();
})->with([
    'nulo' => [null],
    'lista' => [[]],
    'booleano' => [true],
]);

test('aceita telefone informado como número inteiro', function () {
    expect((new PhoneFormatRule)->passes('phone', 1134567890))->toBeTrue();
});

test('a mensagem explica o formato esperado', function () {
    expect((new PhoneFormatRule)->message())
        ->toBe('O telefone deve ter DDD e 8 ou 9 dígitos, por exemplo (11) 3456-7890 ou (11) 98765-4321.');
});
