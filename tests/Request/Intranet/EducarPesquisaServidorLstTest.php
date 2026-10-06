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

test('pesquisa professores disponíveis para um horário', function () {
    $query = http_build_query([
        'ref_cod_instituicao' => $this->employee->institution_id,
        'professor' => 1,
        'horario' => 'S',
        'dia_semana' => 2,
        'hora_inicial' => '08:00',
        'hora_final' => '09:00',
        'ano_alocacao' => now()->year,
    ]);

    $this->get('/intranet/educar_pesquisa_servidor_lst.php?' . $query)->assertOk();
});
