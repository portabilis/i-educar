<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;
use OpenApiGenerator\Types\SchemaType;
use Tests\EloquentTestCase;

#[
    Schema(name: 'State'),
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
    protected $relations = [
        'country' => Country::class,
        'cities' => [City::class]
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return State::class;
    }

    public function testFindByAbbreviation()
    {
        $state = $this->createNewModel();
        $stateReturn = State::findByAbbreviation($state->abbreviation);

        $this->assertInstanceOf(State::class, $stateReturn);
        $this->assertArrayHasKey('abbreviation', $stateReturn->toArray());
        $this->assertEquals($stateReturn->abbreviation, $state->abbreviation);
    }
}
