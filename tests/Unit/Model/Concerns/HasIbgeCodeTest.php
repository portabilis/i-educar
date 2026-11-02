<?php

use App\Models\City;
use Database\Factories\CityFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('has ibge code', function () {
    $city = CityFactory::new()->create();

    $cityReturn = City::findByIbgeCode($city->ibge_code);

    $this->assertArrayHasKey('ibge_code', $city->toArray());
    $this->assertEquals($cityReturn->ibge_code, $city->ibge_code);
});
