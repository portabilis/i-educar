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

    public function testIdAttribute(): void
    {
        $this->assertEquals($this->model->ref_cod_disciplina, $this->model->id);
    }

    public function testNameAttribute(): void
    {
        $this->assertEquals($this->model->discipline->name ?? null, $this->model->name);
    }

    public function testWorkloadAttribute(): void
    {
        $this->assertEquals($this->model->carga_horaria, $this->model->workload);
    }
}
