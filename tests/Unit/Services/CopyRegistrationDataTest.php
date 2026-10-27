<?php

use App\Exceptions\Registration\MissingEvaluationRuleForCurrentYear;
use App\Exceptions\Transfer\MissingAbsenceType;
use App\Exceptions\Transfer\MissingDescriptiveOpinionType;
use App\Models\LegacyDisciplineAbsence;
use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Models\LegacyDisciplineScore;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralAbsence;
use App\Models\LegacyGeneralDescriptiveOpinion;
use App\Models\LegacyGeneralScore;
use App\Models\LegacyRegistration;
use App\Services\CopyAbsenceService;
use App\Services\CopyDescriptiveOpinionService;
use App\Services\CopyScoreService;
use App\Services\RegistrationEvaluationRuleService;
use Database\Factories\LegacyDisciplineAbsenceFactory;
use Database\Factories\LegacyDisciplineDescriptiveOpinionFactory;
use Database\Factories\LegacyDisciplineScoreFactory;
use Database\Factories\LegacyGeneralAbsenceFactory;
use Database\Factories\LegacyGeneralDescriptiveOpinionFactory;
use Database\Factories\LegacyGeneralScoreFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyStudentAbsenceFactory;
use Database\Factories\LegacyStudentDescriptiveOpinionFactory;
use Database\Factories\LegacyStudentScoreFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

function regraDaCopia(array $attributes): LegacyEvaluationRule
{
    return (new LegacyEvaluationRule)->forceFill($attributes + [
        'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
        'nota_geral_por_etapa' => 0,
        'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::GERAL,
        'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::NENHUM,
    ]);
}

// A matrícula nova usa a primeira regra; a antiga, a segunda.
function regrasDaCopia(?LegacyEvaluationRule $nova, LegacyEvaluationRule $antiga): RegistrationEvaluationRuleService
{
    $service = Mockery::mock(RegistrationEvaluationRuleService::class);
    $service->shouldReceive('getEvaluationRule')->andReturnUsing(fn (LegacyRegistration $registration) => $registration->is(test()->new) ? $nova : $antiga);

    return $service;
}

// Cada cópia cria o registro de notas, faltas ou pareceres da matrícula nova,
// que só pode ter um; por isso cada cópia vai para uma matrícula nova.
function matriculaNovaDaCopia(): LegacyRegistration
{
    return test()->new = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => test()->old->ref_cod_aluno]);
}

beforeEach(function () {
    $this->old = LegacyRegistrationFactory::new()->create();
    $this->new = LegacyRegistrationFactory::new()->create(['ref_cod_aluno' => $this->old->ref_cod_aluno]);
});

