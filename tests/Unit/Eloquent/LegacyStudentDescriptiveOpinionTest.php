<?php

use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Models\LegacyGeneralDescriptiveOpinion;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentDescriptiveOpinion;
use Database\Factories\LegacyDisciplineDescriptiveOpinionFactory;
use Database\Factories\LegacyStudentDescriptiveOpinionFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(LegacyStudentDescriptiveOpinion::class, relations: [
    'registration' => LegacyRegistration::class,
    'descriptiveOpinions' => LegacyGeneralDescriptiveOpinion::class,
    'descriptiveOpinionByDiscipline' => LegacyDisciplineDescriptiveOpinion::class,
    'generalDescriptiveOpinion' => LegacyGeneralDescriptiveOpinion::class,
]));

EloquentModelTestCase::registerModelTests();

test('is by discipline', function () {
    $model = Factory::factoryForModel(
        LegacyStudentDescriptiveOpinion::class
    )->new()->create([
        'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
    ]);
    $this->assertTrue($model->isByDiscipline());
});

test('is general', function () {
    $model = Factory::factoryForModel(
        LegacyStudentDescriptiveOpinion::class
    )->new()->create([
        'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
    ]);
    $this->assertTrue($model->isGeneral());
});

test('opinions are by discipline when the rule is by discipline', function () {
    $model = LegacyStudentDescriptiveOpinionFactory::new()->create([
        'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
    ]);
    LegacyDisciplineDescriptiveOpinionFactory::new()->create(['parecer_aluno_id' => $model]);

    $this->assertCount(1, $model->descriptiveOpinions);
    $this->assertInstanceOf(LegacyDisciplineDescriptiveOpinion::class, $model->descriptiveOpinions->first());
});
