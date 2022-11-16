<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyEvaluationRuleGradeYear;
use Tests\EloquentTestCase;

class LegacyEvaluationRuleGradeYearTest extends EloquentTestCase
{
    protected $relations = [
        'evaluationRule' => LegacyEvaluationRule::class,
        'differentiatedEvaluationRule' => LegacyEvaluationRule::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEvaluationRuleGradeYear::class;
    }
}
