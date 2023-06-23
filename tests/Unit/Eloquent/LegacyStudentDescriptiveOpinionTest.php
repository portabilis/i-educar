<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDisciplineDescriptiveOpinion;
use App\Models\LegacyGeneralDescriptiveOpinion;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudentDescriptiveOpinion;
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

    public function testIsByDiscipline(): void
    {
        $model = Factory::factoryForModel(
            $this->getEloquentModelName()
        )->new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_COMPONENTE,
        ]);
        $this->assertTrue($model->isByDiscipline());
    }

    public function testIsGeneral(): void
    {
        $model = Factory::factoryForModel(
            $this->getEloquentModelName()
        )->new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL,
        ]);
        $this->assertTrue($model->isGeneral());
    }
}
