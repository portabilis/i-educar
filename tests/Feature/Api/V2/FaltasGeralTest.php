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

    $this->postJson('/api/v2/falta-geral', [])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);
});

test('rejects request with invalid token', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'wrong-token'])
        ->postJson('/api/v2/falta-geral', [])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);
});

test('requires mandatory fields', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['faltas', 'etapa', 'aluno_id', 'turma_id']);
});

test('rejects negative absences', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', [
            'etapa' => 1,
            'faltas' => -1,
            'aluno_id' => 1,
            'turma_id' => 1,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['faltas']);
});

test('rejects stage lower than one', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', [
            'etapa' => 0,
            'faltas' => 2,
            'aluno_id' => 1,
            'turma_id' => 1,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['etapa']);
});

test('stores general absence when rule is general attendance', function () {
    $data = $this->createAbsenceData();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data))
        ->assertStatus(201)
        ->assertJson(['message' => 'Faltas gerais salvas com sucesso.']);

    $absence = LegacyGeneralAbsence::query()->firstOrFail();

    $this->assertDatabaseHas($absence->studentAbsence->getTable(), [
        'matricula_id' => $data['registration']->getKey(),
        'tipo_falta' => RegraAvaliacao_Model_TipoPresenca::GERAL,
    ]);
    $this->assertDatabaseHas($absence->getTable(), [
        'falta_aluno_id' => $absence->studentAbsence->getKey(),
        'quantidade' => 2,
        'etapa' => 1,
    ]);
    $this->assertDatabaseCount($absence->studentAbsence->getTable(), 1);
    $this->assertDatabaseCount($absence->getTable(), 1);
    $this->assertDatabaseCount(LegacyDisciplineAbsence::class, 0);
});

test('accepts zero absences', function () {
    $data = $this->createAbsenceData();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data, ['faltas' => 0]))
        ->assertStatus(201);

    $absence = LegacyGeneralAbsence::query()->firstOrFail();

    $this->assertDatabaseHas($absence->getTable(), [
        'falta_aluno_id' => $absence->studentAbsence->getKey(),
        'quantidade' => 0,
        'etapa' => 1,
    ]);
    $this->assertDatabaseCount($absence->getTable(), 1);
});

test('updates the amount when posting the same stage twice', function () {
    $data = $this->createAbsenceData();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data, ['faltas' => 2]))
        ->assertStatus(201);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data, ['faltas' => 5]))
        ->assertStatus(201);

    $absence = LegacyGeneralAbsence::query()->firstOrFail();

    $this->assertDatabaseHas($absence->getTable(), [
        'falta_aluno_id' => $absence->studentAbsence->getKey(),
        'quantidade' => 5,
        'etapa' => 1,
    ]);
    $this->assertDatabaseCount($absence->getTable(), 1);
    $this->assertDatabaseCount($absence->studentAbsence->getTable(), 1);
});

test('does not allow general absence when rule is by component', function () {
    $data = $this->createAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data))
        ->assertStatus(422)
        ->assertJson([
            'message' => "A regra da turma {$data['turma_id']} não permite lançamento de faltas geral.",
            'code' => 1008,
        ]);

    $this->assertDatabaseCount(LegacyGeneralAbsence::class, 0);
});

test('does not store when registration is not found', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', [
            'etapa' => 1,
            'faltas' => 2,
            'aluno_id' => 99999999,
            'turma_id' => 99999999,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'aluno_id' => 'Matrícula não encontrada para o aluno e turma informados.',
        ]);

    $this->assertDatabaseCount(LegacyGeneralAbsence::class, 0);
});

test('rejects stage outside the school calendar', function () {
    $data = $this->createAbsenceData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 4);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data, ['etapa' => 5]))
        ->assertStatus(422)
        ->assertJson(['message' => 'A etapa informada é inválida.']);

    $this->assertDatabaseCount(LegacyGeneralAbsence::class, 0);
});

test('stores absence in stages above four when the school calendar has them', function () {
    $data = $this->createAbsenceData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 6);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data, ['etapa' => 6]))
        ->assertStatus(201);

    $this->assertDatabaseHas(LegacyGeneralAbsence::class, [
        'quantidade' => 2,
        'etapa' => 6,
    ]);
});

test('unexpected failure answers 500 with the reason', function () {
    $data = $this->createAbsenceData();
    $service = Mockery::mock(DiarioControllerService::class)->makePartial();
    $service->shouldReceive('getServiceBoletim')->andThrow(new Exception('falha inesperada'));
    app()->instance(DiarioControllerService::class, $service);

    $response = $this->withHeaders(['token' => 'valid-token'])->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data));

    $response->assertStatus(500);
    expect($response->json('message'))->toEndWith('(falha inesperada)');
});

test('rejects registration whose enrollment is in an inactive school class', function () {
    $data = $this->createAbsenceData();
    $data['school_class']->update(['ativo' => 0]);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data))
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'aluno_id' => 'Aluno não enturmado.',
        ]);

    $this->assertDatabaseCount(LegacyGeneralAbsence::class, 0);
});
