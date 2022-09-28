<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineExemption;
use Tests\EloquentTestCase;

class LegacyDisciplineExemptionTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplineExemption::class;
    }
}
