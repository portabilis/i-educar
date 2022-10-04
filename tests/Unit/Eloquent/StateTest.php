<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Database\Factories\CityFactory;
use Database\Factories\StateFactory;
use Tests\EloquentTestCase;

class StateTest extends EloquentTestCase
{

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
        $this->state = StateFactory::new()->hasCities()->create();
    }

    public function testRelationshipCountry()
    {
        $this->assertInstanceOf(Country::class, $this->state->country);
    }

    public function testRelationshipCities()
    {
        $this->assertCount(1, $this->state->cities);
        $this->assertInstanceOf(City::class, $this->state->cities->first());
    }

    public function testFindByAbbreviation()
    {
        $state = State::findByAbbreviation($this->state->abbreviation);

        $this->assertInstanceOf(State::class, $state);
        $this->assertArrayHasKey('abbreviation', $state->toArray());
        $this->assertEquals($state->abbreviation, $this->state->abbreviation);
    }
}
