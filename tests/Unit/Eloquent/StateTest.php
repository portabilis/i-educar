<?php

namespace Tests\Unit\Eloquent;

use App\Models\State;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;
use Tests\EloquentTestCase;

#[
    Schema,
    Property(Type::INT, 'id', 'State ID', 1),
    Property(Type::INT, 'country_id', 'Country ID', 1),
    Property(Type::STRING, 'name', 'State name', 'Paraná'),
    Property(Type::STRING, 'abbreviation', 'Name Abbreviation', 'PR'),
    Property(Type::STRING, 'ibge_code', 'IBGE code', 12345),
    Property(Type::STRING, 'created_at', 'Creation date', '2022-01-01 00:00:00'),
    Property(Type::STRING, 'updated_at', 'Update date', '2022-01-01 00:00:00'),
]
class StateTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return State::class;
    }
}
