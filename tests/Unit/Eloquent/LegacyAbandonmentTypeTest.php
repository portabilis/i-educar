<?php

use App\Models\LegacyAbandonmentType;
use App\Models\LegacyInstitution;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyAbandonmentType::class, relations: [
    'institution' => LegacyInstitution::class,
]));

EloquentModelTestCase::registerModelTests();
