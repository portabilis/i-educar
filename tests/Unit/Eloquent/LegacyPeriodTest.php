<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyPeriod;
use Tests\EloquentTestCase;

class LegacyPeriodTest extends EloquentTestCase
{
    protected function getEloquentModelName()
    {
        return LegacyPeriod::class;
    }
}
