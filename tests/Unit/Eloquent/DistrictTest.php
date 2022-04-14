<?php

namespace Tests\Unit\Eloquent;

use App\Models\District;
use Tests\EloquentTestCase;

class DistrictTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return District::class;
    }
}
