<?php

namespace Tests\Unit\Eloquent;

use App\Models\SchoolInep;
use Tests\EloquentTestCase;

class SchoolInepTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return SchoolInep::class;
    }
}
