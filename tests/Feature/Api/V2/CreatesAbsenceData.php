<?php

namespace Tests\Feature\Api\V2;

use App\Models\LegacyDiscipline;
use App\Models\LegacyKnowledgeArea;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolGrade;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyDisciplineSchoolClassFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyKnowledgeAreaFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassGradeFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use RegraAvaliacao_Model_TipoParecerDescritivo;
use RegraAvaliacao_Model_TipoPresenca;

trait CreatesAbsenceData
{
    use ConfiguresInstitutionToken;
    use CreatesSchoolCalendar;

    protected function createAbsenceData(int $attendanceType = RegraAvaliacao_Model_TipoPresenca::GERAL): array
    {
        $school = LegacySchoolFactory::new()->create();

        $course = LegacyCourseFactory::new()->standardAcademicYear()->create();

        $level = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course,
            'dias_letivos' => '200',
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

        $evaluationRule = LegacyEvaluationRuleFactory::new()->create([
            'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
            'tipo_presenca' => $attendanceType,
        ]);

        $discipline = $this->addDisciplineToSchoolClass($schoolClass, $schoolGrade);

        $evaluationRuleGradeYear = LegacyEvaluationRuleGradeYearFactory::new()->create([
            'regra_avaliacao_id' => $evaluationRule->getKey(),
            'serie_id' => $level,
        ]);

        $registration = LegacyRegistrationFactory::new()->create([
            'ano' => $evaluationRuleGradeYear->ano_letivo,
            'ref_ref_cod_serie' => $level,
            'ref_ref_cod_escola' => $school,
        ]);

        $enrollment = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration,
            'ref_cod_turma' => $schoolClass,
        ]);

        $this->configureInstitutionToken($school->institution);

        return [
            'registration' => $registration,
            'school_class' => $schoolClass,
            'school_grade' => $schoolGrade,
            'discipline' => $discipline,
            'turma_id' => $enrollment->ref_cod_turma,
            'aluno_id' => $registration->ref_cod_aluno,
        ];
    }

    protected function addDisciplineToSchoolClass(
        LegacySchoolClass $schoolClass,
        LegacySchoolGrade $schoolGrade,
        ?LegacyKnowledgeArea $knowledgeArea = null,
    ): LegacyDiscipline {
        $discipline = LegacyDisciplineFactory::new()->create(array_filter([
            'knowledge_area_id' => $knowledgeArea?->getKey(),
        ]));

        LegacyDisciplineSchoolClassFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolGrade->ref_cod_serie,
            'escola_id' => $schoolGrade->ref_cod_escola,
            'turma_id' => $schoolClass,
        ]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $schoolGrade->ref_cod_serie,
            'anos_letivos' => $schoolGrade->anos_letivos,
            'hora_falta' => null,
        ]);

        LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $schoolGrade->ref_cod_escola,
            'ref_ref_cod_serie' => $schoolGrade->ref_cod_serie,
            'ref_cod_disciplina' => $discipline,
            'anos_letivos' => $schoolGrade->anos_letivos,
        ]);

        return $discipline;
    }

    /**
     * Torna a turma multisseriada: adiciona uma segunda série, com um componente
     * próprio e um aluno matriculado nela.
     */
    protected function addSecondGradeToSchoolClass(array $data): array
    {
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $data['school_grade']->grade->ref_cod_curso,
            'dias_letivos' => '200',
        ]);

        $schoolGrade = LegacySchoolGradeFactory::new()->create([
            'ref_cod_serie' => $grade,
            'ref_cod_escola' => $data['school_grade']->ref_cod_escola,
        ]);

        foreach ([$data['school_grade'], $schoolGrade] as $classGrade) {
            LegacySchoolClassGradeFactory::new()->create([
                'escola_id' => $classGrade->ref_cod_escola,
                'serie_id' => $classGrade->ref_cod_serie,
                'turma_id' => $data['school_class'],
            ]);
        }

        $discipline = $this->addDisciplineToSchoolClass($data['school_class'], $schoolGrade);

        LegacyEvaluationRuleGradeYearFactory::new()->create([
            'regra_avaliacao_id' => LegacyEvaluationRuleFactory::new()->create([
                'parecer_descritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_GERAL,
                'tipo_presenca' => RegraAvaliacao_Model_TipoPresenca::POR_COMPONENTE,
            ]),
            'serie_id' => $grade,
            'ano_letivo' => $data['registration']->ano,
        ]);

        $registration = LegacyRegistrationFactory::new()->create([
            'ano' => $data['registration']->ano,
            'ref_ref_cod_serie' => $grade,
            'ref_ref_cod_escola' => $data['school_grade']->ref_cod_escola,
        ]);

        LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration,
            'ref_cod_turma' => $data['school_class'],
        ]);

        return [
            'discipline' => $discipline,
            'registration' => $registration,
            'aluno_id' => $registration->ref_cod_aluno,
        ];
    }

    protected function createGroupedKnowledgeArea(): LegacyKnowledgeArea
    {
        $knowledgeArea = LegacyKnowledgeAreaFactory::new()->create();
        $knowledgeArea->forceFill(['agrupar_descritores' => true])->save();

        return $knowledgeArea;
    }

    protected function generalAbsencePayload(array $data, array $overrides = []): array
    {
        return array_merge([
            'etapa' => 1,
            'faltas' => 2,
            'aluno_id' => $data['aluno_id'],
            'turma_id' => $data['turma_id'],
        ], $overrides);
    }

    protected function disciplineAbsencePayload(array $data, array $overrides = []): array
    {
        return array_merge($this->generalAbsencePayload($data), [
            'componente_id' => $data['discipline']->getKey(),
        ], $overrides);
    }
}
