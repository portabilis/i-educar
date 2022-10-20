<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Tests\EloquentTestCase;

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
        $stateReturn = State::findByAbbreviation($this->model->abbreviation);
        $this->assertInstanceOf(State::class, $stateReturn);
        $this->assertArrayHasKey('abbreviation', $stateReturn->toArray());
        $this->assertEquals($stateReturn->abbreviation, $this->model->abbreviation);
    }

    public function testGetListKeyAbbreviation()
    {
        $list = State::getListKeyAbbreviation();

        $except = collect([
            $this->model->abbreviation => $this->model->name
        ]);

        $this->assertJsonStringEqualsJsonString($except, $list);
    }

    public function testGetNameByAbbreviation()
    {
        $name = State::getNameByAbbreviation($this->model->abbreviation);
        $expect = $this->model->name;

        $this->assertEquals($expect, $name);
    }
}
