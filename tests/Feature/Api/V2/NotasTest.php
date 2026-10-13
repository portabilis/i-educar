<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyDisciplineSchoolClass;
use App\Models\LegacyDisciplineScore;
use App\Models\LegacyDisciplineScoreAverage;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralScore;
use App\Models\LegacySchoolGradeDiscipline;
use App\Services\DiarioControllerService;
use Database\Factories\LegacyDisciplineDependenceFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacyScoreExamFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\V2\CreatesScoreData;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class, CreatesScoreData::class);

test('rejects request without token', function () {
    $data = $this->createScoreData();

    $this->postJson('/api/v2/notas', $this->scorePayload($data, ['nota' => 6]))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('rejects request with invalid token', function () {
    $data = $this->createScoreData();

    $this->withHeaders(['token' => 'invalid-token'])
        ->postJson('/api/v2/notas', $this->scorePayload($data, ['nota' => 6]))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('stores discipline score', function () {
    $data = $this->createScoreData();

    $this->postScore($data, ['nota' => 6, 'recuperacao' => 2])
        ->assertStatus(201)
        ->assertJson(['message' => 'Nota salva com sucesso.']);

    $disciplineScore = LegacyDisciplineScore::query()->firstOrFail();

    $this->assertDatabaseHas($disciplineScore->registrationScore->getTable(), [
        'matricula_id' => $data['registration']->getKey(),
    ]);
    $this->assertDatabaseHas($disciplineScore->getTable(), [
        'nota_aluno_id' => $disciplineScore->registrationScore->getKey(),
        'componente_curricular_id' => $data['discipline']->getKey(),
        'nota' => 6,
        'nota_arredondada' => 6.0,
        'etapa' => 1,
        'nota_recuperacao' => 2,
        'nota_original' => 6,
        'nota_recuperacao_especifica' => null,
    ]);
    $this->assertDatabaseCount(LegacyGeneralScore::class, 0);
});

test('updates the score when posting the same stage twice', function () {
    $data = $this->createScoreData();

    $this->postScore($data, ['nota' => 6, 'recuperacao' => 2])->assertStatus(201);
    $this->postScore($data, ['nota' => 6, 'recuperacao' => 7])->assertStatus(201);

    $disciplineScore = LegacyDisciplineScore::query()->firstOrFail();

    $this->assertDatabaseHas($disciplineScore->getTable(), [
        'nota_aluno_id' => $disciplineScore->registrationScore->getKey(),
        'componente_curricular_id' => $data['discipline']->getKey(),
        'nota' => 7,
        'nota_arredondada' => 7.0,
        'etapa' => 1,
        'nota_recuperacao' => 7,
        'nota_original' => 6,
    ]);
    $this->assertDatabaseCount($disciplineScore->registrationScore->getTable(), 1);
    $this->assertDatabaseCount($disciplineScore->getTable(), 1);
});

test('rejects score above the rule maximum', function () {
    $data = $this->createScoreData();

    $this->postScore($data, ['nota' => 11, 'recuperacao' => null])
        ->assertStatus(422)
        ->assertJson([
            'message' => 'A nota 11 está acima da configurada para nota máxima geral que é 10.',
            'code' => 1003,
        ]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('accepts score equal to the rule maximum', function () {
    $data = $this->createScoreData();

    $this->postScore($data, ['nota' => 10, 'recuperacao' => null])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'nota' => 10,
        'etapa' => 1,
    ]);
});

test('rejects remedial score that raises the score above the maximum', function () {
    $data = $this->createScoreData();

    // Na recuperação por etapa com substituição, a maior nota prevalece,
    // então a validação é feita sobre a nota já calculada (11).
    $this->postScore($data, ['nota' => 6, 'recuperacao' => 11])
        ->assertStatus(422)
        ->assertJson([
            'message' => 'A nota 11 está acima da configurada para nota máxima geral que é 10.',
            'code' => 1003,
        ]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('accepts score above the maximum when rule sums the remedial score', function () {
    $data = $this->createScoreData([
        'tipo_recuperacao_paralela' => RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA,
        'tipo_calculo_recuperacao_paralela' => LegacyEvaluationRule::PARALLEL_REMEDIAL_SUM_SCORE,
    ]);

    $this->postScore($data, ['nota' => 6, 'recuperacao' => 5])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'nota' => 11,
        'nota_original' => 6,
        'nota_recuperacao' => 5,
        'etapa' => 1,
    ]);
});

test('rejects score below the rule minimum', function () {
    $data = $this->createScoreData(['nota_minima_geral' => 2]);

    $this->postScore($data, ['nota' => 1, 'recuperacao' => null])
        ->assertStatus(422)
        ->assertJson([
            'message' => 'A nota 1 está abaixo da configurada para nota mínima geral que é 2.',
            'code' => 1004,
        ]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('rejects exam score above the exam maximum', function () {
    $data = $this->createScoreData(['nota_maxima_exame_final' => 8]);

    $this->postScore($data, ['nota' => 9, 'recuperacao' => null, 'etapa' => 'Rc'])
        ->assertStatus(422)
        ->assertJson([
            'message' => 'A nota 9 está acima da configurada para nota máxima para exame que é 8.',
            'code' => 1005,
        ]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('exam score is not limited by the general maximum', function () {
    $data = $this->createScoreData([
        'nota_maxima_geral' => 10,
        'nota_maxima_exame_final' => 12,
    ]);

    $this->postScore($data, ['nota' => 11, 'recuperacao' => null, 'etapa' => 'Rc'])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'nota' => 11,
        'etapa' => 'Rc',
    ]);
});

test('does not store when previous stages have no scores', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 3);

    $response = $this->postScore($data, ['nota' => 6, 'recuperacao' => null, 'etapa' => 2])
        ->assertStatus(422);

    expect($response->json('message'))
        ->toContain('Nota somente pode ser lançada após lançar notas nas etapas: 1')
        ->and($response->json('code'))->toBe(1001)
        ->and($response->json('extra'))->toHaveKey('missing_stages');

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('stores score when previous stages have scores', function () {
    $data = $this->createScoreData();

    // O cenário padrão possui apenas a 1ª etapa; replica para existir a 2ª.
    LegacyAcademicYearStage::query()
        ->where('ref_ref_cod_escola', $data['school']->getKey())
        ->firstOrFail()
        ->replicate()
        ->fill(['sequencial' => 2])
        ->save();

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null, 'etapa' => 1])->assertStatus(201);
    $this->postScore($data, ['nota' => 7, 'recuperacao' => null, 'etapa' => 2])->assertStatus(201);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 2);
});

test('does not store when registration is not found', function () {
    $data = $this->createScoreData();
    $otherStudent = LegacyStudentFactory::new()->create();

    $this->postScore($data, [
        'nota' => 6,
        'recuperacao' => null,
        'aluno_id' => $otherStudent->getKey(),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['aluno_id']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('does not store when discipline does not belong to school class', function () {
    $data = $this->createScoreData();
    $otherDiscipline = LegacyDisciplineFactory::new()->create();

    $this->postScore($data, [
        'nota' => 6,
        'recuperacao' => null,
        'componente_id' => $otherDiscipline->getKey(),
    ])
        ->assertStatus(422)
        ->assertJson([
            'message' => "Componente curricular de código {$otherDiscipline->getKey()} não existe para a turma {$data['school_class']->getKey()}.",
            'code' => 1010,
            'extra' => [
                'school_class_id' => $data['school_class']->getKey(),
                'discipline_id' => $otherDiscipline->getKey(),
            ],
        ]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('rejects request without discipline', function () {
    $data = $this->createScoreData();

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null, 'componente_id' => null])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['componente_id']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('rejects score in scientific notation', function (string $field) {
    $data = $this->createScoreData();

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null, $field => '1e1'])
        ->assertStatus(422)
        ->assertJsonValidationErrors([$field]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
})->with(['nota', 'recuperacao']);

test('stores score with more than four decimal places truncated', function () {
    $data = $this->createScoreData();

    $this->postScore($data, ['nota' => '6.123456', 'recuperacao' => null])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'nota_original' => '6.1234',
    ]);
});

test('rejects stage in an invalid format', function () {
    $data = $this->createScoreData();

    $this->postScore($data, ['nota' => 6, 'etapa' => 'X'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['etapa']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('stores scores in stages above four when the school calendar has them', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 6);

    foreach (range(1, 6) as $stage) {
        $this->postScore($data, ['nota' => 6, 'recuperacao' => null, 'etapa' => $stage])->assertStatus(201);
    }

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 6);
    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'nota' => 6,
        'etapa' => '6',
    ]);
});

test('rejects stage greater than the school class stages', function (int $stage, int $postedStages) {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 3);

    foreach (range(1, $postedStages) as $postedStage) {
        $this->postScore($data, ['nota' => 6, 'recuperacao' => null, 'etapa' => $postedStage])->assertStatus(201);
    }

    // A etapa é validada antes da checagem de etapas anteriores, que recusaria com o código 1001.
    $this->postScore($data, ['nota' => 6, 'recuperacao' => null, 'etapa' => $stage])
        ->assertStatus(422)
        ->assertJson([
            'message' => "A etapa {$stage} não existe no calendário da turma {$data['school_class']->getKey()}, que possui 3 etapa(s).",
            'code' => 1012,
            'extra' => [
                'school_class_id' => $data['school_class']->getKey(),
                'stage' => $stage,
                'stages' => 3,
            ],
        ]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, $postedStages);
})->with([
    'etapa 4 com a 3ª etapa sem nota' => [4, 2],
    'etapa 99 com a 3ª etapa sem nota' => [99, 2],
    'etapa 4 com todas as etapas lançadas' => [4, 3],
]);

test('stores score of a discipline partially exempted and removes its exam score', function () {
    $data = $this->createScoreData();
    $registration = $data['registration'];
    $this->createSchoolCalendar($registration, $data['school_class'], 4);

    // Componente com dispensa em alguma etapa fica fora da lista de situações do boletim.
    $this->exemptDiscipline($data, [2]);

    LegacyScoreExamFactory::new()->create([
        'ref_cod_matricula' => $registration,
        'ref_cod_componente_curricular' => $data['discipline'],
    ]);

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null])
        ->assertStatus(201)
        ->assertJson(['message' => 'Nota salva com sucesso.']);

    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => 1,
        'nota_original' => 6,
    ]);
    $this->assertDatabaseMissing('modules.nota_exame', [
        'ref_cod_matricula' => $registration->getKey(),
        'ref_cod_componente_curricular' => $data['discipline']->getKey(),
    ]);
});

test('calculates the discipline average without the exempted stages', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 4);
    $this->exemptDiscipline($data, [2]);

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null])->assertStatus(201);

    // Fórmula Se / Et: 6 dividido pelas 3 etapas não dispensadas.
    $this->assertDatabaseHas(LegacyDisciplineScoreAverage::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'media' => 2,
    ]);
});

