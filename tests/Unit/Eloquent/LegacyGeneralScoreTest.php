<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyGeneralScore;
use App\Models\LegacyStudentScore;
use Tests\EloquentTestCase;

class LegacyGeneralScoreTest extends EloquentTestCase
{
    protected $relations = [
        'studentScore' => LegacyStudentScore::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyGeneralScore::class;
    }
}
