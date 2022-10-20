<?php

namespace Tests\Unit\Enum;

use App\Models\SchoolHistoryStatus;
use Tests\TestCase;

class SchoolHistoryStatusTest extends TestCase
{
    public function testDescriptiveValues(): void
    {
        $values = (new SchoolHistoryStatus())->getDescriptiveValues();
        $this->assertIsArray($values);
        $except = [
            1 => 'Apro',
            2 => 'Repr',
            3 => 'Curs',
            4 => 'Tran',
            5 => 'Recl',
            6 => 'Aban',
            12 => 'AprDep',
            13 => 'AprCo',
            14 => 'RpFt',
        ];
        $this->assertJsonStringEqualsJsonString(collect($except), collect($values));
    }
}
