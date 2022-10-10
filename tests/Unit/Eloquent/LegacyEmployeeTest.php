<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEmployee;
use Tests\EloquentTestCase;

class LegacyEmployeeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEmployee::class;
    }
}
