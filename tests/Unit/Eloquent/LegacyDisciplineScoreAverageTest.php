<?php

use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacyRegistrationScore;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyDisciplineScoreAverage::class, relations: [
    'registrationScore' => LegacyRegistrationScore::class,
]));

EloquentModelTestCase::registerModelTests();
