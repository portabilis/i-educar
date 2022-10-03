<?php

namespace Tests\Unit\Eloquent;

use App\Models\Country;
use App\Models\State;
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
        $this->state = StateFactory::new()->create();
    }

    public function testRelationshipCountry()
    {
        $this->assertInstanceOf(Country::class, $this->state->country);
    }

    public function testFindByAbbreviation()
    {
        $state = State::findByAbbreviation($this->state->abbreviation);

        $this->assertArrayHasKey('abbreviation', $state->toArray());
        $this->assertEquals($state->abbreviation, $this->state->abbreviation);
    }
}
