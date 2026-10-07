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

test('abre a seleção de cursos do servidor', function () {
    $this->get('/intranet/educar_servidor_curso_lst.php?' . $this->keys)->assertOk();
});
