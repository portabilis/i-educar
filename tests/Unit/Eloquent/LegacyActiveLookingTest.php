<?php

use App\Models\LegacyActiveLooking;
use App\Models\LegacyRegistration;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyActiveLooking::class, relations: [
    'registration' => LegacyRegistration::class,
]));

EloquentModelTestCase::registerModelTests();
