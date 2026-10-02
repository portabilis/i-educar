<?php

use Database\Factories\EmployeeAllocationFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyEmployeeRoleFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->employee = EmployeeFactory::new()->create(['id' => LegacyIndividualFactory::new()->create()->idpes]);
    $this->keys = 'ref_cod_servidor=' . $this->employee->getKey() . '&ref_cod_instituicao=' . $this->employee->institution_id;
    $this->role = LegacyEmployeeRoleFactory::new()->create([
        'ref_cod_servidor' => $this->employee,
        'ref_ref_cod_instituicao' => $this->employee->institution_id,
    ]);
    $this->allocation = EmployeeAllocationFactory::new()->create([
        'ref_cod_servidor' => $this->employee,
        'ref_cod_servidor_funcao' => $this->role,
    ]);
});

test('mostra o detalhe da alocação do servidor', function () {
    $this->get('/intranet/educar_servidor_alocacao_det.php?cod_servidor_alocacao=' . $this->allocation->getKey())
        ->assertOk();
});

test('volta para a listagem quando a alocação não existe', function () {
    $this->get('/intranet/educar_servidor_alocacao_det.php?cod_servidor_alocacao=0')->assertRedirect();
});
