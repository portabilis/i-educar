<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyAcademicYearStage;
use Tests\EloquentTestCase;

class LegacyAcademicYearStageTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyAcademicYearStage::class;
    }
}
