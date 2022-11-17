<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\District;
use App\Models\Place;
use App\Models\State;
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
    protected $relations = [
        'state' => State::class,
        'districts' => District::class,
        'places' => Place::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return City::class;
    }

    /** @test */
    public function attributes()
    {
        $query = City::queryFindByName($this->model->name)->first();

        $this->assertEquals($this->model->name, $query->name);

        $query = City::getNameById($this->model->id);

        $this->assertEquals($this->model->name, $query);
    }
}
