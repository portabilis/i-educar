<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyIndividual;
use App\Models\LegacyRace;
use Tests\EloquentTestCase;

class LegacyRaceTest extends EloquentTestCase
{
    protected $relations = [
        'individual' => LegacyIndividual::class
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return LegacyRace::class;
    }
}
