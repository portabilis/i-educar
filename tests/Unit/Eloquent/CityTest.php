<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\District;
use App\Models\Place;
use App\Models\State;
use Tests\EloquentTestCase;

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
