<?php

namespace Tests\Unit\Eloquent;

use App\Models\AverageFormula;
use Tests\EloquentTestCase;

class AverageFormulaTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return AverageFormula::class;
    }
}
