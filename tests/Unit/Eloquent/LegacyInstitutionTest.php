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

    public function test_scope_active(): void
    {
        $found = LegacyInstitution::active()->get();

        $this->assertCount(1, $found);
    }

    public function test_attributes()
    {
        $this->assertEquals($this->model->cod_instituicao, $this->model->id);
        $this->assertEquals($this->model->nm_instituicao, $this->model->name);
        $this->assertEquals($this->model->cidade, $this->model->city);
        $this->assertEquals($this->model->state, $this->model->ref_sigla_uf);
    }

    public function test_relocation_date(): void
    {
        $this->assertEquals($this->model->relocationDate, $this->model->data_base_remanejamento);
    }

    public function test_educacenso_date(): void
    {
        $this->assertEquals($this->model->educacensoDate, $this->model->data_educacenso);
    }

    public function test_is_mandatory_censo_fields(): void
    {
        $this->assertEquals((bool) $this->model->obrigar_campos_censo, $this->model->isMandatoryCensoFields());
    }

    public function test_get_allow_registration_out_academic_year_attribute(): void
    {
        $this->assertEquals((bool) $this->model->permitir_matricula_fora_periodo_letivo, $this->model->allowRegistrationOutAcademicYear);
    }

    public function test_relationship_evaluation_rules()
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

    public function test_get_relocation_date(): void
    {
        $this->assertEquals($this->model->relocationDate?->format('Y-m-d'), $this->model->getRelocationDate());
    }
}
