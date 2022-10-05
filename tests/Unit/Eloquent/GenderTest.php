<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\District;
use App\Models\Gender;
use Database\Factories\CityFactory;
use Tests\TestCase;

class GenderTest extends TestCase
{
    public function test_get_array_values(){
        $gender = new Gender();
        $genres = $gender->getDescriptiveValues();

        $this->assertArrayHasKey(Gender::MALE, $genres);
        $this->assertArrayHasKey(Gender::FEMALE, $genres);
        $this->assertIsArray($genres);
    }
}
