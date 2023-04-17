<?php

namespace Tests\Unit\Eloquent;

use App\Models\ReportsCount;
use Tests\EloquentTestCase;

class ReportsCountTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return ReportsCount::class;
    }
}
