<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyActiveLooking;
use App\Models\LegacyRegistration;
use Tests\EloquentTestCase;

class LegacyActiveLookingTest extends EloquentTestCase
{
    public $relations = [
        'registration' => LegacyRegistration::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyActiveLooking::class;
    }
}
