<?php

use App\Services\EducacensoReferenceDateService;
use PHPUnit\Framework\TestCase;

uses(TestCase::class);

test('calcula a última quarta-feira de maio do ano informado', function (int $year, string $expected) {
    expect((new EducacensoReferenceDateService)->forYear($year)->format('d/m/Y'))->toBe($expected);
})->with([
    [2023, '31/05/2023'],
    [2024, '29/05/2024'],
    [2025, '28/05/2025'],
    [2026, '27/05/2026'],
    [2027, '26/05/2027'],
]);

test('preenche com a data calculada quando a data está vazia', function (?string $date) {
    expect((new EducacensoReferenceDateService)->fill($date, 2026))->toBe('27/05/2026');
})->with([null, '']);

test('recalcula quando a data é de um ano anterior', function (string $date) {
    expect((new EducacensoReferenceDateService)->fill($date, 2027))->toBe('26/05/2027');
})->with(['2026-05-27', '27/05/2026']);

test('mantém a data informada quando ela é do ano atual ou posterior', function (string $date) {
    expect((new EducacensoReferenceDateService)->fill($date, 2027))->toBe($date);
})->with(['2027-06-02', '02/06/2027', '2028-05-31']);
