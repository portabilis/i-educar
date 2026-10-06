<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacyUserFactory;
use Tests\Request\Support\LegacyFields;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->employee = EmployeeFactory::new()->create(['id' => LegacyIndividualFactory::new()->create()->idpes]);
    $this->keys = 'ref_cod_servidor=' . $this->employee->getKey() . '&ref_cod_instituicao=' . $this->employee->institution_id;
    $this->link = LegacySchoolClassTeacherFactory::new()->create([
        'servidor_id' => $this->employee,
        'instituicao_id' => $this->employee->institution_id,
    ]);
});

test('abre o formulário de um novo vínculo', function () {
    $this->get('/intranet/educar_servidor_vinculo_turma_cad.php?' . $this->keys)->assertOk();
});

test('edita o vínculo do servidor com a turma', function () {
    $url = '/intranet/educar_servidor_vinculo_turma_cad.php?' . $this->keys . '&id=' . $this->link->getKey();

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent());

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
});
