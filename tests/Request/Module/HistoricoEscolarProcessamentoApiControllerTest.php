<?php

use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Support\Arr;
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

test('lista a série de cada matrícula, inclusive em turma multisseriada', function () {
    $multigradeRegistration = LegacyRegistrationFactory::new()->withEnrollment($this->schoolClass)->create([
        'ano' => $this->schoolClass->ano,
        'ref_ref_cod_serie' => LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $this->schoolClass->ref_cod_curso,
        ]),
    ]);

    $response = $this->get('/module/HistoricoEscolar/processamentoApi?' . http_build_query(['oper' => 'get', 'att' => 'matriculas'] + Arr::except($this->params, 'serie_id')))
        ->assertOk();

    $series = collect($response->json('matriculas'))->pluck('serie_id', 'matricula_id');

    expect($series[$this->registration->getKey()])->toBe($this->registration->ref_ref_cod_serie)
        ->and($series[$multigradeRegistration->getKey()])->toBe($multigradeRegistration->ref_ref_cod_serie)
        ->and($multigradeRegistration->ref_ref_cod_serie)->not->toBe($this->registration->ref_ref_cod_serie);
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
