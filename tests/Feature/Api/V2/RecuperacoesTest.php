<?php

use App\Models\LegacyDisciplineScore;
use App\Models\LegacyEvaluationRule;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyRemedialRuleFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Feature\Api\V2\CreatesScoreData;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class, CreatesScoreData::class);

function specificStagesRemedialRule(): array
{
    return ['tipo_recuperacao_paralela' => RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPAS_ESPECIFICAS];
}

function createRemedialRule(array $data, float $maxScore = 5): void
{
    LegacyRemedialRuleFactory::new()->create([
        'regra_avaliacao_id' => $data['evaluation_rule'],
        'etapas_recuperadas' => '1',
        'nota_maxima' => $maxScore,
    ]);
}

test('rejects request without token', function () {
    $data = $this->createScoreData();

    $this->postJson('/api/v2/recuperacoes', $this->scorePayload($data, ['nota' => 5, 'recuperacao' => 7]))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('rejects request with invalid token', function () {
    $data = $this->createScoreData();

    $this->withHeaders(['token' => 'invalid-token'])
        ->postJson('/api/v2/recuperacoes', $this->scorePayload($data, ['nota' => 5, 'recuperacao' => 7]))
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('requires mandatory fields', function () {
    $this->createScoreData();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/recuperacoes', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['recuperacao', 'etapa', 'aluno_id', 'turma_id', 'componente_id']);
});

test('rejects exam stage', function () {
    $data = $this->createScoreData();

    $this->postRemedialScore($data, ['recuperacao' => 7, 'etapa' => 'Rc'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['etapa']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('stores remedial score with the stage score', function () {
    $data = $this->createScoreData();

    $this->postRemedialScore($data, ['nota' => 5, 'recuperacao' => 7])
        ->assertStatus(201)
        ->assertJson(['message' => 'Recuperação salva com sucesso.']);

    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => 1,
        'nota' => 7,
        'nota_original' => 5,
        'nota_recuperacao' => 7,
        'nota_recuperacao_especifica' => null,
    ]);
});

test('keeps the stored stage score when it is not informed', function () {
    $data = $this->createScoreData();

    $this->postScore($data, ['nota' => 5])->assertStatus(201);
    $this->postRemedialScore($data, ['recuperacao' => 8])->assertStatus(201);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 1);
    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => 1,
        'nota' => 8,
        'nota_original' => 5,
        'nota_recuperacao' => 8,
    ]);
});

test('uses zero as stage score when there is none stored', function () {
    $data = $this->createScoreData();

    $this->postRemedialScore($data, ['recuperacao' => 6])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => 1,
        'nota' => 6,
        'nota_original' => 0,
        'nota_recuperacao' => 6,
    ]);
});

test('sums the remedial score when the rule is configured to sum', function () {
    $data = $this->createScoreData([
        'tipo_calculo_recuperacao_paralela' => LegacyEvaluationRule::PARALLEL_REMEDIAL_SUM_SCORE,
    ]);

    $this->postRemedialScore($data, ['nota' => 6, 'recuperacao' => 3])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'nota' => 9,
        'nota_original' => 6,
        'nota_recuperacao' => 3,
    ]);
});

test('rejects remedial score above the general maximum', function () {
    $data = $this->createScoreData();

    $this->postRemedialScore($data, ['nota' => 5, 'recuperacao' => 11])
        ->assertStatus(422)
        ->assertJson([
            'message' => 'A nota de recuperação 11 está acima da configurada para nota máxima de recuperação que é 10.',
        ]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('stores specific stage remedial score within the remedial rule maximum', function () {
    $data = $this->createScoreData(specificStagesRemedialRule());
    createRemedialRule($data);

    $this->postRemedialScore($data, ['nota' => 4, 'recuperacao' => 5])->assertStatus(201);

    $this->assertDatabaseHas(LegacyDisciplineScore::class, [
        'componente_curricular_id' => $data['discipline']->getKey(),
        'etapa' => 1,
        'nota' => 4,
        'nota_original' => 4,
        'nota_recuperacao' => null,
        'nota_recuperacao_especifica' => 5,
    ]);
});

test('rejects specific stage remedial score above the remedial rule maximum', function () {
    $data = $this->createScoreData(specificStagesRemedialRule());
    createRemedialRule($data);

    $this->postRemedialScore($data, ['nota' => 4, 'recuperacao' => 6])
        ->assertStatus(422)
        ->assertJson([
            'message' => 'A nota de recuperação 6 está acima da configurada para nota máxima de recuperação que é 5.',
        ]);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('rejects specific stage remedial score when the stage has no remedial rule', function () {
    $data = $this->createScoreData(specificStagesRemedialRule());

    $this->postRemedialScore($data, ['nota' => 4, 'recuperacao' => 5])
        ->assertStatus(422)
        ->assertJson(['message' => 'A nota máxima para recuperação não foi definida.']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('rejects remedial score when the rule does not use parallel remedial', function () {
    $data = $this->createScoreData([
        'tipo_recuperacao_paralela' => RegraAvaliacao_Model_TipoRecuperacaoParalela::NAO_USAR,
    ]);

    $this->postRemedialScore($data, ['nota' => 5, 'recuperacao' => 7])
        ->assertStatus(422)
        ->assertJson(['message' => 'A regra de avaliação da matrícula não utiliza recuperação paralela.']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('does not store when registration is not found', function () {
    $data = $this->createScoreData();
    $otherStudent = LegacyStudentFactory::new()->create();

    $this->postRemedialScore($data, [
        'recuperacao' => 7,
        'aluno_id' => $otherStudent->getKey(),
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['aluno_id']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});

test('does not store when discipline does not belong to school class', function () {
    $data = $this->createScoreData();
    $otherDiscipline = LegacyDisciplineFactory::new()->create();

    $this->postRemedialScore($data, [
        'recuperacao' => 7,
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

test('rejects stage outside the school calendar', function () {
    $data = $this->createScoreData();
    $this->createSchoolCalendar($data['registration'], $data['school_class'], 4);

    $this->postRemedialScore($data, ['nota' => 5, 'recuperacao' => 7, 'etapa' => 5])
        ->assertStatus(422)
        ->assertJson(['message' => 'A etapa informada é inválida.']);

    $this->assertDatabaseCount(LegacyDisciplineScore::class, 0);
});
