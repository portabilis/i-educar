<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyIndividual;
use Tests\EloquentTestCase;

class LegacyIndividualTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyIndividual::class;
    }
}
