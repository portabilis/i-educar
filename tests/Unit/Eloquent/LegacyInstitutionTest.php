<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGeneralConfiguration;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use Database\Factories\LegacyAverageFormulaFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyRoundingTableFactory;
use Tests\EloquentTestCase;

class LegacyInstitutionTest extends EloquentTestCase
{
    public $relations = [
        'generalConfiguration' => LegacyGeneralConfiguration::class,
        'schools' => LegacySchool::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyInstitution::class;
    }

    public function testScopeActive(): void
    {
        $found = LegacyInstitution::active()->get();

        $this->assertCount(1, $found);
    }

    /** @test */
    public function attributes()
    {
        $this->assertEquals($this->model->cod_instituicao, $this->model->id);
        $this->assertEquals($this->model->nm_instituicao, $this->model->name);
        $this->assertEquals($this->model->cidade, $this->model->city);
        $this->assertEquals($this->model->state, $this->model->ref_sigla_uf);
    }

    public function testRelocationDate(): void
    {
        $this->assertEquals($this->model->relocationDate, $this->model->data_base_remanejamento);
    }

    public function testEducacensoDate(): void
    {
        $this->assertEquals($this->model->educacensoDate, $this->model->data_educacenso);
    }

    public function testIsMandatoryCensoFields(): void
    {
        $this->assertEquals((bool) $this->model->obrigar_campos_censo, $this->model->isMandatoryCensoFields());
    }

    public function testGetAllowRegistrationOutAcademicYearAttribute(): void
    {
        $this->assertEquals((bool) $this->model->permitir_matricula_fora_periodo_letivo, $this->model->allowRegistrationOutAcademicYear);
    }

    public function testRelationshipEvaluationRules()
    {
        LegacyRoundingTableFactory::new()->create([
            'instituicao_id' => $this->model,
        ]);
        LegacyEvaluationRuleFactory::new()->create([
            'formula_media_id' => LegacyAverageFormulaFactory::new()->create([
                'institution_id' => $this->model,
            ]),
            'instituicao_id' => $this->model,
        ]);

        $this->assertCount(1, $this->model->evaluationRules);
        $this->assertInstanceOf(LegacyEvaluationRule::class, $this->model->evaluationRules->first());
    }

    public function testGetRelocationDate(): void
    {
        $this->assertEquals($this->model->relocationDate?->format('Y-m-d'), $this->model->getRelocationDate());
    }
}
