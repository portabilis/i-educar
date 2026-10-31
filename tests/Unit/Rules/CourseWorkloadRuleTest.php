<?php

use App\Rules\CourseWorkloadRule;

test('permite carga horária com valores numéricos positivos maiores que zero', function (mixed $value) {
    $rule = new CourseWorkloadRule;

    expect((bool) $rule->passes('carga_horaria', $value))->toBeTrue();
})->with([
    'valor positivo comum especificado (800)' => 800,
    'valor positivo como string ("800")' => '800',
    'limite inferior positivo inteiro (1)' => 1,
    'limite inferior positivo decimal (0.5)' => 0.5,
    'carga horária extensa (1200)' => 1200,
    'carga horária extensa decimal (800.5)' => 800.5,
]);

test('rejeita carga horária igual a zero, negativa, nula ou não numérica', function (mixed $value) {
    $rule = new CourseWorkloadRule;

    expect((bool) $rule->passes('carga_horaria', $value))->toBeFalse();
})->with([
    'carga horária zero (0)' => 0,
    'carga horária zero como string ("0")' => '0',
    'carga horária negativa especificada (-10)' => -10,
    'carga horária negativa como string ("-10")' => '-10',
    'carga horária limite negativo (-1)' => -1,
    'carga horária nula (null)' => null,
    'string vazia' => '',
    'apenas espaços em branco' => '   ',
    'texto não numérico ("abc")' => 'abc',
    'booleano true' => true,
    'booleano false' => false,
]);

test('retorna a mensagem de erro correta quando a carga horária for inválida', function () {
    $rule = new CourseWorkloadRule;

    expect($rule->message())->toBe('A carga horária do curso deve ser um valor numérico maior que zero.');
});
