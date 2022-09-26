<?php

namespace Tests\Unit\Eloquent;

use App\Models\Religion;
use Tests\EloquentTestCase;

class ReligionTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Religion::class;
    }
}
