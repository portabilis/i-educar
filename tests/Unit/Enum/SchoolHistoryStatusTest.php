<?php

namespace Tests\Unit\Enum;

use App\Models\SchoolHistoryStatus;
use Tests\EnumTestCase;

class SchoolHistoryStatusTest extends EnumTestCase
{
    public function getDescriptiveValues(): array
    {
        return [
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
    }

    protected function getEnumName(): string
    {
        return SchoolHistoryStatus::class;
    }
}
