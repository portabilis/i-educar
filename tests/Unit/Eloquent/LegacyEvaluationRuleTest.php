<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEvaluationRule;
use Tests\EloquentTestCase;

class LegacyEvaluationRuleTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEvaluationRule::class;
    }
}
