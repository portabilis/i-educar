<?php

namespace Tests\Unit\Eloquent;

use App\Models\Country;
use App\Models\State;
use Database\Factories\CountryFactory;
use Tests\EloquentTestCase;

class CountryTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Country::class;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->country = CountryFactory::new()->hasStates()->create();
    }

    public function testRelationshipStates()
    {
        $this->assertCount(1, $this->country->states);
        $this->assertInstanceOf(State::class, $this->country->states->first());
    }

}
