<?php

namespace App\Models;

use App\Contracts\Enum;

class TransportationVehicleType implements Enum
{
    const VAN = 1;
    const MICROBUS = 2;
    const BUS = 3;
    const BIKE = 4;
    const ANIMAL_TRACTION = 5;
    const OTHER = 6;
    const BOAT_5 = 7;
    const BOAT_5_15 = 8;
    const BOAT_15_35 = 9;
    const BOAT_35 = 10;
    const TRAIN = 11;

    public function getDescriptiveValues(): array
    {
        return [
            self::VAN => 'Rodoviário - Vans/Kombis',
            self::MICROBUS => 'Rodoviário - Microônibus',
            self::BUS => 'Rodoviário - Ônibus',
            self::BIKE => 'Rodoviário - Bicicleta',
            self::ANIMAL_TRACTION => 'Rodoviário - Tração animal',
            self::OTHER => 'Rodoviário - Outro',
            self::BOAT_5 => 'Aquaviário/Embarcação - Capacidade de até 5 alunos',
            self::BOAT_5_15 => 'Aquaviário/Embarcação - Capacidade entre 5 a 15 alunos',
            self::BOAT_15_35 => 'Aquaviário/Embarcação - Capacidade entre 15 a 35 alunos',
            self::BOAT_35 => 'Aquaviário/Embarcação - Capacidade acima de 35 alunos',
            self::TRAIN => 'Ferroviário - Trem/Metrô',
        ];
    }
}
