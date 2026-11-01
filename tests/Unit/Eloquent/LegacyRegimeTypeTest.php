<?php

use App\Models\LegacyCourse;
use App\Models\LegacyInstitution;
use App\Models\LegacyRegimeType;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyRegimeType::class, relations: [
    'institution' => LegacyInstitution::class,
    'courses' => LegacyCourse::class,
]));

EloquentModelTestCase::registerModelTests();
