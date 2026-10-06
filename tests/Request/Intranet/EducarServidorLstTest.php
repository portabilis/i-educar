<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->employee = EmployeeFactory::new()->create(['id' => LegacyIndividualFactory::new()->create()->idpes]);
    $this->keys = 'ref_cod_servidor=' . $this->employee->getKey() . '&ref_cod_instituicao=' . $this->employee->institution_id;
});

test('lista os servidores filtrando pelo nome', function () {
    $name = $this->employee->person->nome;

    $this->get('/intranet/educar_servidor_lst.php?busca=S&ref_cod_instituicao=' . $this->employee->institution_id . '&nome=' . urlencode($name))
        ->assertOk()
        ->assertSee('educar_servidor_det.php?cod_servidor=' . $this->employee->getKey(), false);
});
