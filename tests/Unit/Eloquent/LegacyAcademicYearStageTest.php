<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacyStageType;
use Tests\EloquentTestCase;

class LegacyAcademicYearStageTest extends EloquentTestCase
{
    public $relations = [
        'stageType' => LegacyStageType::class,
        'schoolAcademicYear' => LegacySchoolAcademicYear::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyAcademicYearStage::class;
    }
}
