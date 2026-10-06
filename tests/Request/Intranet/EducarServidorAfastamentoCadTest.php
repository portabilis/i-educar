<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\WithdrawalReasonFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->employee = EmployeeFactory::new()->create(['id' => LegacyIndividualFactory::new()->create()->idpes]);
    $this->keys = 'ref_cod_servidor=' . $this->employee->getKey() . '&ref_cod_instituicao=' . $this->employee->institution_id;
});

test('afasta o servidor', function () {
    $url = '/intranet/educar_servidor_afastamento_cad.php?' . $this->keys;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'ref_cod_motivo_afastamento' => WithdrawalReasonFactory::new()->create(['ref_cod_instituicao' => $this->employee->institution_id])->getKey(),
        'data_saida' => now()->format('d/m/Y'),
    ]);

    $this->post($url, $payload)->assertRedirect();

    $this->assertDatabaseHas('pmieducar.servidor_afastamento', ['ref_cod_servidor' => $this->employee->getKey()]);
});
