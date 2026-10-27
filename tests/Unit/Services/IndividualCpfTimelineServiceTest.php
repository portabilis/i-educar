<?php

use App\Services\IndividualCpfTimelineService;
use Tests\TestCase;

uses(TestCase::class);

test('summary describes each kind of change', function () {
    $service = app(IndividualCpfTimelineService::class);

    $this->assertSame('Informou o CPF 123.456.789-09', $service->summary(['after' => ['CPF' => '123.456.789-09']]));
    $this->assertSame('Marcou "Não possui" com o motivo Estrangeiro', $service->summary(['after' => ['Motivo da ausência' => 'Estrangeiro']]));
    $this->assertSame('Removeu o CPF', $service->summary(['after' => ['CPF' => null]]));
    $this->assertSame('Removeu o CPF', $service->summary([]));
});
