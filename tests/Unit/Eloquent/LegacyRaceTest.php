<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyRace;
use Tests\EloquentTestCase;

class LegacyRaceTest extends EloquentTestCase
{
    protected function getEloquentModelName()
    {
        return LegacyRace::class;
    }
}
