<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolClassStage;
use Tests\EloquentTestCase;

class LegacySchoolClassStageTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacySchoolClassStage::class;
    }
}
