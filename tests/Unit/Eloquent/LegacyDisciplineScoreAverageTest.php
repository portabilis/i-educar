<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacyRegistrationScore;
use Tests\EloquentTestCase;

class LegacyDisciplineScoreAverageTest extends EloquentTestCase
{
    protected $relations = [
        'registrationScore' => LegacyRegistrationScore::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyDisciplineScoreAverage::class;
    }
}
