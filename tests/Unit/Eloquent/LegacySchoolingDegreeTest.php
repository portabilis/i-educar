<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolingDegree;
use Tests\EloquentTestCase;

class LegacySchoolingDegreeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolingDegree::class;
    }
}
