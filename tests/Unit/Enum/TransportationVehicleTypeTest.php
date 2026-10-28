<?php

use App\Models\TransportationVehicleType;
use Tests\TestCase;

uses(TestCase::class);

test('descriptive values', function () {
    $values = (new TransportationVehicleType)->getDescriptiveValues();
    $this->assertIsArray($values);
    $this->assertJsonStringEqualsJsonString(collect([
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
    ]), collect($values));
});
