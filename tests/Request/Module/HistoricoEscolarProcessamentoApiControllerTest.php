<?php

use Database\Factories\LegacyUserFactory;
use Tests\Api\DiarioApiFakeDataTestTrait;

uses(DiarioApiFakeDataTestTrait::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($this->user);
    // Matrícula enturmada, com etapa e um componente curricular lançáveis.
    $enrollment = $this->getPromotionFromAverageAndAttendanceWithoutRetake();
    $this->schoolClass = $enrollment->schoolClass;
    $this->registration = $enrollment->registration;
    $this->createStages($this->schoolClass->school, 1);
    $this->createDisciplines($this->schoolClass, 1);
    $this->params = [
        'instituicao_id' => $this->schoolClass->ref_cod_instituicao,
        'escola_id' => $this->schoolClass->ref_ref_cod_escola,
        'curso_id' => $this->schoolClass->ref_cod_curso,
        'serie_id' => $this->schoolClass->ref_ref_cod_serie,
        'turma_id' => $this->schoolClass->getKey(),
        'ano' => $this->schoolClass->ano,
    ];
});

test('lista as matrículas da turma para processar o histórico', function () {
    $this->get('/module/HistoricoEscolar/processamentoApi?' . http_build_query(['oper' => 'get', 'att' => 'matriculas'] + $this->params))
        ->assertOk()
        ->assertJsonPath('att', 'matriculas');
});

test('processa o histórico da matrícula a partir do boletim', function () {
    $this->post('/module/HistoricoEscolar/processamentoApi?' . http_build_query([
        'oper' => 'post',
        'att' => 'processamento',
        'matricula_id' => $this->registration->getKey(),
        'dias_letivos' => 200,
        'situacao' => 'buscar-matricula',
        'extra_curricular' => 0,
        'grade_curso_id' => 1,
        'percentual_frequencia' => 'buscar-boletim',
        'notas' => 'buscar-boletim',
        'faltas' => 'buscar-boletim',
        'disciplinas' => 'buscar-boletim',
        'media_area_conhecimento' => 0,
        'processar_media_geral' => 0,
        'posicao' => 1,
    ] + $this->params))
        ->assertOk()
        ->assertJsonPath('att', 'processamento');
});
