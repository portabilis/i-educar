<?php

namespace Tests\Unit\Enum;

use App\Models\TransportationVehicleType;
use Tests\TestCase;

class TransportationVehicleTypeTest extends TestCase
{
    public function testDescriptiveValues()
    {
        $values = (new TransportationVehicleType())->getDescriptiveValues();
        $value = current($values);
        $this->assertIsArray($values);
        $this->assertIsString($value);
        $this->assertArrayHasKey(TransportationVehicleType::BIKE, $values);
    }
}
