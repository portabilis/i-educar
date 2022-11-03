<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyStudent;
use App\Models\LegacyStudentTransport;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyStudentTransportTest extends EloquentTestCase
{
    protected $relations = [
        'student' => LegacyStudent::class,
        'user' => LegacyUser::class
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return LegacyStudentTransport::class;
    }
}
