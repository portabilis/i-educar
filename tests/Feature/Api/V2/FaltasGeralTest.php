<?php

use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyGeneralAbsence;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\V2\CreatesGeneralAbsenceData;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class, CreatesGeneralAbsenceData::class);

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

test('rejects stage out of range', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', [
            'etapa' => 5,
            'faltas' => 2,
            'aluno_id' => 1,
            'turma_id' => 1,
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['etapa']);
});

test('stores general absence when rule is general attendance', function () {
    $data = $this->createGeneralAbsenceData();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data))
        ->assertStatus(202)
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
    $data = $this->createGeneralAbsenceData();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data, ['faltas' => 0]))
        ->assertStatus(202);

    $absence = LegacyGeneralAbsence::query()->firstOrFail();

    $this->assertDatabaseHas($absence->getTable(), [
        'falta_aluno_id' => $absence->studentAbsence->getKey(),
        'quantidade' => 0,
        'etapa' => 1,
    ]);
    $this->assertDatabaseCount($absence->getTable(), 1);
});

test('updates the amount when posting the same stage twice', function () {
    $data = $this->createGeneralAbsenceData();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data, ['faltas' => 2]))
        ->assertStatus(202);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data, ['faltas' => 5]))
        ->assertStatus(202);

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
    $data = $this->createGeneralAbsenceData(RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE);

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/falta-geral', $this->generalAbsencePayload($data))
        ->assertStatus(422)
        ->assertJson([
            'message' => "A regra da turma {$data['turma_id']} não permite lançamento de faltas geral.",
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
        ->assertStatus(200)
        ->assertJson(['message' => 'Matrícula não encontrada para o aluno e turma informados.']);

    $this->assertDatabaseCount(LegacyGeneralAbsence::class, 0);
});
