<?php

namespace App\Models;

use App\Contracts\Enum;

class SchoolHistoryStatus implements Enum
{
    const APPROVED = 1;
    const REPROVED = 2;
    const ONGOING = 3;
    const TRANSFERRED = 4;
    const RECLASSIFIED = 5;
    const ABANDONED = 6;
    const APPROVED_WITH_DEPENDENCY = 12;
    const APPROVED_BY_BOARD = 13;
    const REPROVED_BY_ABSENCE = 14;

    public function getDescriptiveValues(): array
    {
        return [
            self::APPROVED => 'Apro',
            self::REPROVED => 'Repr',
            self::ONGOING => 'Curs',
            self::TRANSFERRED => 'Tran',
            self::RECLASSIFIED => 'Recl',
            self::ABANDONED => 'Aban',
            self::APPROVED_WITH_DEPENDENCY => 'AprDep',
            self::APPROVED_BY_BOARD => 'AprCo',
            self::REPROVED_BY_ABSENCE => 'RpFt',
        ];
    }
}
