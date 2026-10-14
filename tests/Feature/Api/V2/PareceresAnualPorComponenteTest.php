<?php

use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Services\DiarioControllerService;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\V2\CreatesScoreData;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class, CreatesScoreData::class);

function annualDisciplineOpinionRule(): array
{
    return ['parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE];
}

test('rejects request without token', function () {
    $data = $this->createScoreData(annualDisciplineOpinionRule());

    $this->postJson('/api/v2/pareceres-anual-por-componente', $this->scorePayload($data, ['parecer' => 'Parecer']))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);
});

test('rejects request with invalid token', function () {
    $data = $this->createScoreData(annualDisciplineOpinionRule());

    $this->withHeaders(['token' => 'invalid-token'])
        ->postJson('/api/v2/pareceres-anual-por-componente', $this->scorePayload($data, ['parecer' => 'Parecer']))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);
});

test('requires mandatory fields', function () {
    $this->createScoreData(annualDisciplineOpinionRule());

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/pareceres-anual-por-componente', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['parecer', 'aluno_id', 'turma_id', 'componente_id']);
});

test('stores the annual discipline opinion', function () {
    $data = $this->createScoreData(annualDisciplineOpinionRule());

    $this->postAnnualDisciplineOpinion($data, ['parecer' => 'O aluno evoluiu bem no ano.'])
        ->assertStatus(201)
        ->assertJson(['message' => 'Parecer salvo com sucesso.']);

    $this->assertDatabaseHas(LegacyDisciplineDescriptiveOpinion::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => 'An',
        'parecer' => 'O aluno evoluiu bem no ano.',
    ]);
});

test('updates the annual opinion already stored', function () {
    $data = $this->createScoreData(annualDisciplineOpinionRule());

    $this->postAnnualDisciplineOpinion($data, ['parecer' => 'Primeiro parecer'])->assertStatus(201);
    $this->postAnnualDisciplineOpinion($data, ['parecer' => 'Parecer revisado'])->assertStatus(201);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 1);
    $this->assertDatabaseHas(LegacyDisciplineDescriptiveOpinion::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => 'An',
        'parecer' => 'Parecer revisado',
    ]);
});

test('clears the opinion when it is empty', function () {
    $data = $this->createScoreData(annualDisciplineOpinionRule());

    $this->postAnnualDisciplineOpinion($data, ['parecer' => 'Primeiro parecer'])->assertStatus(201);
    $this->postAnnualDisciplineOpinion($data, ['parecer' => ''])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineDescriptiveOpinion::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => 'An',
        'parecer' => null,
    ]);
});

test('removes html styles from the opinion', function () {
    $data = $this->createScoreData(annualDisciplineOpinionRule());

    $this->postAnnualDisciplineOpinion($data, [
        'parecer' => '<p style="color: red">Parecer <font face="Arial">formatado</font></p>',
    ])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineDescriptiveOpinion::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'parecer' => '<p >Parecer formatado</p>',
    ]);
});

test('rejects opinion when the rule does not allow annual discipline opinions', function () {
    $data = $this->createScoreData([
        'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
    ]);

    $this->postAnnualDisciplineOpinion($data, ['parecer' => 'Parecer'])
        ->assertStatus(422)
        ->assertJson([
            'message' => "A regra da turma {$data['school_class']->getKey()} não permite lançamento de pareceres anual por componente.",
        ]);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);
});

test('does not store when registration is not found', function () {
    $data = $this->createScoreData(annualDisciplineOpinionRule());
    $otherStudent = LegacyStudentFactory::new()->create();

    $this->postAnnualDisciplineOpinion($data, [
        'parecer' => 'Parecer',
        'aluno_id' => $otherStudent->getKey(),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['aluno_id']);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);
});

test('does not store when discipline does not belong to the registration', function () {
    $data = $this->createScoreData(annualDisciplineOpinionRule());
    $otherDiscipline = LegacyDisciplineFactory::new()->create();

    $this->postAnnualDisciplineOpinion($data, [
        'parecer' => 'Parecer',
        'componente_id' => $otherDiscipline->getKey(),
    ])
        ->assertStatus(422)
        ->assertJson([
            'message' => "Componente curricular de código {$otherDiscipline->getKey()} não existe para essa turma/matrícula.",
        ]);

    $this->assertDatabaseCount(LegacyDisciplineDescriptiveOpinion::class, 0);
});

test('unexpected failure answers 500 with the reason', function () {
    $data = $this->createScoreData(annualDisciplineOpinionRule());
    $service = Mockery::mock(DiarioControllerService::class)->makePartial();
    $service->shouldReceive('getServiceBoletim')->andThrow(new Exception('falha inesperada'));
    app()->instance(DiarioControllerService::class, $service);

    $response = $this->postAnnualDisciplineOpinion($data, ['parecer' => 'Bom']);

    $response->assertStatus(500);
    expect($response->json('message'))->toEndWith('(falha inesperada)');
});
