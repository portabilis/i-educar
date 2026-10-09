<?php

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyRemedialRule;
use App\Models\LegacyRoundingTable;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyEvaluationRule::class, relations: [
    'remedialRules' => LegacyRemedialRule::class,
    'roundingTable' => LegacyRoundingTable::class,
    'conceptualRoundingTable' => LegacyRoundingTable::class,
]));

EloquentModelTestCase::registerModelTests();

test('is global score rule', function () {
    if ($this->model->nota_geral_por_etapa == 1) {
        $this->assertEquals(true, $this->model->isGlobalScore());
    } else {
        $this->assertEquals(false, $this->model->isGlobalScore());
    }
});
