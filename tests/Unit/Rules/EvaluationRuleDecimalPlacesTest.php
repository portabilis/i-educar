<?php

use App\Rules\EvaluationDecimalPlacesRule;

test('permite valores inteiros entre 0 e 4 casas decimais', function (mixed $value) {
    $rule = new EvaluationDecimalPlacesRule;

    expect((bool) $rule->passes('qtd_casas_decimais', $value))->toBeTrue();
})->with([
    'limite inferior (0)' => 0,
    'limite inferior como string ("0")' => '0',
    'valor intermediário (1)' => 1,
    'valor padrão comum (2)' => 2,
    'valor intermediário (3)' => 3,
    'limite superior (4)' => 4,
    'limite superior como string ("4")' => '4',
]);

test('rejeita valores negativos, acima de 4 e valores absurdos', function (mixed $value) {
    $rule = new EvaluationDecimalPlacesRule;

    expect((bool) $rule->passes('qtd_casas_decimais', $value))->toBeFalse();
})->with([
    'negativo limite imediato (-1)' => -1,
    'negativo limite imediato como string ("-1")' => '-1',
    'negativo extremo (-10)' => -10,
    'acima do limite superior imediato (5)' => 5,
    'acima do limite superior imediato como string ("5")' => '5',
    'valor absurdo (999)' => 999,
    'valor absurdo como string ("999")' => '999',
    'valor nulo' => null,
    'string não numérica' => 'abc',
    'número de ponto flutuante (2.5)' => 2.5,
]);

test('retorna a mensagem de erro correta quando a validação falha', function () {
    $rule = new EvaluationDecimalPlacesRule;

    expect($rule->message())->toBe('A quantidade de casas decimais deve ser um número inteiro entre 0 e 4.');
});
