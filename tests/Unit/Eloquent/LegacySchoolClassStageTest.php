<?php

use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacyStageType;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacySchoolClassStage::class, relations: [
    'stageType' => LegacyStageType::class,
    'schoolClass' => LegacySchoolClass::class,
]));

EloquentModelTestCase::registerModelTests();
