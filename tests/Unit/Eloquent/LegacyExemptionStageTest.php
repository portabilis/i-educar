<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineExemption;
use App\Models\LegacyExemptionStage;
use Tests\EloquentTestCase;

class LegacyExemptionStageTest extends EloquentTestCase
{
    protected $relations = [
        'exemption' => LegacyDisciplineExemption::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyExemptionStage::class;
    }
}
