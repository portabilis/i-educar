<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacyStageType;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyAcademicYearStage::class, relations: [
    'stageType' => LegacyStageType::class,
    'schoolAcademicYear' => LegacySchoolAcademicYear::class,
]));

EloquentModelTestCase::registerModelTests();
