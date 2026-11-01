<?php

use App\Models\LegacyRegistration;
use App\Services\Exemption\BatchExemptionService;
use App\Services\Exemption\ExemptionService;

test('cria as dispensas de cada matrícula adicionada com os componentes, o tipo, a observação e as etapas', function () {
    $ana = new LegacyRegistration(['cod_matricula' => 1]);
    $bia = new LegacyRegistration(['cod_matricula' => 2]);

    $exemptions = Mockery::mock(ExemptionService::class);
    $exemptions->shouldReceive('createExemptionByDisciplineArray')->once()->with($ana, [7], 3, 'Obs', [1, 2]);
    $exemptions->shouldReceive('createExemptionByDisciplineArray')->once()->with($bia, [8], 4, null, [1]);

    $service = new BatchExemptionService($exemptions);
    $service->addRegistration($ana, [7], 3, 'Obs', [1, 2]);
    $service->addRegistration($bia, [8], 4, null, [1]);
    $service->handle();
});
