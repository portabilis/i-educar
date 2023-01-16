<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\District;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;
use Tests\EloquentTestCase;

#[
    Schema(name: 'District'),
    Property(Type::INT, 'id', 'District ID', 1),
    Property(Type::INT, 'city_id', 'City ID', 1),
    Property(Type::STRING, 'name', 'District name', 'São Miguel'),
    Property(Type::STRING, 'ibge_code', 'IBGE code', 12345),
    Property(Type::STRING, 'created_at', 'Creation date', '2022-01-01 00:00:00'),
    Property(Type::STRING, 'updated_at', 'Update date', '2022-01-01 00:00:00'),
]
class DistrictTest extends EloquentTestCase
{
    /**
     * @var string[]
     */
    protected $relations = [
        'city' => City::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return District::class;
    }
}
