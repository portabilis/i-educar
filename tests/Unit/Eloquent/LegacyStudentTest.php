<?php

namespace Tests\Unit\App\Models;

use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudent;
use App\Models\StudentInep;
use Tests\EloquentTestCase;

class LegacyStudentTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'individual' => LegacyIndividual::class,
        'person' => LegacyPerson::class,
        'registrations' => LegacyRegistration::class,
        'inep' => StudentInep::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyStudent::class;
    }
}
