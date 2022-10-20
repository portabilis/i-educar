<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use Tests\EloquentTestCase;

class LegacyIndividualTest extends EloquentTestCase
{
    public $relations = [
        'mother' => LegacyPerson::class,
        'father' => LegacyPerson::class,
        'responsible' => LegacyPerson::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyIndividual::class;
    }
}
