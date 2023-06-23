<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyIndividual;
use App\Models\Religion;
use Tests\EloquentTestCase;

class ReligionTest extends EloquentTestCase
{
    protected $relations = [
        'individual' => LegacyIndividual::class,
    ];

    protected function getEloquentModelName(): string
    {
        return Religion::class;
    }
}
