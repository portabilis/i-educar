<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyExemptionStage;
use Tests\EloquentTestCase;

class LegacyExemptionStageTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyExemptionStage::class;
    }
}
