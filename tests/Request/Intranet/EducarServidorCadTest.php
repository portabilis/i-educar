<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->employee = EmployeeFactory::new()->create(['id' => LegacyIndividualFactory::new()->create()->idpes]);
    $this->keys = 'ref_cod_servidor=' . $this->employee->getKey() . '&ref_cod_instituicao=' . $this->employee->institution_id;
});

test('abre o formulário de um novo servidor', function () {
    $this->get('/intranet/educar_servidor_cad.php')->assertOk();
});

test('edita a carga horária do servidor', function () {
    LegacyEmployeeRoleFactory::new()->create([
        'ref_cod_servidor' => $this->employee,
        'ref_ref_cod_instituicao' => $this->employee->institution_id,
    ]);

    $url = '/intranet/educar_servidor_cad.php?cod_servidor=' . $this->employee->getKey() . '&ref_cod_instituicao=' . $this->employee->institution_id;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), ['carga_horaria' => '30:00']);

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
});
