<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyAbsenceDelayFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->employee = EmployeeFactory::new()->create(['id' => LegacyIndividualFactory::new()->create()->idpes]);
    $this->keys = 'ref_cod_servidor=' . $this->employee->getKey() . '&ref_cod_instituicao=' . $this->employee->institution_id;
    $this->absence = LegacyAbsenceDelayFactory::new()->create([
        'ref_cod_servidor' => $this->employee,
        'ref_ref_cod_instituicao' => $this->employee->institution_id,
    ]);
    $this->keys .= '&ref_cod_escola=' . $this->absence->ref_cod_escola;
});

test('abre o formulário e grava a compensação de horas', function () {
    $url = '/intranet/educar_falta_atraso_compensado_cad.php?' . $this->keys;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'data_inicio' => now()->subDay()->format('d/m/Y'),
        'data_fim' => now()->format('d/m/Y'),
    ]);

    $this->post($url, $payload)->assertRedirect();
});
