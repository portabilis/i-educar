<?php

namespace Tests\Unit\Model\Concerns;

use App\Models\City;
use Database\Factories\CityFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HasIbgeCodeTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        $this->city = CityFactory::new()->create();
    }

    public function testHasIbgeCode(): void
    {
        $city = City::findByIbgeCode($this->city->ibge_code);

        $this->assertArrayHasKey('ibge_code', $city->toArray());
        $this->assertEquals($city->ibge_code, $this->city->ibge_code);
    }
}
