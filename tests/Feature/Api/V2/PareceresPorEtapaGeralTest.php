<?php

use App\Models\LegacyGeneralDescriptiveOpinion;
use App\Services\DiarioControllerService;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\V2\CreatesScoreData;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class, CreatesScoreData::class);

function stageGeneralOpinionRule(): array
{
    return ['parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL];
}

test('rejects request without token', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());

    $this->postJson('/api/v2/pareceres-por-etapa-geral', $this->scorePayload($data, ['parecer' => 'Parecer']))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 0);
});

test('rejects request with invalid token', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());

    $this->withHeaders(['token' => 'invalid-token'])
        ->postJson('/api/v2/pareceres-por-etapa-geral', $this->scorePayload($data, ['parecer' => 'Parecer']))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 0);
});

test('requires mandatory fields', function () {
    $this->createScoreData(stageGeneralOpinionRule());

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/pareceres-por-etapa-geral', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['parecer', 'etapa', 'aluno_id', 'turma_id']);
});

test('rejects invalid stage', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());

    $this->postStageGeneralOpinion($data, ['parecer' => 'Parecer', 'etapa' => 'X'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['etapa']);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 0);
});

test('stores the stage general opinion', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());

    $this->postStageGeneralOpinion($data, ['parecer' => 'O aluno evoluiu bem na etapa.'])
        ->assertStatus(201)
        ->assertJson(['message' => 'Parecer salvo com sucesso.']);

    $this->assertDatabaseHas(LegacyGeneralDescriptiveOpinion::class, [
        'etapa' => '1',
        'parecer' => 'O aluno evoluiu bem na etapa.',
    ]);
});

test('updates the opinion already stored for the stage', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());

    $this->postStageGeneralOpinion($data, ['parecer' => 'Primeiro parecer'])->assertStatus(201);
    $this->postStageGeneralOpinion($data, ['parecer' => 'Parecer revisado'])->assertStatus(201);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 1);
    $this->assertDatabaseHas(LegacyGeneralDescriptiveOpinion::class, [
        'etapa' => '1',
        'parecer' => 'Parecer revisado',
    ]);
});

test('stores one opinion per stage', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());

    $this->postStageGeneralOpinion($data, ['parecer' => 'Parecer da etapa 1'])->assertStatus(201);
    $this->postStageGeneralOpinion($data, ['parecer' => 'Parecer da etapa 2', 'etapa' => 2])->assertStatus(201);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 2);
    $this->assertDatabaseHas(LegacyGeneralDescriptiveOpinion::class, [
        'etapa' => '2',
        'parecer' => 'Parecer da etapa 2',
    ]);
});

test('clears the opinion when it is empty', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());

    $this->postStageGeneralOpinion($data, ['parecer' => 'Primeiro parecer'])->assertStatus(201);
    $this->postStageGeneralOpinion($data, ['parecer' => ''])->assertStatus(201);

    $this->assertDatabaseHas(LegacyGeneralDescriptiveOpinion::class, [
        'etapa' => '1',
        'parecer' => null,
    ]);
});

test('removes html styles from the opinion', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());

    $this->postStageGeneralOpinion($data, [
        'parecer' => '<p style="color: red">Parecer <font face="Arial">formatado</font></p>',
    ])->assertStatus(201);

    $this->assertDatabaseHas(LegacyGeneralDescriptiveOpinion::class, [
        'parecer' => '<p >Parecer formatado</p>',
    ]);
});

test('rejects opinion when the rule does not allow stage general opinions', function () {
    $data = $this->createScoreData([
        'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
    ]);

    $this->postStageGeneralOpinion($data, ['parecer' => 'Parecer'])
        ->assertStatus(422)
        ->assertJson([
            'message' => "A regra da turma {$data['school_class']->getKey()} não permite lançamento de pareceres por etapa geral.",
        ]);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 0);
});

test('does not store when registration is not found', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());
    $otherStudent = LegacyStudentFactory::new()->create();

    $this->postStageGeneralOpinion($data, [
        'parecer' => 'Parecer',
        'aluno_id' => $otherStudent->getKey(),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['aluno_id']);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 0);
});

// O boletim não confere a etapa do parecer com o calendário; o resource legado
// também aceitava qualquer etapa.
test('accepts stage outside the school calendar like the legacy resource', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 4);

    $this->postStageGeneralOpinion($data, ['parecer' => 'Parecer', 'etapa' => 5])
        ->assertStatus(201);

    $this->assertDatabaseHas(LegacyGeneralDescriptiveOpinion::class, ['etapa' => '5']);
});

test('stores opinion in stages above four when the school calendar has them', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 6);

    $this->postStageGeneralOpinion($data, ['parecer' => 'Parecer da etapa 6', 'etapa' => 6])
        ->assertStatus(201);

    $this->assertDatabaseHas(LegacyGeneralDescriptiveOpinion::class, [
        'etapa' => '6',
        'parecer' => 'Parecer da etapa 6',
    ]);
});

test('unexpected failure answers 500 with the reason', function () {
    $data = $this->createScoreData(stageGeneralOpinionRule());
    $service = Mockery::mock(DiarioControllerService::class)->makePartial();
    $service->shouldReceive('getServiceBoletim')->andThrow(new Exception('falha inesperada'));
    app()->instance(DiarioControllerService::class, $service);

    $response = $this->postStageGeneralOpinion($data, ['parecer' => 'Bom']);

    $response->assertStatus(500);
    expect($response->json('message'))->toEndWith('(falha inesperada)');
});
