<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyAverageFormula;
use Tests\EloquentTestCase;

class LegacyAverageFormulaTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyAverageFormula::class;
    }
}
