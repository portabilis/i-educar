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

test('pesquisa servidores pelo nome', function () {
    $this->get('/intranet/educar_pesquisa_servidor_lst.php?busca=S&campo1=ref_cod_servidor&campo2=nm_servidor&ref_cod_instituicao=' . $this->employee->institution_id . '&nome_servidor=' . urlencode($this->employee->person->nome))
        ->assertOk();
});
