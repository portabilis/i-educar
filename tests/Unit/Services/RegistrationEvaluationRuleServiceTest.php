<?php

namespace Tests\Unit\Services;

use App\Models\LegacyRegistration;
use App\Services\RegistrationEvaluationRuleService;
use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegistrationEvaluationRuleServiceTest extends TestCase
{
    use DatabaseTransactions;

    private function registration(bool $differentiatedSchool = false): array
    {
        $rule = LegacyEvaluationRuleFactory::new()->create();
        $differentiated = LegacyEvaluationRuleFactory::new()->create();
        $forDeficiency = LegacyEvaluationRuleFactory::new()->create();
        $rule->forceFill(['regra_diferenciada_id' => $forDeficiency->getKey()])->save();

        $gradeYear = LegacyEvaluationRuleGradeYearFactory::new()->create([
            'regra_avaliacao_id' => $rule,
            'regra_avaliacao_diferenciada_id' => $differentiated,
            'ano_letivo' => 2030,
        ]);

        $registration = LegacyRegistrationFactory::new()->create([
            'ano' => 2030,
            'ref_ref_cod_serie' => $gradeYear->serie_id,
            'ref_ref_cod_escola' => LegacySchoolFactory::new()->create(['utiliza_regra_diferenciada' => $differentiatedSchool]),
        ]);

        return [LegacyRegistration::query()->findOrFail($registration->getKey()), $rule, $differentiated, $forDeficiency];
    }

    public function test_uses_the_grade_rule_of_the_registration_year(): void
    {
        [$registration, $rule] = $this->registration();

        $this->assertSame($rule->getKey(), (new RegistrationEvaluationRuleService)->getEvaluationRule($registration)->getKey());
    }

    public function test_uses_the_differentiated_rule_when_the_school_asks_for_it(): void
    {
        [$registration, , $differentiated] = $this->registration(differentiatedSchool: true);

        $this->assertSame($differentiated->getKey(), (new RegistrationEvaluationRuleService)->getEvaluationRule($registration)->getKey());
    }

    public function test_uses_the_rule_for_students_with_a_considerable_deficiency(): void
    {
        [$registration, , , $forDeficiency] = $this->registration();
        $registration->student->person->deficiencies()->attach(LegacyDeficiencyFactory::new()->create());

        $this->assertSame($forDeficiency->getKey(), (new RegistrationEvaluationRuleService)->getEvaluationRule($registration)->getKey());
    }
}
