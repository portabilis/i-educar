<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDiscipline;
use App\Models\LegacySchoolGradeDiscipline;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Tests\EloquentTestCase;

class LegacySchoolGradeDisciplineTest extends EloquentTestCase
{
    protected $relations = [
        'discipline' => LegacyDiscipline::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacySchoolGradeDiscipline::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'id' => 'ref_cod_disciplina',
            'workload' => 'carga_horaria',
        ];
    }

    public function setUp(): void
    {
        $this->factoryModifier = function (LegacySchoolGradeDisciplineFactory $factory) {
            return $factory->withLegacyDefinition();
        };

        parent::setUp();
    }

    /** @test */
    public function attributes()
    {
        $this->assertEquals($this->model->ref_cod_disciplina, $this->model->id);
        $this->assertEquals($this->model->discipline->name ?? null, $this->model->name);
        $this->assertEquals($this->model->carga_horaria, $this->model->workload);
    }
}
