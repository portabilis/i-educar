<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyStudent;
use App\Models\StudentInep;
use Tests\EloquentTestCase;

class StudentInepTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return StudentInep::class;
    }
}
