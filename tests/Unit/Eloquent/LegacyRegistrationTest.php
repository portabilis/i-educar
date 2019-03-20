<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRegistration;
use App\Models\LegacyStudent;
use Illuminate\Support\Collection;
use Tests\EloquentTestCase;

class LegacyRegistrationTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'student' => LegacyStudent::class,
        'enrollments' => Collection::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyRegistration::class;
    }
}
