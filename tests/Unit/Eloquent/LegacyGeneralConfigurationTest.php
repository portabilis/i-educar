<?php

use App\Models\LegacyGeneralConfiguration;
use App\Models\LegacyInstitution;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyGeneralConfiguration::class, relations: [
    'institution' => LegacyInstitution::class,
]));

EloquentModelTestCase::registerModelTests();
