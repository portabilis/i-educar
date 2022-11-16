<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyRemedialRule;
use App\Models\LegacyRoundingTable;
use Tests\EloquentTestCase;

class LegacyEvaluationRuleTest extends EloquentTestCase
{
    public $relations = [
        'remedialRules' => LegacyRemedialRule::class,
        'roundingTable' => LegacyRoundingTable::class,
        'conceptualRoundingTable' => LegacyRoundingTable::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEvaluationRule::class;
    }

    /** @test */
    public function isGlobalScoreRule()
    {
        if ($this->model->nota_geral_por_etapa == 1) {
            $this->assertEquals(true, $this->model->isGlobalScore());
        } else {
            $this->assertEquals(false, $this->model->isGlobalScore());
        }
    }
}
