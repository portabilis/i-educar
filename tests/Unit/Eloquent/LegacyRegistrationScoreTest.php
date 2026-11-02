<?php

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacyGeneralAverage;
use App\Models\LegacyRegistration;
use App\Models\LegacyRegistrationScore;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyRegistrationScore::class, relations: [
    'registration' => LegacyRegistration::class,
    'generalAverages' => LegacyGeneralAverage::class,
    'disciplineScoreAverages' => LegacyDisciplineScoreAverage::class,
    'disciplineScores' => LegacyDisciplineScore::class,
]));

EloquentModelTestCase::registerModelTests();
