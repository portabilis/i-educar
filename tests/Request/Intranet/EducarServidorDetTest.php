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
});

test('mostra o detalhe do servidor com função e alocação', function () {
    $role = LegacyEmployeeRoleFactory::new()->create([
        'ref_cod_servidor' => $this->employee,
        'ref_ref_cod_instituicao' => $this->employee->institution_id,
    ]);
    EmployeeAllocationFactory::new()->create([
        'ref_cod_servidor' => $this->employee,
        'ref_cod_servidor_funcao' => $role,
    ]);

    $this->get('/intranet/educar_servidor_det.php?cod_servidor=' . $this->employee->getKey() . '&ref_cod_instituicao=' . $this->employee->institution_id)
        ->assertOk()
        ->assertSee('Servidor - Detalhe');
});

test('volta para a listagem quando o servidor não existe', function () {
    $this->get('/intranet/educar_servidor_det.php?cod_servidor=0&ref_cod_instituicao=' . $this->employee->institution_id)
        ->assertRedirect('educar_servidor_lst.php');
});
