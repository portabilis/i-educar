<?php

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyRemedialRule;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyRemedialRule::class, relations: [
    'evaluationRule' => LegacyEvaluationRule::class,
]));

EloquentModelTestCase::registerModelTests();

test('get stages', function () {
    $expected = explode(';', $this->model->etapas_recuperadas);
    $this->assertEquals($expected, $this->model->getStages());
});

test('get last stage', function () {
    $this->assertEquals(max($this->model->getStages()), $this->model->getLastStage());
});
