<?php

namespace Tests\Unit\Eloquent;

use App\Models\Country;
use App\Models\State;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;
use Tests\EloquentTestCase;

#[
    Schema(name: 'Country'),
    Property(Type::INT, 'id', 'Country ID', 1),
    Property(Type::STRING, 'name', 'Country name', 'Brasil'),
    Property(Type::STRING, 'ibge_code', 'IBGE code', 12345),
    Property(Type::STRING, 'created_at', 'Creation date', '2022-01-01 00:00:00'),
    Property(Type::STRING, 'updated_at', 'Update date', '2022-01-01 00:00:00'),
]
class CountryTest extends EloquentTestCase
{
    protected $relations = [
        'states' => State::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Country::class;
    }
}
