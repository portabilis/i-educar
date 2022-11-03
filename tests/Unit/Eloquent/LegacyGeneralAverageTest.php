<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGeneralAverage;
use App\Models\LegacyStudentScore;
use Tests\EloquentTestCase;

class LegacyGeneralAverageTest extends EloquentTestCase
{
    protected $relations = [
        'studentScore' => LegacyStudentScore::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyGeneralAverage::class;
    }
}
