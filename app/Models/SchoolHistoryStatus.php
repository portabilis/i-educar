<?php

namespace App\Models;

use App\Contracts\Enum;

class SchoolHistoryStatus implements Enum
{
    public const APPROVED = 1;
    public const REPROVED = 2;
    public const ONGOING = 3;
    public const TRANSFERRED = 4;
    public const RECLASSIFIED = 5;
    public const ABANDONED = 6;
    public const APPROVED_WITH_DEPENDENCY = 12;
    public const APPROVED_BY_BOARD = 13;
    public const REPROVED_BY_ABSENCE = 14;

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
