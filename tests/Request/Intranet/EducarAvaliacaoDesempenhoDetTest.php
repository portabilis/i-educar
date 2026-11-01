<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\PerformanceEvaluationFactory;

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    $this->employee = EmployeeFactory::new()->create(['id' => LegacyIndividualFactory::new()->create()->idpes]);
    $this->keys = 'ref_cod_servidor=' . $this->employee->getKey() . '&ref_cod_instituicao=' . $this->employee->institution_id;
    $this->evaluation = PerformanceEvaluationFactory::new()->create([
        'employee_id' => $this->employee,
        'institution_id' => $this->employee->institution_id,
        'title' => 'Avaliação Alfa',
    ]);
});

test('mostra o detalhe da avaliação de desempenho', function () {
    $this->get('/intranet/educar_avaliacao_desempenho_det.php?id=' . $this->evaluation->getKey())
        ->assertOk()
        ->assertSee($this->evaluation->description);
});

test('volta para a listagem quando a avaliação não existe', function () {
    $this->get('/intranet/educar_avaliacao_desempenho_det.php?id=0')->assertRedirect();
});
