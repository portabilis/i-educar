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

test('lista as avaliações de desempenho do servidor', function () {
    $this->get('/intranet/educar_avaliacao_desempenho_lst.php?ref_cod_servidor=' . $this->employee->getKey() . '&ref_ref_cod_instituicao=' . $this->employee->institution_id)
        ->assertOk()
        ->assertSee('Avaliação Alfa');
});
