<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolStage;
use Tests\EloquentTestCase;

class LegacySchoolStageTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolStage::class;
    }
}
