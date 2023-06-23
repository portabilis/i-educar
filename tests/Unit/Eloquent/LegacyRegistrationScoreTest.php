<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacyGeneralAverage;
use App\Models\LegacyRegistration;
use App\Models\LegacyRegistrationScore;
use Tests\EloquentTestCase;

class LegacyRegistrationScoreTest extends EloquentTestCase
{
    protected $relations = [
        'registration' => LegacyRegistration::class,
        'generalAverages' => LegacyGeneralAverage::class,
        'disciplineScoreAverages' => LegacyDisciplineScoreAverage::class,
        'disciplineScores' => LegacyDisciplineScore::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyRegistrationScore::class;
    }
}
