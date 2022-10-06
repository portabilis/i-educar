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

    public function setUp(): void
    {
        parent::setUp();
        $this->state = $this->createNewModel();
    }

    public function testFindByAbbreviation()
    {
        $state = State::findByAbbreviation($this->state->abbreviation);

        $this->assertInstanceOf(State::class, $state);
        $this->assertArrayHasKey('abbreviation', $state->toArray());
        $this->assertEquals($state->abbreviation, $this->state->abbreviation);
    }
}
