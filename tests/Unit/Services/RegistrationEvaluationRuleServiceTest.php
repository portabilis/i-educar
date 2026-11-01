<?php

use App\Models\LegacyRegistration;
use App\Services\RegistrationEvaluationRuleService;
use Database\Factories\LegacyDeficiencyFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->registration = function (bool $differentiatedSchool = false): array {
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
    };
});

test('uses the grade rule of the registration year', function () {
    [$registration, $rule] = ($this->registration)();

    $this->assertSame($rule->getKey(), (new RegistrationEvaluationRuleService)->getEvaluationRule($registration)->getKey());
});

test('uses the differentiated rule when the school asks for it', function () {
    [$registration, , $differentiated] = ($this->registration)(differentiatedSchool: true);

    $this->assertSame($differentiated->getKey(), (new RegistrationEvaluationRuleService)->getEvaluationRule($registration)->getKey());
});

test('uses the rule for students with a considerable deficiency', function () {
    [$registration, , , $forDeficiency] = ($this->registration)();
    $registration->student->person->deficiencies()->attach(LegacyDeficiencyFactory::new()->create());

    $this->assertSame($forDeficiency->getKey(), (new RegistrationEvaluationRuleService)->getEvaluationRule($registration)->getKey());
});
