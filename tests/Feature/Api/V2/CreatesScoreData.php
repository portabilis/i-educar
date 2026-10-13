<?php

namespace Tests\Feature\Api\V2;

use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineExemptionFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyExemptionStageFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use RegraAvaliacao_Model_Nota_TipoValor;
use RegraAvaliacao_Model_TipoParecerDescritivo;
use RegraAvaliacao_Model_TipoPresenca;
use RegraAvaliacao_Model_TipoRecuperacaoParalela;

trait CreatesScoreData
{
    use ConfiguresInstitutionToken;
    use CreatesSchoolCalendar;

    protected function createScoreData(array $evaluationRuleAttributes = []): array
    {
        $school = LegacySchoolFactory::new()->create();

        $course = LegacyCourseFactory::new()
            ->standardAcademicYear()
            ->create(['ref_cod_instituicao' => $school->ref_cod_instituicao]);

        $level = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
        ]);

        $year = LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $school,
        ]);

        $stage = LegacyStageTypeFactory::new()->create([
            'ref_cod_instituicao' => $school->ref_cod_instituicao,
            'num_etapas' => 4,
        ]);

        LegacyAcademicYearStageFactory::new()->create([
            'ref_ano' => $year->ano,
            'ref_ref_cod_escola' => $school,
            'ref_cod_modulo' => $stage,
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $level,
            'ref_cod_escola' => $school,
        ]);

        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->school_id,
            'ref_ref_cod_serie' => $schoolGrade->grade_id,
            'ref_cod_curso' => $course,
        ]);

        // A definition da factory sorteia tipo de nota e de presença; o teste
        // fixa os dois para que as notas assertadas não dependam do sorteio.
        $evaluationRule = LegacyEvaluationRuleFactory::new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            'tipo_recuperacao_paralela' => RegraAvaliacao_Model_TipoRecuperacaoParalela::USAR_POR_ETAPA,
            'tipo_nota' => RegraAvaliacao_Model_Nota_TipoValor::NUMERICA,
            'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
            ...$evaluationRuleAttributes,
        ]);

        $discipline = LegacyDisciplineFactory::new()->create();

        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'escola_id' => $school,
            'turma_id' => $schoolClass,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolClass->grade_id,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'hora_falta' => null,
        ]);

        $evaluationRuleGradeYear = LegacyEvaluationRuleGradeYearFactory::new()->create([
            'regra_avaliacao_id' => $evaluationRule->getKey(),
            'serie_id' => $level,
        ]);

        $student = LegacyStudentFactory::new()->create();

        $registration = LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student,
            'ano' => $evaluationRuleGradeYear->ano_letivo,
            'ref_ref_cod_serie' => $level,
            'ref_ref_cod_escola' => $school,
            'ref_cod_curso' => $course,
        ]);

        LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration,
            'ref_cod_turma' => $schoolClass,
        ]);

        LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->ref_cod_escola,
            'ref_ref_cod_serie' => $schoolGrade->ref_cod_serie,
            'ref_cod_disciplina' => $discipline,
            'anos_letivos' => $schoolGrade->anos_letivos,
        ]);

        $this->configureInstitutionToken($school->institution);

        return [
            'school' => $school,
            'school_class' => $schoolClass,
            'student' => $student,
            'discipline' => $discipline,
            'registration' => $registration,
        ];
    }

    protected function exemptDiscipline(array $data, array $stages): void
    {
        $registration = $data['registration'];

        $exemption = LegacyDisciplineExemptionFactory::new()->create([
            'ref_cod_matricula' => $registration,
            'ref_cod_disciplina' => $data['discipline'],
            'ref_cod_escola' => $registration->ref_ref_cod_escola,
            'ref_cod_serie' => $registration->ref_ref_cod_serie,
        ]);

        foreach ($stages as $stage) {
            LegacyExemptionStageFactory::new()->create(['ref_cod_dispensa' => $exemption, 'etapa' => $stage]);
        }
    }

    protected function scorePayload(array $data, array $overrides = []): array
    {
        return array_merge([
            'turma_id' => $data['school_class']->getKey(),
            'aluno_id' => $data['student']->getKey(),
            'componente_id' => $data['discipline']->getKey(),
            'etapa' => 1,
        ], $overrides);
    }

    protected function postScore(array $data, array $overrides = []): TestResponse
    {
        return $this->withHeaders(['token' => 'valid-token'])
            ->postJson('/api/v2/notas', $this->scorePayload($data, $overrides));
    }

    protected function postStageDisciplineOpinion(array $data, array $overrides = []): TestResponse
    {
        return $this->withHeaders(['token' => 'valid-token'])
            ->postJson('/api/v2/pareceres-por-etapa-e-componente', $this->scorePayload($data, $overrides));
    }

    protected function postStageGeneralOpinion(array $data, array $overrides = []): TestResponse
    {
        return $this->withHeaders(['token' => 'valid-token'])
            ->postJson('/api/v2/pareceres-por-etapa-geral', Arr::except($this->scorePayload($data, $overrides), 'componente_id'));
    }

    protected function postAnnualDisciplineOpinion(array $data, array $overrides = []): TestResponse
    {
        return $this->withHeaders(['token' => 'valid-token'])
            ->postJson('/api/v2/pareceres-anual-por-componente', Arr::except($this->scorePayload($data, $overrides), 'etapa'));
    }

    protected function postAnnualGeneralOpinion(array $data, array $overrides = []): TestResponse
    {
        return $this->withHeaders(['token' => 'valid-token'])
            ->postJson('/api/v2/pareceres-anual-geral', Arr::except($this->scorePayload($data, $overrides), ['componente_id', 'etapa']));
    }
}
