<?php

namespace Tests\Unit\Eloquent;

use App\Models\City;
use App\Models\District;
use App\Models\Gender;
use App\Models\GuardianType;
use Database\Factories\CityFactory;
use Tests\TestCase;

class GuardianTypeTest extends TestCase
{
    public function test_get_array_values(){
        $guardian = new GuardianType();
        $types = $guardian->getDescriptiveValues();

        $this->assertArrayHasKey(GuardianType::FATHER, $types);
        $this->assertArrayHasKey(GuardianType::MOTHER, $types);
        $this->assertArrayHasKey(GuardianType::BOTH, $types);
        $this->assertArrayHasKey(GuardianType::OTHER, $types);
        $this->assertIsArray($types);
    }
}
