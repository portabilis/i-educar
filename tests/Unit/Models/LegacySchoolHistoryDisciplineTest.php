<?php

namespace Tests\Unit\Models;

use App\Models\LegacySchoolHistoryDiscipline;
use Tests\TestCase;

uses(TestCase::class);

function disciplineWithScore(?string $score): LegacySchoolHistoryDiscipline
{
    $discipline = new LegacySchoolHistoryDiscipline;
    $discipline->nota = $score;

    return $discipline;
}

test('nota inteira de tres digitos mantem todos os digitos', function () {
    expect(disciplineWithScore('100')->scoreNotRounding(1))->toBe('100');
});

test('nota inteira nao recebe casa decimal', function (string $score, string $expected) {
    expect(disciplineWithScore($score)->scoreNotRounding(1))->toBe($expected);
})->with([
    ['0', '0'],
    ['9', '9'],
    ['85', '85'],
    ['100', '100'],
    ['1000', '1000'],
    ['85,0', '85'],
    ['100,0', '100'],
]);

test('nota com decimal e truncada sem arredondar', function (string $score, int $decimalPlaces, string $expected) {
    expect(disciplineWithScore($score)->scoreNotRounding($decimalPlaces))->toBe($expected);
})->with([
    ['85,5', 1, '85,5'],
    ['7,55', 1, '7,5'],
    ['7,59', 1, '7,5'],
    ['7,55', 2, '7,55'],
    ['99,99', 1, '99,9'],
]);

test('sem casas decimais a parte fracionaria e descartada', function (string $score, string $expected) {
    expect(disciplineWithScore($score)->scoreNotRounding(0))->toBe($expected);
})->with([
    ['100', '100'],
    ['100,9', '100'],
    ['7,5', '7'],
]);

test('nota com espaco em volta e formatada normalmente', function (string $score, string $expected) {
    expect(disciplineWithScore($score)->scoreNotRounding(1))->toBe($expected);
})->with([
    [' 85', '85'],
    ['85 ', '85'],
    [' 100 ', '100'],
    [' 7,5 ', '7,5'],
]);

test('zero no meio do decimal e preservado', function () {
    expect(disciplineWithScore('10,05')->scoreNotRounding(2))->toBe('10,05');
});

test('zero a direita do decimal e removido', function () {
    expect(disciplineWithScore('7,50')->scoreNotRounding(2))->toBe('7,5');
});

test('nota negativa mantem o sinal', function () {
    expect(disciplineWithScore('-7,5')->scoreNotRounding(1))->toBe('-7,5');
});

test('nota gravada com ponto e formatada como as demais', function (string $score, int $decimalPlaces, string $expected) {
    expect(disciplineWithScore($score)->scoreNotRounding($decimalPlaces))->toBe($expected);
})->with([
    ['7.5', 1, '7,5'],
    ['100.00', 2, '100'],
    ['85.0', 1, '85'],
]);

test('nota fora do formato canonico e normalizada', function (string $score, string $expected) {
    expect(disciplineWithScore($score)->scoreNotRounding(1))->toBe($expected);
})->with([
    ['+85', '85'],
    [',5', '0,5'],
    ['7,', '7'],
    ['007', '7'],
]);

test('nota nao numerica e devolvida sem alteracao', function (string $score, string $expected) {
    expect(disciplineWithScore($score)->scoreNotRounding(1))->toBe($expected);
})->with([
    ['MB', 'MB'],
    ['1e3', '1e3'],
    ['8a', '8a'],
    ['DS,0', 'DS,0'],
    ['´7,5', '´7,5'],
    ['90%,0', '90%,0'],
]);

test('score usa o mesmo criterio de nota numerica', function (string $score, ?string $expected) {
    expect(disciplineWithScore($score)->score(1))->toBe($expected);
})->with([
    ['100', '100,0'],
    ['85', '85,0'],
    ['7,5', '7,5'],
    [' 85 ', '85,0'],
    ['1e3', '1e3'],
    ['DS,0', 'DS,0'],
    ['   ', null],
]);

test('ano digitado no campo de nota nao e alterado', function () {
    expect(disciplineWithScore('2019')->scoreNotRounding(1))->toBe('2019');
});

test('nota vazia devolve null', function (?string $score) {
    expect(disciplineWithScore($score)->scoreNotRounding(1))->toBeNull();
})->with([
    [null],
    [''],
    ['   '],
]);
