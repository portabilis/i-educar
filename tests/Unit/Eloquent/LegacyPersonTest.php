<?php

namespace Tests\Unit\App\Models;

use App\Models\LegacyPerson;
use Tests\EloquentTestCase;

class LegacyPersonTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyPerson::class;
    }
}
