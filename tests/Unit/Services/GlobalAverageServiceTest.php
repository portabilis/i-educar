<?php

use App\Services\GlobalAverageService;
use Database\Factories\LegacyDisciplineScoreAverageFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyGeneralAverageFactory;
use Database\Factories\LegacyRegistrationScoreFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Api\DiarioApiFakeDataTestTrait;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class, DiarioApiFakeDataTestTrait::class);

test('sem notas lançadas a média global é zero', function (int $generalScore) {
    $registration = $this->getCommonFakeData(LegacyEvaluationRuleFactory::new()->create(['nota_geral_por_etapa' => $generalScore]))->registration;

    expect((new GlobalAverageService)->getGlobalAverage($registration))->toBe(0);
})->with([
    'nota por componente' => [0],
    'nota geral por etapa' => [1],
]);

test('com nota por componente faz a média das médias dos componentes', function () {
    $registration = $this->getCommonFakeData(LegacyEvaluationRuleFactory::new()->create(['nota_geral_por_etapa' => 0]))->registration;
    $scores = LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $registration]);
    LegacyDisciplineScoreAverageFactory::new()->create(['nota_aluno_id' => $scores, 'media' => 6]);
    LegacyDisciplineScoreAverageFactory::new()->create(['nota_aluno_id' => $scores, 'media' => 8]);

    expect((float) (new GlobalAverageService)->getGlobalAverage($registration->refresh()))->toBe(7.0);
});

test('com nota geral por etapa faz a média das médias gerais', function () {
    $registration = $this->getCommonFakeData(LegacyEvaluationRuleFactory::new()->create(['nota_geral_por_etapa' => 1]))->registration;
    $scores = LegacyRegistrationScoreFactory::new()->create(['matricula_id' => $registration]);
    LegacyGeneralAverageFactory::new()->create(['nota_aluno_id' => $scores, 'media' => 5]);
    LegacyGeneralAverageFactory::new()->create(['nota_aluno_id' => $scores, 'media' => 7, 'etapa' => 2]);

    expect((float) (new GlobalAverageService)->getGlobalAverage($registration->refresh()))->toBe(6.0);
});
