<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Models\LegacyGeneralDescriptiveOpinion;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentDescriptiveOpinion;
use Database\Factories\LegacyDisciplineDescriptiveOpinionFactory;
use Database\Factories\LegacyStudentDescriptiveOpinionFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use RegraAvaliacao_Model_TipoParecerDescritivo;
use Tests\EloquentTestCase;

class LegacyStudentDescriptiveOpinionTest extends EloquentTestCase
{
    protected $relations = [
        'registration' => LegacyRegistration::class,
        'descriptiveOpinions' => LegacyGeneralDescriptiveOpinion::class,
        'descriptiveOpinionByDiscipline' => LegacyDisciplineDescriptiveOpinion::class,
        'generalDescriptiveOpinion' => LegacyGeneralDescriptiveOpinion::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyStudentDescriptiveOpinion::class;
    }

    public function test_is_by_discipline(): void
    {
        $model = Factory::factoryForModel(
            $this->getEloquentModelName()
        )->new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
        ]);
        $this->assertTrue($model->isByDiscipline());
    }

    public function test_is_general(): void
    {
        $model = Factory::factoryForModel(
            $this->getEloquentModelName()
        )->new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
        ]);
        $this->assertTrue($model->isGeneral());
    }

    public function test_opinions_are_by_discipline_when_the_rule_is_by_discipline(): void
    {
        $model = LegacyStudentDescriptiveOpinionFactory::new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
        ]);
        LegacyDisciplineDescriptiveOpinionFactory::new()->create(['parecer_aluno_id' => $model]);

        $this->assertCount(1, $model->descriptiveOpinions);
        $this->assertInstanceOf(LegacyDisciplineDescriptiveOpinion::class, $model->descriptiveOpinions->first());
    }
}
