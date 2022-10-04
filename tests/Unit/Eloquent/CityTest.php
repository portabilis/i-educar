<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\District;
use Database\Factories\CityFactory;
use Tests\EloquentTestCase;

class CityTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return City::class;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->city = CityFactory::new()->hasDistricts()->create();
    }

    public function testRelationshipDistricts()
    {
        $this->assertCount(1, $this->city->districts);
        $this->assertInstanceOf(District::class, $this->city->districts->first());
    }
}
