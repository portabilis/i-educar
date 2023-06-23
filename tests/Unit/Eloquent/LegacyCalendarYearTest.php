<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCalendarYear;
use Tests\EloquentTestCase;

class LegacyCalendarYearTest extends EloquentTestCase
{
    public $relationships = [
        'school' => LegacySchool::class,
    ];

    protected function getEloquentModelName()
    {
        return LegacyCalendarYear::class;
    }
}
