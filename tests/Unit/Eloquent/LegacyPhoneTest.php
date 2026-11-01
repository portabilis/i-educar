<?php

use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyPhone::class, relations: [
    'person' => LegacyPerson::class,
]));

EloquentModelTestCase::registerModelTests();
