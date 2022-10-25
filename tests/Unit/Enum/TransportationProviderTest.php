<?php

namespace Tests\Unit\Enum;

use App\Models\TransportationProvider;
use Tests\TestCase;

class TransportationProviderTest extends TestCase
{
    public function testDescriptiveValues(): void
    {
        $values = (new TransportationProvider())->getDescriptiveValues();
        $value = current($values);
        $this->assertIsArray($values);
        $this->assertIsString($value);
        $this->assertArrayHasKey(TransportationProvider::CITY, $values);
        $this->assertArrayHasKey(TransportationProvider::STATE, $values);
    }
}
