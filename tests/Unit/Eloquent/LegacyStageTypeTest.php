<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyAcademicYearStage;
use App\Models\LegacyInstitution;
use App\Models\LegacySchoolClassStage;
use App\Models\LegacyStageType;
use App\Models\LegacyUser;
use Database\Factories\LegacyStageTypeFactory;
use Tests\EloquentTestCase;

class LegacyStageTypeTest extends EloquentTestCase
{
    public $relations = [
        'institution' => LegacyInstitution::class,
        'academicYearStages' => LegacyAcademicYearStage::class,
        'schoolClassStage' => LegacySchoolClassStage::class,
        'createdByUser' => LegacyUser::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyStageType::class;
    }

    /** @test */
    public function attributes()
    {
        $expected = sprintf('%s - %d etapa(s)', $this->model->nm_tipo, $this->model->num_etapas);
        $this->assertEquals($expected, $this->model->name);

        $expected = str_replace(["\r\n", "\r", "\n"], '<br />', $this->model->getRawOriginal('descricao'));
        $this->assertEquals($expected, $this->model->descricao);
    }

    public function testScopeActive(): void
    {
        LegacyStageTypeFactory::new()->create(['ativo' => 0]);
        $found = $this->instanceNewEloquentModel()->active()->get();
        $this->assertCount(1, $found);
    }

    public function testAlreadyExists(): void
    {
        $id = 1;
        $name = $this->model->nm_tipo;
        $stagesNumber = $this->model->num_etapas;
        $expected = $this->instanceNewEloquentModel()->query()->where('ativo', 1)
            ->where('nm_tipo', $name)
            ->where('num_etapas', $stagesNumber)
            ->when($id, static function ($query) use ($id) {
                $query->where('cod_modulo', '<>', $id);
            })
            ->exists();
        $this->assertEquals($expected, $this->instanceNewEloquentModel()->alreadyExists($name, $stagesNumber, $id));
    }
}
