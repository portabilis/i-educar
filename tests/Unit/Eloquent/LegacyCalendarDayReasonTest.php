<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCalendarDayReason;
use App\Models\LegacySchool;
use Tests\EloquentTestCase;

class LegacyCalendarDayReasonTest extends EloquentTestCase
{
    public $relationships = [
        'school' => LegacySchool::class,
    ];

    protected function getEloquentModelName()
    {
        return LegacyCalendarDayReason::class;
    }
}
