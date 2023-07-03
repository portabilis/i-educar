<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyRemedialRule;
use Tests\EloquentTestCase;

class LegacyRemedialRuleTest extends EloquentTestCase
{
    protected $relations = [
        'evaluationRule' => LegacyEvaluationRule::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyRemedialRule::class;
    }

    public function testGetStages(): void
    {
        $expected = explode(';', $this->model->etapas_recuperadas);
        $this->assertEquals($expected, $this->model->getStages());
    }

    public function testGetLastStage(): void
    {
        $this->assertEquals(max($this->model->getStages()), $this->model->getLastStage());
    }
}
