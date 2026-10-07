<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacyUserFactory;

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

test('mostra o detalhe do vínculo do servidor com a turma', function () {
    $this->get('/intranet/educar_servidor_vinculo_turma_det.php?id=' . $this->link->getKey())->assertOk();
});

test('volta para a listagem quando o vínculo não existe', function () {
    $this->get('/intranet/educar_servidor_vinculo_turma_det.php?id=0')->assertRedirect();
});
