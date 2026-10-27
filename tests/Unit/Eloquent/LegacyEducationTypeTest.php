<?php

use App\Models\LegacyEducationType;
use App\Models\LegacyInstitution;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyEducationType::class, relations: [
    'institution' => LegacyInstitution::class,
]));

EloquentModelTestCase::registerModelTests();
