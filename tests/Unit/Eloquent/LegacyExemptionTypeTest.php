<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyExemptionType;
use Tests\EloquentTestCase;

class LegacyExemptionTypeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyExemptionType::class;
    }
}
