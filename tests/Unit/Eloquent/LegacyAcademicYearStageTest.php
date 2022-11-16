<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyStageType;
use Tests\EloquentTestCase;

class LegacyAcademicYearStageTest extends EloquentTestCase
{
    public $relations = [
        'module' => LegacyStageType::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyAcademicYearStage::class;
    }
}
