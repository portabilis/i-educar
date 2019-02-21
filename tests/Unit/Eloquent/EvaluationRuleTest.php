<?php

namespace Tests\Unit\Eloquent;

use App\Models\EvaluationRule;
use Tests\EloquentTestCase;

class EvaluationRuleTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EvaluationRule::class;
    }
}
