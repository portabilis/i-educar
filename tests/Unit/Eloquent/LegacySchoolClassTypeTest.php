<?php

use App\Models\LegacyInstitution;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassType;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacySchoolClassType::class, relations: [
    'institution' => LegacyInstitution::class,
    'schoolClasses' => LegacySchoolClass::class,
]));

EloquentModelTestCase::registerModelTests();