test('ignores exempted stages outside the school calendar when calculating the average', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 1);
    $this->exemptDiscipline($data, [2, 3, 4]);

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null])
        ->assertStatus(201)
        ->assertJson(['message' => 'Nota salva com sucesso.']);

    $this->assertDatabaseHas(LegacyDisciplineScoreAverage::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'media' => 6,
    ]);
});

test('discounts the exempted stage from the specific stages of the discipline', function () {
    $data = $this->createScoreData(['definir_componente_etapa' => 1]);
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 4);
    LegacyDisciplineSchoolClass::query()
        ->where('turma_id', $data['school_class']->getKey())
        ->where('componente_curricular_id', $data['discipline']->getKey())
        ->update(['etapas_especificas' => 1, 'etapas_utilizadas' => '3,4']);
    $this->exemptDiscipline($data, [3]);

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null, 'etapa' => 4])->assertStatus(201);

    // Fórmula Se / Et: o componente cursa as etapas 3 e 4, e a 3 está dispensada.
    $this->assertDatabaseHas(LegacyDisciplineScoreAverage::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'media' => 6,
    ]);
});

test('rejects score in a stage exempted for the registration', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 4);
    $this->exemptDiscipline($data, [2]);

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null])->assertStatus(201);

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null, 'etapa' => 2])
        ->assertStatus(422)
        ->assertJson([
            'message' => "O(a) aluno(a) da matrícula {$data['registration']->getKey()} está dispensado(a) do componente curricular de código {$data['discipline']->getKey()} na etapa 2.",
            'code' => 1014,
            'extra' => [
                'registration_id' => $data['registration']->getKey(),
                'discipline_id' => $data['discipline']->getKey(),
                'stage' => '2',
            ],
        ]);

    $this->assertDatabaseMissing(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => 2,
    ]);
    // A nota da etapa dispensada não entra na média: 6 dividido pelas 3 etapas não dispensadas.
    $this->assertDatabaseHas(LegacyDisciplineScoreAverage::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'media' => 2,
    ]);
});

