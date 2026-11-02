<?php

use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\PerformanceEvaluationFactory;
use Tests\Request\Support\LegacyFields;

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

test('cadastra uma avaliação de desempenho', function () {
    $url = '/intranet/educar_avaliacao_desempenho_cad.php?ref_cod_servidor=' . $this->employee->getKey() . '&ref_ref_cod_instituicao=' . $this->employee->institution_id;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent(), [
        'titulo_avaliacao' => 'Avaliação Beta',
        'descricao' => 'Descrição da avaliação',
    ]);

    $this->post($url, $payload)->assertRedirect();
});

test('edita e exclui a avaliação de desempenho', function () {
    $url = '/intranet/educar_avaliacao_desempenho_cad.php?id=' . $this->evaluation->getKey() . '&ref_cod_servidor=' . $this->employee->getKey() . '&ref_ref_cod_instituicao=' . $this->employee->institution_id;

    $payload = LegacyFields::from($this->get($url)->assertOk()->getContent());

    expect($payload['tipoacao'])->toBe('Editar');

    $this->post($url, $payload)->assertRedirect();
    $this->post($url, ['tipoacao' => 'Excluir'] + $payload)->assertRedirect();
});
