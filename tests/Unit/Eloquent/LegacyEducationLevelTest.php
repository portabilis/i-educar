<?php

use App\Models\LegacyEducationLevel;
use App\Models\LegacyInstitution;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyEducationLevel::class, relations: [
    'institution' => LegacyInstitution::class,
]));

EloquentModelTestCase::registerModelTests();
