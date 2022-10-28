<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyRemedialRule;
use App\Models\LegacyRoundingTable;
use Tests\EloquentTestCase;

class LegacyEvaluationRuleTest extends EloquentTestCase
{
    private LegacyEvaluationRule $evaluationRule;

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

    public function setUp(): void
    {
        parent::setUp();

        $this->evaluationRule = $this->createNewModel();
    }
}
