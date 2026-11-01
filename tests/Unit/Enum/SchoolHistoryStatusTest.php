<?php

use App\Models\SchoolHistoryStatus;
use Tests\TestCase;

uses(TestCase::class);

test('descriptive values', function () {
    $values = (new SchoolHistoryStatus)->getDescriptiveValues();
    $this->assertIsArray($values);
    $this->assertJsonStringEqualsJsonString(collect([
        1 => 'Apro',
        2 => 'Repr',
        3 => 'Curs',
        4 => 'Tran',
        5 => 'Recl',
        6 => 'DeFr',
        12 => 'AprDep',
        13 => 'AprCo',
        14 => 'RpFt',
    ]), collect($values));
});
