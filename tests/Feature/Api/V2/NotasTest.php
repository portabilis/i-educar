<?php

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyDisciplineScore;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralScore;
use App\Services\DiarioControllerService;
use Database\Factories\LegacyDisciplineFactory;
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

test('unexpected failure answers 500 with the reason', function () {
    $data = $this->createScoreData();
    $service = Mockery::mock(DiarioControllerService::class)->makePartial();
    $service->shouldReceive('getServiceBoletim')->andThrow(new Exception('falha inesperada'));
    app()->instance(DiarioControllerService::class, $service);

    $response = $this->postScore($data, ['nota' => 6]);

    $response->assertStatus(500);
    expect($response->json('message'))->toEndWith('(falha inesperada)');
});
