<?php

namespace Tests\Unit\Enum;

use App\Models\TransportationVehicleType;
use Tests\EnumTestCase;

class TransportationVehicleTypeTest extends EnumTestCase
{
    public function getDescriptiveValues(): array
    {
        return [
            1 => 'Rodoviário - Vans/Kombis',
            2 => 'Rodoviário - Microônibus',
            3 => 'Rodoviário - Ônibus',
            4 => 'Rodoviário - Bicicleta',
            5 => 'Rodoviário - Tração animal',
            6 => 'Rodoviário - Outro',
            7 => 'Aquaviário/Embarcação - Capacidade de até 5 alunos',
            8 => 'Aquaviário/Embarcação - Capacidade entre 5 a 15 alunos',
            9 => 'Aquaviário/Embarcação - Capacidade entre 15 a 35 alunos',
            10 => 'Aquaviário/Embarcação - Capacidade acima de 35 alunos',
            11 => 'Ferroviário - Trem/Metrô',
        ];
    }

    protected function getEnumName(): string
    {
        return TransportationVehicleType::class;
    }
}
