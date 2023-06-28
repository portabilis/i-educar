<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyPerson;
use App\Models\LegacyPhone;
use Tests\EloquentTestCase;

class LegacyPhoneTest extends EloquentTestCase
{
    protected $relations = [
        'person' => LegacyPerson::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyPhone::class;
    }
}