test('rejects exam score of a discipline exempted in every stage', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 1);
    $this->exemptDiscipline($data, [1]);

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null, 'etapa' => 'Rc'])
        ->assertStatus(422)
        ->assertJson([
            'message' => "O(a) aluno(a) da matrícula {$data['registration']->getKey()} está dispensado(a) do componente curricular de código {$data['discipline']->getKey()} em todas as etapas e não pode receber nota de exame.",
            'code' => 1014,
            'extra' => ['stage' => 'Rc'],
        ]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('stores exam score of a discipline exempted only in some stages', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 2);
    $this->exemptDiscipline($data, [2]);

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null])->assertStatus(201);

    $this->postScore($data, ['nota' => 7, 'recuperacao' => null, 'etapa' => 'Rc'])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'nota' => 7,
        'etapa' => 'Rc',
    ]);
});

test('rejects score in a stage outside the specific stages of the discipline', function (string $configuredIn) {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 4);
    $specificStages = ['etapas_especificas' => 1, 'etapas_utilizadas' => '3,4'];

    $disciplineSchoolClass = LegacyDisciplineSchoolClass::query()
        ->where('turma_id', $data['school_class']->getKey())
        ->where('componente_curricular_id', $data['discipline']->getKey());

    if ($configuredIn === 'turma') {
        $disciplineSchoolClass->update($specificStages);
    } else {
        // Sem vínculo na turma, os componentes e as etapas vêm da série da escola.
        $disciplineSchoolClass->delete();
        LegacySchoolGradeDiscipline::query()
            ->where('ref_ref_cod_serie', $data['registration']->ref_ref_cod_serie)
            ->where('ref_cod_disciplina', $data['discipline']->getKey())
            ->update($specificStages);
    }

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null])
        ->assertStatus(422)
        ->assertJson(['code' => 1013, 'extra' => ['stage' => '1']]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
})->with([
    'etapas específicas na turma' => ['turma'],
    'etapas específicas na série da escola' => ['serie'],
]);

