<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDeficiency;
use Tests\EloquentTestCase;

class DeficiencyTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDeficiency::class;
    }
}
