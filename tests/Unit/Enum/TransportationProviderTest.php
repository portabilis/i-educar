<?php

namespace Tests\Unit\Enum;

use App\Models\TransportationProvider;
use Tests\EnumTestCase;

class TransportationProviderTest extends EnumTestCase
{
    public function getDescriptiveValues(): array
    {
        return [
            0 => 'Não utiliza',
            1 => 'Estadual',
            2 => 'Municipal',
        ];
    }

    protected function getEnumName(): string
    {
        return TransportationProvider::class;
    }

    public function testFrom(): void
    {
        $this->assertEquals(0, $this->enum->from(null));
        $this->assertEquals(1, $this->enum->from('estadual'));
        $this->assertEquals(2, $this->enum->from('municipal'));
    }

    public function testValueDescription(): void
    {
        $this->assertEquals('nenhum', $this->enum->getValueDescription(0));
        $this->assertEquals('estadual', $this->enum->getValueDescription(1));
        $this->assertEquals('municipal', $this->enum->getValueDescription(2));
    }
}
