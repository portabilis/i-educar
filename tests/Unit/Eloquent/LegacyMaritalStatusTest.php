<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyIndividual;
use App\Models\LegacyMaritalStatus;
use Tests\EloquentTestCase;

class LegacyMaritalStatusTest extends EloquentTestCase
{
    protected $relations = [
        'individuals' => LegacyIndividual::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyMaritalStatus::class;
    }
}
