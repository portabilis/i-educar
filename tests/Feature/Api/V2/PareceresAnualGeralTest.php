<?php

use App\Models\LegacyGeneralDescriptiveOpinion;
use App\Services\DiarioControllerService;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\V2\CreatesScoreData;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class, CreatesScoreData::class);

function annualGeneralOpinionRule(): array
{
    return ['parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL];
}

test('rejects request without token', function () {
    $data = $this->createScoreData(annualGeneralOpinionRule());

    $this->postJson('/api/v2/pareceres-anual-geral', $this->scorePayload($data, ['parecer' => 'Parecer']))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 0);
});

test('rejects request with invalid token', function () {
    $data = $this->createScoreData(annualGeneralOpinionRule());

    $this->withHeaders(['token' => 'invalid-token'])
        ->postJson('/api/v2/pareceres-anual-geral', $this->scorePayload($data, ['parecer' => 'Parecer']))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 0);
});

test('requires mandatory fields', function () {
    $this->createScoreData(annualGeneralOpinionRule());

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/pareceres-anual-geral', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['parecer', 'aluno_id', 'turma_id']);
});

test('stores the annual general opinion', function () {
    $data = $this->createScoreData(annualGeneralOpinionRule());

    $this->postAnnualGeneralOpinion($data, ['parecer' => 'O aluno evoluiu bem no ano.'])
        ->assertStatus(201)
        ->assertJson(['message' => 'Parecer salvo com sucesso.']);

    $this->assertDatabaseHas(LegacyGeneralDescriptiveOpinion::class, [
        'etapa' => 'An',
        'parecer' => 'O aluno evoluiu bem no ano.',
    ]);
});

test('updates the annual opinion already stored', function () {
    $data = $this->createScoreData(annualGeneralOpinionRule());

    $this->postAnnualGeneralOpinion($data, ['parecer' => 'Primeiro parecer'])->assertStatus(201);
    $this->postAnnualGeneralOpinion($data, ['parecer' => 'Parecer revisado'])->assertStatus(201);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 1);
    $this->assertDatabaseHas(LegacyGeneralDescriptiveOpinion::class, [
        'etapa' => 'An',
        'parecer' => 'Parecer revisado',
    ]);
});

test('clears the opinion when it is empty', function () {
    $data = $this->createScoreData(annualGeneralOpinionRule());

    $this->postAnnualGeneralOpinion($data, ['parecer' => 'Primeiro parecer'])->assertStatus(201);
    $this->postAnnualGeneralOpinion($data, ['parecer' => ''])->assertStatus(201);

    $this->assertDatabaseHas(LegacyGeneralDescriptiveOpinion::class, [
        'etapa' => 'An',
        'parecer' => null,
    ]);
});

test('removes html styles from the opinion', function () {
    $data = $this->createScoreData(annualGeneralOpinionRule());

    $this->postAnnualGeneralOpinion($data, [
        'parecer' => '<p style="color: red">Parecer <font face="Arial">formatado</font></p>',
    ])->assertStatus(201);

    $this->assertDatabaseHas(LegacyGeneralDescriptiveOpinion::class, [
        'parecer' => '<p >Parecer formatado</p>',
    ]);
});

test('rejects opinion when the rule does not allow annual general opinions', function () {
    $data = $this->createScoreData([
        'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
    ]);

    $this->postAnnualGeneralOpinion($data, ['parecer' => 'Parecer'])
        ->assertStatus(422)
        ->assertJson([
            'message' => "A regra da turma {$data['school_class']->getKey()} não permite lançamento de pareceres anual geral.",
        ]);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 0);
});

test('does not store when registration is not found', function () {
    $data = $this->createScoreData(annualGeneralOpinionRule());
    $otherStudent = LegacyStudentFactory::new()->create();

    $this->postAnnualGeneralOpinion($data, [
        'parecer' => 'Parecer',
        'aluno_id' => $otherStudent->getKey(),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['aluno_id']);

    $this->assertDatabaseCount(LegacyGeneralDescriptiveOpinion::class, 0);
});

test('unexpected failure answers 500 with the reason', function () {
    $data = $this->createScoreData(annualGeneralOpinionRule());
    $service = Mockery::mock(DiarioControllerService::class)->makePartial();
    $service->shouldReceive('getServiceBoletim')->andThrow(new Exception('falha inesperada'));
    app()->instance(DiarioControllerService::class, $service);

    $response = $this->postAnnualGeneralOpinion($data, ['parecer' => 'Bom']);

    $response->assertStatus(500);
    expect($response->json('message'))->toEndWith('(falha inesperada)');
});
