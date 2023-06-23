<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacyStageType;
use Tests\EloquentTestCase;

class LegacySchoolClassStageTest extends EloquentTestCase
{
    protected $relations = [
        'stageType' => LegacyStageType::class,
        'schoolClass' => LegacySchoolClass::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacySchoolClassStage::class;
    }
}
