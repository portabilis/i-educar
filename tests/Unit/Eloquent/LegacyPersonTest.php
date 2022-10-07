<?php

namespace Tests\Unit\App\Models;

use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use Tests\EloquentTestCase;

class LegacyPersonTest extends EloquentTestCase
{
    protected $relations = [
        'phone' => [LegacyPhone::class],
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyPerson::class;
    }
}
