<?php

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyEvaluationRuleGradeYear;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyEvaluationRuleGradeYear::class, relations: [
    'evaluationRule' => LegacyEvaluationRule::class,
    'differentiatedEvaluationRule' => LegacyEvaluationRule::class,
]));

EloquentModelTestCase::registerModelTests();
