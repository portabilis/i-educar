<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyDiscipline;
use App\Models\LegacySchoolGradeDiscipline;
use Tests\EloquentTestCase;

class LegacySchoolGradeDisciplineTest extends EloquentTestCase
{
    protected $relations = [
        'discipline' => LegacyDiscipline::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName(): string
    {
        return LegacySchoolGradeDiscipline::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'id' => 'ref_cod_disciplina',
            'workload' => 'carga_horaria'
        ];
    }

    /** @test */
    public function attributes()
    {
        $this->assertEquals($this->model->ref_cod_disciplina, $this->model->id);
        $this->assertEquals($this->model->discipline->name ?? null, $this->model->name);
        $this->assertEquals($this->model->carga_horaria, $this->model->workload);
    }
}
