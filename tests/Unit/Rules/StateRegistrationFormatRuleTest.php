<?php

use App\Rules\StateRegistrationFormatRule;

/*
|--------------------------------------------------------------------------
| Testes Caixa-Preta: StateRegistrationFormatRule
|--------------------------------------------------------------------------
| Técnicas aplicadas:
| 1. Particionamento em Classes de Equivalência (Valores Válidos, Inválidos e Vazios)
| 2. Análise do Valor Limite (BVA - Limites de dígitos ASCII, blocos e DV)
| 3. Teste de Robustez (Tipos primitivos escalares e não-escalares)
*/

// =========================================================================
// TESTE 1: Formatos válidos (com e sem dígito verificador) + Limites Válidos
// =========================================================================
test('Teste 1: valida formatos válidos com e sem dígito verificador', function (string $value) {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration_id', $value))->toBeTrue();
})->with([
    // Partição sem DV (máscara 999.999.999 - 11 caracteres)
    'sem DV padrão' => '123.456.789',
    'sem DV repetidos' => '111.222.333',

    // BVA: Limite inferior dos dígitos numéricos ('0' - ASCII 48)
    'limite inferior numérico (apenas zeros) sem DV' => '000.000.000',

    // BVA: Limite superior dos dígitos numéricos ('9' - ASCII 57)
    'limite superior numérico (apenas noves) sem DV' => '999.999.999',

    // Partição com DV (máscara 999.999.999-9 - 13 caracteres)
    'com DV padrão intermediário' => '123.456.789-5',
    'com DV alternado' => '111.222.333-4',

    // BVA: Limite inferior do dígito verificador ('0')
    'com DV no limite inferior zero' => '123.456.789-0',
    'com DV zero e corpo de zeros' => '000.000.000-0',

    // BVA: Limite superior do dígito verificador ('9')
    'com DV no limite superior nove' => '123.456.789-9',
    'com DV nove e corpo de noves' => '999.999.999-9',
]);

// =========================================================================
// TESTE 2: Formatos inválidos (sem pontos, letras, etc.) + Limites Inválidos
// =========================================================================
test('Teste 2: rejeita formatos inválidos sem pontos, com letras e separadores incorretos', function (string $value) {
    $rule = new StateRegistrationFormatRule;

    expect((bool) $rule->passes('state_registration_id', $value))->toBeFalse();
})->with([
    // Partição: Sem pontos (apenas números)
    'sem pontos com 9 dígitos' => '123456789',
    'sem pontos com 10 dígitos' => '1234567890',
    'sem pontos com hífen' => '123456789-0',

    // Partição: Presença de letras
    'apenas letras com pontos' => 'abc.def.ghi',
    'letras no primeiro bloco' => '12a.456.789',
    'letras no segundo bloco' => '123.45a.789',
    'letras no terceiro bloco' => '123.456.78a',
    'apenas texto alfabético' => 'invalido',

    // BVA: Caracteres imediatamente fora do intervalo numérico '0'-'9' (ASCII)
    'caractere logo abaixo de 0 (barra ASCII 47)' => '//0.000.000',
    'caractere logo acima de 9 (dois pontos ASCII 58)' => '::0.000.000',
    'caractere especial @ no bloco' => '12@.456.789',

    // BVA: Limites inferiores de quantidade de dígitos por bloco (2 dígitos em vez de 3)
    'limite inferior no bloco 1 (2 dígitos)' => '12.345.678',
    'limite inferior no bloco 2 (2 dígitos)' => '123.45.678',
    'limite inferior no bloco 3 (2 dígitos)' => '123.456.78',
    'bloco com apenas 1 dígito' => '1.2.3',

    // Partição: Separadores incorretos
    'separador hífen em vez de ponto' => '123-456-789',
    'separador barra em vez de ponto' => '123/456/789',
    'separador espaço em vez de ponto' => '123 456 789',
    'caracteres especiais diversos' => '!@#.$%^.&*()',
]);

// =========================================================================
// TESTE 3: Entradas vazias e nulas
// =========================================================================
test('Teste 3: rejeita entradas vazias ou nulas', function ($value) {
    $rule = new StateRegistrationFormatRule;

    expect((bool) @$rule->passes('state_registration_id', $value))->toBeFalse();
})->with([
    'string vazia (comprimento 0)' => '',
    'espaços em branco' => '   ',
    'espaço simples' => ' ',
    'tabs e quebras de linha' => "\t\n",
    'valor nulo (null)' => null,
]);

// =========================================================================
// TESTES ADICIONAIS DE CAIXA-PRETA: Robustez de Tipos Primitivos
// =========================================================================
test('Teste de Robustez: rejeita tipos escalares incompatíveis', function ($value) {
    $rule = new StateRegistrationFormatRule;

    expect((bool) @$rule->passes('state_registration_id', $value))->toBeFalse();
})->with([
    'número inteiro 9 dígitos' => 123456789,
    'número inteiro zero' => 0,
    'número float' => 123.456,
    'booleano true' => true,
    'booleano false' => false,
]);

test('Teste de Robustez: entrada não-escalar lança TypeError por falta de verificação de tipo', function () {
    $rule = new StateRegistrationFormatRule;

    expect(fn () => $rule->passes('state_registration_id', []))->toThrow(TypeError::class);
});

// =========================================================================
// Mensagem de validação
// =========================================================================
test('retorna a mensagem de erro correta quando a validação falha', function () {
    $rule = new StateRegistrationFormatRule;

    expect($rule->message())->toBe('O número de inscrição é inválido.');
});
