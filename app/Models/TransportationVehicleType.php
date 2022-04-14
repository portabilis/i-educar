<?php

namespace App\Models;

use App\Contracts\Enum;

class TransportationVehicleType implements Enum
{
    public const VAN = 1;
    public const MICROBUS = 2;
    public const BUS = 3;
    public const BIKE = 4;
    public const ANIMAL_TRACTION = 5;
    public const OTHER = 6;
    public const BOAT_5 = 7;
    public const BOAT_5_15 = 8;
    public const BOAT_15_35 = 9;
    public const BOAT_35 = 10;
    public const TRAIN = 11;

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
