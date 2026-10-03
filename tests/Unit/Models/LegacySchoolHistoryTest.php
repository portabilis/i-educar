<?php

namespace Tests\Unit\Models;

use App\Models\LegacySchoolHistory;
use Tests\TestCase;

uses(TestCase::class);

function historyWithGeneralAbsences(mixed $absences): LegacySchoolHistory
{
    $history = new LegacySchoolHistory;
    $history->faltas_globalizadas = $absences;

    return $history;
}

test('faltas globalizadas inteiras sao mantidas', function (mixed $absences, int $expected) {
    expect(historyWithGeneralAbsences($absences)->faltas_globalizadas)->toBe($expected);
})->with([
    ['0', 0],
    ['40', 40],
    [40, 40],
]);

test('faltas globalizadas com decimal sao arredondadas', function (string $absences, int $expected) {
    expect(historyWithGeneralAbsences($absences)->faltas_globalizadas)->toBe($expected);
})->with([
    ['40.0', 40],
    ['40.4', 40],
    ['40.5', 41],
    ['40.6', 41],
]);

test('faltas globalizadas nao numericas devolvem null', function (mixed $absences) {
    expect(historyWithGeneralAbsences($absences)->faltas_globalizadas)->toBeNull();
})->with([
    [null],
    [''],
    ['abc'],
]);
