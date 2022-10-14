<?php

namespace Tests\Unit\Eloquent;

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDeficiency;
use Tests\EloquentTestCase;

class LegacyDeficiencyTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDeficiency::class;
    }
}
