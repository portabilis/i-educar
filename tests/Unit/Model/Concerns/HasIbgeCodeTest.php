<?php

namespace Tests\Unit\Model\Concerns;

use App\Models\City;
use Database\Factories\CityFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HasIbgeCodeTest extends TestCase
{
    use DatabaseTransactions;

    public function testHasIbgeCode(): void
    {
        $city = CityFactory::new()->create();

        $cityReturn = City::findByIbgeCode($city->ibge_code);

        $this->assertArrayHasKey('ibge_code', $city->toArray());
        $this->assertEquals($cityReturn->ibge_code, $city->ibge_code);
    }
}
