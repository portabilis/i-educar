<?php

namespace Tests\Unit\Eloquent;

use App\Models\ReleasePeriod;
use App\Models\ReleasePeriodDate;
use Tests\EloquentTestCase;

class ReleasePeriodDateTest extends EloquentTestCase
{
    protected $relations = [
        'releasePeriod' => ReleasePeriod::class,
    ];

    protected function getEloquentModelName(): string
    {
        return ReleasePeriodDate::class;
    }
}
