<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEvaluationRuleGradeYear;
use Tests\EloquentTestCase;

class LegacyEvaluationRuleGradeYearTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        $this->markTestSkipped();
        return LegacyEvaluationRuleGradeYear::class;
    }
}
