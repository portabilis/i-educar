<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\District;
use App\Models\State;
use Database\Factories\CityFactory;
use Database\Factories\DistrictFactory;
use Tests\EloquentTestCase;

class DistrictTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return District::class;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->district = DistrictFactory::new()->create();
    }

    public function testRelationshipCity()
    {
        $this->assertInstanceOf(City::class, $this->district->city);
    }
}
