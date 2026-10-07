<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyStudent;
use App\Models\LegacyStudentHousing;
use Tests\EloquentTestCase;

class LegacyStudentHousingTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyStudentHousing::class;
    }
}