test('copia as notas por componente, e nada quando as regras não combinam ou não há nota', function () {
    $score = LegacyStudentScoreFactory::new()->create(['matricula_id' => $this->old]);
    LegacyDisciplineScoreFactory::new()->create(['nota_aluno_id' => $score, 'etapa' => 1, 'nota' => 8]);

    (new CopyScoreService(regrasDaCopia(regraDaCopia([]), regraDaCopia([]))))->copy($this->new, $this->old);

    expect(LegacyDisciplineScore::query()->whereHas('registrationScore', fn ($q) => $q->where('matricula_id', $this->new->getKey()))->pluck('nota')->map(fn ($n) => (float) $n)->all())->toBe([8.0]);

    $nenhuma = regraDaCopia(['tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::NENHUM]);
    (new CopyScoreService(regrasDaCopia($nenhuma, $nenhuma)))->copy(matriculaNovaDaCopia(), $this->old);
    (new CopyScoreService(regrasDaCopia(regraDaCopia([]), regraDaCopia(['tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::CONCEITUAL]))))->copy(matriculaNovaDaCopia(), $this->old);

    expect(LegacyDisciplineScore::query()->whereHas('registrationScore', fn ($q) => $q->where('matricula_id', $this->new->getKey()))->exists())->toBeFalse();
});

test('as notas gerais não são copiadas: o serviço lê a relação generalScore, que o modelo chama de scoreGeneral', function () {
    $score = LegacyStudentScoreFactory::new()->create(['matricula_id' => $this->old]);
    LegacyGeneralScoreFactory::new()->create(['nota_aluno_id' => $score, 'etapa' => 2, 'nota' => 6]);
    $geral = regraDaCopia(['nota_geral_por_etapa' => 1]);

    // A cópia roda nas telas legadas, sem avisos do PHP; aqui o laço recebe nulo.
    $level = error_reporting(0);

    try {
        (new CopyScoreService(regrasDaCopia($geral, $geral)))->copy($this->new, $this->old);
    } finally {
        error_reporting($level);
    }

    expect(LegacyGeneralScore::query()->whereHas('studentScore', fn ($q) => $q->where('matricula_id', $this->new->getKey()))->exists())->toBeFalse();
});

test('copia as faltas gerais ou por componente e exige a regra da matrícula nova e um tipo de presença conhecido', function () {
    $absence = LegacyStudentAbsenceFactory::new()->create(['matricula_id' => $this->old]);
    LegacyGeneralAbsenceFactory::new()->create(['falta_aluno_id' => $absence, 'quantidade' => 3]);
    LegacyDisciplineAbsenceFactory::new()->create(['falta_aluno_id' => $absence, 'quantidade' => 2, 'etapa' => 1]);

    (new CopyAbsenceService(regrasDaCopia(regraDaCopia([]), regraDaCopia([]))))->copy($this->new, $this->old);

    expect(LegacyGeneralAbsence::query()->whereHas('studentAbsence', fn ($q) => $q->where('matricula_id', $this->new->getKey()))->pluck('quantidade')->all())->toBe([3]);

    $porComponente = regraDaCopia(['tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE]);
    (new CopyAbsenceService(regrasDaCopia($porComponente, $porComponente)))->copy(matriculaNovaDaCopia(), $this->old);

    expect(LegacyDisciplineAbsence::query()->whereHas('studentAbsence', fn ($q) => $q->where('matricula_id', $this->new->getKey()))->pluck('quantidade')->all())->toBe([2]);

    (new CopyAbsenceService(regrasDaCopia($porComponente, regraDaCopia([]))))->copy(matriculaNovaDaCopia(), $this->old);

    expect(fn () => (new CopyAbsenceService(regrasDaCopia(null, regraDaCopia([]))))->copy(matriculaNovaDaCopia(), $this->old))->toThrow(MissingEvaluationRuleForCurrentYear::class)
        ->and(fn () => (new CopyAbsenceService(regrasDaCopia(regraDaCopia(['tipo_presenca' => 9]), regraDaCopia(['tipo_presenca' => 9]))))->copy(matriculaNovaDaCopia(), $this->old))->toThrow(MissingAbsenceType::class);
});

test('copia os pareceres gerais ou por componente e recusa um tipo de parecer desconhecido', function () {
    $opinion = LegacyStudentDescriptiveOpinionFactory::new()->create(['matricula_id' => $this->old]);
    LegacyGeneralDescriptiveOpinionFactory::new()->create(['parecer_aluno_id' => $opinion, 'parecer' => 'Parecer geral', 'etapa' => '1']);
    LegacyDisciplineDescriptiveOpinionFactory::new()->create(['parecer_aluno_id' => $opinion, 'parecer' => 'Parecer do componente', 'etapa' => 1]);

    $geral = regraDaCopia(['parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL]);
    (new CopyDescriptiveOpinionService(regrasDaCopia($geral, $geral)))->copy($this->new, $this->old);

    expect(LegacyGeneralDescriptiveOpinion::query()->whereHas('studentDescriptiveOpinion', fn ($q) => $q->where('matricula_id', $this->new->getKey()))->pluck('parecer')->all())->toBe(['Parecer geral']);

    $componente = regraDaCopia(['parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE]);
    (new CopyDescriptiveOpinionService(regrasDaCopia($componente, $componente)))->copy(matriculaNovaDaCopia(), $this->old);

    expect(LegacyDisciplineDescriptiveOpinion::query()->whereHas('studentDescriptiveOpinion', fn ($q) => $q->where('matricula_id', $this->new->getKey()))->pluck('parecer')->all())->toBe(['Parecer do componente']);

    $nenhum = regraDaCopia([]);
    (new CopyDescriptiveOpinionService(regrasDaCopia($nenhum, $nenhum)))->copy(matriculaNovaDaCopia(), $this->old);
    (new CopyDescriptiveOpinionService(regrasDaCopia($componente, $geral)))->copy(matriculaNovaDaCopia(), $this->old);

    $descritor = regraDaCopia(['parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_DESCRITOR]);

    expect(fn () => (new CopyDescriptiveOpinionService(regrasDaCopia($descritor, $descritor)))->copy(matriculaNovaDaCopia(), $this->old))->toThrow(MissingDescriptiveOpinionType::class);
});
