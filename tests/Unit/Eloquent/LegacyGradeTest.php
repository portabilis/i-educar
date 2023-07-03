<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGrade;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Tests\EloquentTestCase;

class LegacyGradeTest extends EloquentTestCase
{
    protected $relations = [
        'course' => LegacyCourse::class,
        'evaluationRules' => [LegacyEvaluationRule::class, ['ano_letivo' => 1]],
        'schools' => [LegacySchool::class, [
            'ref_usuario_cad' => 1,
            'data_cadastro' => '2022-01-01 00:00:00',
        ]],
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyGrade::class;
    }

    protected function getLegacyAttributes(): array
    {
        return [
            'id' => 'cod_serie',
            'name' => 'nm_serie',
            'description' => 'descricao',
            'created_at' => 'data_cadastro',
            'course_id' => 'ref_cod_curso',
        ];
    }

    /** @test */
    public function attributes()
    {
        $this->assertEquals($this->model->cod_serie, $this->model->id);
        $this->assertEquals($this->model->descricao, $this->model->description);
        $this->assertEquals($this->model->ref_cod_curso, $this->model->course_id);

        if (empty($this->model->descricao)) {
            $except = $this->model->nm_serie;
        } else {
            $except = $this->model->nm_serie . ' (' . $this->model->descricao . ')';
        }

        $this->assertEquals($except, $this->model->name);
    }

    public function testRelationshipSchoolClass(): void
    {
        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $this->model->id,
        ]);
        LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_serie' => $schoolGrade->ref_cod_serie,
            'ref_ref_cod_escola' => $schoolGrade->ref_cod_escola,
        ]);

        $this->assertCount(1, $this->model->schoolClass);
        $this->assertInstanceOf(LegacySchoolClass::class, $this->model->schoolClass->first());
    }
}
