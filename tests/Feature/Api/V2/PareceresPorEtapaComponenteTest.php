<?php

use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Services\DiarioControllerService;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\V2\CreatesScoreData;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class, CreatesScoreData::class);

function stageDisciplineOpinionRule(): array
{
    return ['parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE];
}

test('rejects request without token', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());

    $this->postJson('/api/v2/pareceres-por-etapa-e-componente', $this->scorePayload($data, ['parecer' => 'Parecer']))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);
});

test('rejects request with invalid token', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());

    $this->withHeaders(['token' => 'invalid-token'])
        ->postJson('/api/v2/pareceres-por-etapa-e-componente', $this->scorePayload($data, ['parecer' => 'Parecer']))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);
});

test('requires mandatory fields', function () {
    $this->createScoreData(stageDisciplineOpinionRule());

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/pareceres-por-etapa-e-componente', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['parecer', 'etapa', 'aluno_id', 'turma_id', 'componente_id']);
});

test('rejects invalid stage', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());

    $this->postStageDisciplineOpinion($data, ['parecer' => 'Parecer', 'etapa' => 'X'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['etapa']);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);
});

test('stores the stage discipline opinion', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());

    $this->postStageDisciplineOpinion($data, ['parecer' => 'O aluno evoluiu bem na etapa.'])
        ->assertStatus(201)
        ->assertJson(['message' => 'Parecer salvo com sucesso.']);

    $this->assertDatabaseHas(LegacyDisciplineDescriptiveOpinion::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => '1',
        'parecer' => 'O aluno evoluiu bem na etapa.',
    ]);
});

test('updates the opinion already stored for the stage', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());

    $this->postStageDisciplineOpinion($data, ['parecer' => 'Primeiro parecer'])->assertStatus(201);
    $this->postStageDisciplineOpinion($data, ['parecer' => 'Parecer revisado'])->assertStatus(201);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 1);
    $this->assertDatabaseHas(LegacyDisciplineDescriptiveOpinion::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => '1',
        'parecer' => 'Parecer revisado',
    ]);
});

test('clears the opinion when it is empty', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());

    $this->postStageDisciplineOpinion($data, ['parecer' => 'Primeiro parecer'])->assertStatus(201);
    $this->postStageDisciplineOpinion($data, ['parecer' => ''])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineDescriptiveOpinion::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => '1',
        'parecer' => null,
    ]);
});

test('removes html styles from the opinion', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());

    $this->postStageDisciplineOpinion($data, [
        'parecer' => '<p style="color: red">Parecer <font face="Arial">formatado</font></p>',
    ])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineDescriptiveOpinion::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'parecer' => '<p >Parecer formatado</p>',
    ]);
});

test('rejects opinion when the rule does not allow stage discipline opinions', function () {
    $data = $this->createScoreData([
        'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE,
    ]);

    $this->postStageDisciplineOpinion($data, ['parecer' => 'Parecer'])
        ->assertStatus(422)
        ->assertJson([
            'message' => "A regra da turma {$data['school_class']->getKey()} não permite lançamento de pareceres por etapa e componente.",
        ]);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);
});

test('does not store when registration is not found', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());
    $otherStudent = LegacyStudentFactory::new()->create();

    $this->postStageDisciplineOpinion($data, [
        'parecer' => 'Parecer',
        'aluno_id' => $otherStudent->getKey(),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['aluno_id']);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);
});

test('does not store when discipline does not belong to school class', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());
    $otherDiscipline = LegacyDisciplineFactory::new()->create();

    $this->postStageDisciplineOpinion($data, [
        'parecer' => 'Parecer',
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

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);
});

// O boletim não confere a etapa do parecer com o calendário; o resource legado
// também aceitava qualquer etapa.
test('accepts stage outside the school calendar like the legacy resource', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 4);

    $this->postStageDisciplineOpinion($data, ['parecer' => 'Parecer', 'etapa' => 5])
        ->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineDescriptiveOpinion::class, ['etapa' => '5']);
});

test('stores opinion in stages above four when the school calendar has them', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 6);

    $this->postStageDisciplineOpinion($data, ['parecer' => 'Parecer da etapa 6', 'etapa' => 6])
        ->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineDescriptiveOpinion::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => '6',
        'parecer' => 'Parecer da etapa 6',
    ]);
});

test('unexpected failure answers 500 with the reason', function () {
    $data = $this->createScoreData(stageDisciplineOpinionRule());
    $service = Mockery::mock(DiarioControllerService::class)->makePartial();
    $service->shouldReceive('getServiceBoletim')->andThrow(new Exception('falha inesperada'));
    app()->instance(DiarioControllerService::class, $service);

    $response = $this->postStageDisciplineOpinion($data, ['parecer' => 'Bom']);

    $response->assertStatus(500);
    expect($response->json('message'))->toEndWith('(falha inesperada)');
});
