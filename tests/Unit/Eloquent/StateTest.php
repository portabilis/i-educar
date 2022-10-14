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
        $state = $this->createNewModel();
        $stateReturn = State::findByAbbreviation($state->abbreviation);

        $this->assertInstanceOf(State::class, $stateReturn);
        $this->assertArrayHasKey('abbreviation', $stateReturn->toArray());
        $this->assertEquals($stateReturn->abbreviation, $state->abbreviation);
    }
}
