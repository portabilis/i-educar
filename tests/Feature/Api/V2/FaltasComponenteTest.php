<?php

use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyGeneralAbsence;
use App\Services\DiarioControllerService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\V2\CreatesAbsenceData;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class, CreatesAbsenceData::class);

test('rejects request without token', function () {
    $this->institutionWithToken();

    $this->postJson('/api/v2/falta-componente', [])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);
});

test('rejects request with invalid token', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'wrong-token'])
        ->postJson('/api/v2/falta-componente', [])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);
});

test('requires mandatory fields', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['faltas', 'etapa', 'aluno_id', 'turma_id', 'componente_id']);
});

test('rejects negative absences', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', [
            'etapa' => 1,
            'faltas' => -1,
            'aluno_id' => 1,
            'turma_id' => 1,
            'componente_id' => 1,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['faltas']);
});

test('rejects stage lower than one', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', [
            'etapa' => 0,
            'faltas' => 2,
            'aluno_id' => 1,
            'turma_id' => 1,
            'componente_id' => 1,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['etapa']);
});

test('rejects non integer knowledge area', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', [
            'etapa' => 1,
            'faltas' => 2,
            'aluno_id' => 1,
            'turma_id' => 1,
            'componente_id' => 1,
            'area_do_conhecimento_id' => 'abc',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['area_do_conhecimento_id']);
});

test('does not store when registration is not found', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', [
            'etapa' => 1,
            'faltas' => 2,
            'aluno_id' => 99999999,
            'turma_id' => 99999999,
            'componente_id' => 99999999,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'aluno_id' => 'Matrícula não encontrada para o aluno e turma informados.',
        ]);

    $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 0);
});

test('stores discipline absence when rule is by component', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data))
        ->assertStatus(201)
        ->assertJson(['message' => 'Falta por componente salva com sucesso.']);

    $absence = LegacyDisciplineAbsence::query()->firstOrFail();

    $this->assertDatabaseHas($absence->studentAbsence->getTable(), [
        'matricula_id' => $data['registration']->getKey(),
        'tipo_falta' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
    ]);
    $this->assertDatabaseHas($absence->getTable(), [
        'falta_aluno_id' => $absence->studentAbsence->getKey(),
        'componente_curricular_id' => $data['discipline']->getKey(),
        'quantidade' => 2,
        'etapa' => 1,
    ]);
    $this->assertDatabaseCount($absence->studentAbsence->getTable(), 1);
    $this->assertDatabaseCount($absence->getTable(), 1);
    $this->assertDatabaseCount(LegacyGeneralAbsence::class, 0);
});

test('accepts zero absences', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data, ['faltas' => 0]))
        ->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineAbsence::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'quantidade' => 0,
        'etapa' => 1,
    ]);
    $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 1);
});

test('updates the amount when posting the same stage twice', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data, ['faltas' => 2]))
        ->assertStatus(201);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data, ['faltas' => 5]))
        ->assertStatus(201);

    $absence = LegacyDisciplineAbsence::query()->firstOrFail();

    $this->assertDatabaseHas($absence->getTable(), [
        'falta_aluno_id' => $absence->studentAbsence->getKey(),
        'componente_curricular_id' => $data['discipline']->getKey(),
        'quantidade' => 5,
        'etapa' => 1,
    ]);
    $this->assertDatabaseCount($absence->getTable(), 1);
    $this->assertDatabaseCount($absence->studentAbsence->getTable(), 1);
});

test('does not allow discipline absence when rule is general', function () {
    $data = $this->createAbsenceData();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data))
        ->assertStatus(422)
        ->assertJson([
            'message' => "A regra da turma {$data['turma_id']} não permite lançamento de faltas por componente.",
            'code' => 1011,
            'extra' => ['school_class_id' => $data['turma_id']],
        ]);

    $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 0);
});

test('does not store when discipline does not belong to school class', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data, ['componente_id' => 99999999]))
        ->assertStatus(422)
        ->assertJson([
            'message' => "Componente curricular de código 99999999 não existe para a turma {$data['turma_id']}.",
            'code' => 1010,
        ]);

    $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 0);
});

