<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyStudent;
use App\Models\LegacyStudentHistoricalHeightWeight;
use Tests\EloquentTestCase;

class LegacyStudentHistoricalHeightWeightTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
    ];

    protected function getEloquentModelName()
    {
        return LegacyStudentHistoricalHeightWeight::class;
    }
}
