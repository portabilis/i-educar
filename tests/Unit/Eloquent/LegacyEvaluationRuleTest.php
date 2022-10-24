<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyRemedialRule;
use Tests\EloquentTestCase;

class LegacyEvaluationRuleTest extends EloquentTestCase
{
    private LegacyEvaluationRule $evaluationRule;

    public $relations = [
        'remedialRules' => LegacyRemedialRule::class,
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
