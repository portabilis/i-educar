<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyRoundingTable;
use App\Models\LegacyValueRoundingTable;
use Tests\EloquentTestCase;

class LegacyRoundingTableTest extends EloquentTestCase
{
    protected $relations = [
        'roundingValues' => LegacyValueRoundingTable::class,
        'evaluationRules' => LegacyEvaluationRule::class,
    ];

    public function getEloquentModelName(): string
    {
        return LegacyRoundingTable::class;
    }
}
