<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacySchool;
use App\Models\LegacyStudent;
use App\Models\UniformDistribution;
use Tests\EloquentTestCase;

class UniformDistributionTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
        'school' => LegacySchool::class,
    ];

    protected function getEloquentModelName()
    {
        return UniformDistribution::class;
    }
}
