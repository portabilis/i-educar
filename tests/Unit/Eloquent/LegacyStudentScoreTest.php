<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacyGeneralAverage;
use App\Models\LegacyGeneralScore;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentScore;
use Tests\EloquentTestCase;

class LegacyStudentScoreTest extends EloquentTestCase
{
    protected $relations = [
        'registration' => LegacyRegistration::class,
        'scoreGeneral' => LegacyGeneralScore::class,
        'scoreByDiscipline' => LegacyDisciplineScore::class,
        'averageByDiscipline' => LegacyDisciplineScoreAverage::class,
        'averageGeneral' => LegacyGeneralAverage::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyStudentScore::class;
    }
}