test('distributes absences among grouped disciplines of the knowledge area', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);
    $knowledgeArea = $this->createGroupedKnowledgeArea();

    $disciplines = collect([
        $this->addDisciplineToSchoolClass($data['school_class'], $data['school_grade'], $knowledgeArea),
        $this->addDisciplineToSchoolClass($data['school_class'], $data['school_grade'], $knowledgeArea),
    ])->sortBy('nome')->values();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data, [
            'faltas' => 3,
            'area_do_conhecimento_id' => $knowledgeArea->getKey(),
        ]))
        ->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineAbsence::class, [
        'componente_curricular_id' => $disciplines[0]->getKey(),
        'quantidade' => 3,
        'etapa' => 1,
    ]);
    $this->assertDatabaseHas(LegacyDisciplineAbsence::class, [
        'componente_curricular_id' => $disciplines[1]->getKey(),
        'quantidade' => 0,
        'etapa' => 1,
    ]);
    $this->assertDatabaseMissing(LegacyDisciplineAbsence::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
    ]);
    $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 2);
});

test('does not store when knowledge area has no grouped disciplines in school class', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data, [
            'area_do_conhecimento_id' => $data['discipline']->area_conhecimento_id,
        ]))
        ->assertStatus(422)
        ->assertJson([
            'message' => 'Não foi possível encontrar componentes dessa turma agrupados para a área do conhecimento informada.',
        ]);

    $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 0);
});

test('distributes absences by knowledge area without informing the discipline', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);
    $knowledgeArea = $this->createGroupedKnowledgeArea();
    $discipline = $this->addDisciplineToSchoolClass($data['school_class'], $data['school_grade'], $knowledgeArea);

    $payload = $this->generalAbsencePayload($data, [
        'faltas' => 3,
        'area_do_conhecimento_id' => $knowledgeArea->getKey(),
    ]);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $payload)
        ->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineAbsence::class, [
        'componente_curricular_id' => $discipline->getKey(),
        'quantidade' => 3,
        'etapa' => 1,
    ]);
    $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 1);
});

test('requires the discipline when knowledge area is not informed', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->generalAbsencePayload($data))
        ->assertStatus(422)
        ->assertJsonValidationErrors(['componente_id']);
});

test('rejects stage outside the school calendar', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 4);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data, ['etapa' => 5]))
        ->assertStatus(422)
        ->assertJson(['message' => 'A etapa informada é inválida.']);

    $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 0);
});

test('stores absence in stages above four when the school calendar has them', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 6);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data, ['etapa' => 6]))
        ->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineAbsence::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'quantidade' => 2,
        'etapa' => 6,
    ]);
});

test('stores absence of each grade in a multigrade school class', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);
    $secondGrade = $this->addSecondGradeToSchoolClass($data);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data))
        ->assertStatus(201);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data, [
            'aluno_id' => $secondGrade['aluno_id'],
            'componente_id' => $secondGrade['discipline']->getKey(),
        ]))
        ->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineAbsence::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'quantidade' => 2,
    ]);
    $this->assertDatabaseHas(LegacyDisciplineAbsence::class, [
        'componente_curricular_id' => $secondGrade['discipline']->getKey(),
        'quantidade' => 2,
    ]);
    $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 2);
});

test('rejects discipline of another grade in a multigrade school class', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);
    $secondGrade = $this->addSecondGradeToSchoolClass($data);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data, [
            'componente_id' => $secondGrade['discipline']->getKey(),
        ]))
        ->assertStatus(422)
        ->assertJson([
            'message' => "Componente curricular de código {$secondGrade['discipline']->getKey()} não existe para a turma {$data['turma_id']}.",
            'code' => 1010,
        ]);

    $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 0);
});

test('unexpected failure answers 500 with the reason', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);
    $service = Mockery::mock(DiarioControllerService::class)->makePartial();
    $service->shouldReceive('getServiceBoletim')->andThrow(new Exception('falha inesperada'));
    app()->instance(DiarioControllerService::class, $service);

    $response = $this->withHeaders(['token' => 'valid-token'])->postJson('/api/v2/falta-componente', $this->disciplineAbsencePayload($data));

    $response->assertStatus(500);
    expect($response->json('message'))->toEndWith('(falha inesperada)');
});
