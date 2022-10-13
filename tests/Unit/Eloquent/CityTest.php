<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;
use Tests\EloquentTestCase;

#[
    Schema(name: 'City'),
    Property(Type::INT, 'id', 'City ID', 1),
    Property(Type::INT, 'state_id', 'State ID', 1),
    Property(Type::STRING, 'name', 'City name', 'Francisco Beltrão'),
    Property(Type::STRING, 'ibge_code', 'IBGE code', 12345),
    Property(Type::STRING, 'created_at', 'Creation date', '2022-01-01 00:00:00'),
    Property(Type::STRING, 'updated_at', 'Update date', '2022-01-01 00:00:00'),
]
class CityTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return City::class;
    }
}
