<?php

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacyGeneralAverage;
use App\Models\LegacyGeneralScore;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentScore;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyStudentScore::class, relations: [
    'registration' => LegacyRegistration::class,
    'scoreGeneral' => LegacyGeneralScore::class,
    'scoreByDiscipline' => LegacyDisciplineScore::class,
    'averageByDiscipline' => LegacyDisciplineScoreAverage::class,
    'averageGeneral' => LegacyGeneralAverage::class,
]));

EloquentModelTestCase::registerModelTests();