test('rejects exam score of a discipline exempted in every specific stage', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 4);
    LegacyDisciplineSchoolClass::query()
        ->where('turma_id', $data['school_class']->getKey())
        ->where('componente_curricular_id', $data['discipline']->getKey())
        ->update(['etapas_especificas' => 1, 'etapas_utilizadas' => '3,4']);
    $this->exemptDiscipline($data, [3, 4]);

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null, 'etapa' => 'Rc'])
        ->assertStatus(422)
        ->assertJson([
            'message' => "O(a) aluno(a) da matrícula {$data['registration']->getKey()} está dispensado(a) do componente curricular de código {$data['discipline']->getKey()} em todas as etapas e não pode receber nota de exame.",
            'code' => 1014,
            'extra' => ['stage' => 'Rc'],
        ]);
});

test('rejects score of a discipline outside the dependency of the registration', function () {
    $data = $this->createScoreData();
    $registration = $data['registration'];
    $registration->update(['dependencia' => true]);

    // A dependência é de outro componente da série, não do componente lançado.
    $schoolGradeDiscipline = LegacySchoolGradeDiscipline::query()
        ->where('ref_ref_cod_serie', $registration->ref_ref_cod_serie)
        ->where('ref_cod_disciplina', $data['discipline']->getKey())
        ->firstOrFail();
    $dependencyDiscipline = LegacyDisciplineFactory::new()->create();
    LegacySchoolGradeDisciplineFactory::new()->create([
        'ref_ref_cod_escola' => $registration->ref_ref_cod_escola,
        'ref_ref_cod_serie' => $registration->ref_ref_cod_serie,
        'ref_cod_disciplina' => $dependencyDiscipline,
        'anos_letivos' => $schoolGradeDiscipline->anos_letivos,
    ]);

    LegacyDisciplineDependenceFactory::new()->create([
        'ref_cod_matricula' => $registration,
        'ref_cod_disciplina' => $dependencyDiscipline,
        'ref_cod_escola' => $registration->ref_ref_cod_escola,
        'ref_cod_serie' => $registration->ref_ref_cod_serie,
    ]);

    $this->postScore($data, ['nota' => 6, 'recuperacao' => null])
        ->assertStatus(422)
        ->assertJson(['code' => 1013]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('unexpected failure answers 500 with the reason', function () {
    $data = $this->createScoreData();
    $service = Mockery::mock(DiarioControllerService::class)->makePartial();
    $service->shouldReceive('getServiceBoletim')->andThrow(new Exception('falha inesperada'));
    app()->instance(DiarioControllerService::class, $service);

    $response = $this->postScore($data, ['nota' => 6]);

    $response->assertStatus(500);
    expect($response->json('message'))->toEndWith('(falha inesperada)');
});
