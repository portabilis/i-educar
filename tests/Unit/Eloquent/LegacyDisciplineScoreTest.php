<?php

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyRegistrationScore;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyDisciplineScore::class, relations: [
    'registrationScore' => LegacyRegistrationScore::class,
]));

EloquentModelTestCase::registerModelTests();
