<?php

namespace Tests\Unit\App\Models;

use App\Models\LegacyPerson;
use App\Models\LegacyStudent;
use Tests\EloquentTestCase;

class LegacyStudentTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'person' => LegacyPerson::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStudent::class;
    }
}
