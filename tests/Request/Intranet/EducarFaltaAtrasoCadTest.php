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

test('abre o formulário de uma nova falta ou atraso', function () {
    $this->get('/intranet/educar_falta_atraso_cad.php?' . $this->keys)->assertOk();
});

test('edita a falta ou atraso', function () {
    $url = '/intranet/educar_falta_atraso_cad.php?cod_falta_atraso=' . $this->absence->getKey() . '&' . $this->keys;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['justificada' => 1]);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
});

test('exclui a falta ou atraso', function () {
    $url = '/intranet/educar_falta_atraso_cad.php?cod_falta_atraso=' . $this->absence->getKey() . '&' . $this->keys;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['tipoacao' => 'Excluir']);

    $this->post($url, $payload)->assertRedirect();
});
