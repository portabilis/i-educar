<?php

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyRoundingTable;
use App\Models\LegacyValueRoundingTable;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyRoundingTable::class, relations: [
    'roundingValues' => LegacyValueRoundingTable::class,
    'evaluationRules' => LegacyEvaluationRule::class,
]));

EloquentModelTestCase::registerModelTests();
