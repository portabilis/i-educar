<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRegistration;
use App\Models\LegacyRegistrationScore;
use Tests\EloquentTestCase;

class LegacyRegistrationScoreTest extends EloquentTestCase
{
    protected $relations = [
        'registration' => LegacyRegistration::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyRegistrationScore::class;
    }
}
