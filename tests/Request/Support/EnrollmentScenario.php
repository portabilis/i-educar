<?php

namespace Tests\Request\Support;

use App\Models\LegacyCourse;
use App\Models\LegacyDiscipline;
use App\Models\LegacyEnrollment;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyGrade;
use App\Models\LegacyPeriod;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Models\LegacyStudent;
use App\Models\LegacyUser;
use Database\Factories\LegacyAcademicYearStageFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyEducationTypeFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStageTypeFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Seeders\DefaultRelatorioSituacaoMatriculaTableSeeder;
use Illuminate\Support\Facades\DB;

/**
 * A cadeia escola > curso > série que as telas de turma, matrícula e
 * enturmação exigem, montada na instituição de um usuário e com o ano letivo
 * da escola em andamento, de 1º de fevereiro a 15 de dezembro, em dois
 * semestres. O curso segue o padrão de ano escolar, então as turmas usam as
 * etapas da escola.
 *
 * As factories de turma e de matrícula criam, por padrão, escola, curso e
 * série soltos entre si; aqui tudo aponta para o mesmo lugar.
 */
final class EnrollmentScenario
{
    public int $institution;

    public LegacySchool $school;

    public LegacyCourse $course;

    public LegacyGrade $grade;

    private function __construct(public int $year) {}

    public static function for(LegacyUser $user, ?int $year = null): self
    {
        // As views de situação da matrícula cruzam com esta tabela, que o
        // banco de testes não traz preenchida.
        if (DB::table('relatorio.situacao_matricula')->doesntExist()) {
            (new DefaultRelatorioSituacaoMatriculaTableSeeder)->run();
        }

        $scenario = new self($year ?? now()->year);
        $institution = ['ref_cod_instituicao' => $user->ref_cod_instituicao];

        $scenario->institution = $user->ref_cod_instituicao;
        $scenario->school = LegacySchoolFactory::new()->withName('Escola Aurora')->create($institution);
        $scenario->course = LegacyCourseFactory::new()->create($institution + [
            'nm_curso' => 'Ensino Fundamental',
            'padrao_ano_escolar' => 1,
            'multi_seriado' => 0,
            'ref_cod_tipo_ensino' => LegacyEducationTypeFactory::new()->create($institution + ['atividade_complementar' => false]),
        ]);
        $scenario->grade = $scenario->grade('Primeiro Ano');

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $scenario->school,
            'ref_cod_curso' => $scenario->course,
            'anos_letivos' => '{' . $scenario->year . '}',
        ]);

        $academicYear = LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $scenario->school,
            'ano' => $scenario->year,
            'andamento' => 1,
        ]);

        $stageType = LegacyStageTypeFactory::new()->create($institution + ['nm_tipo' => 'Semestre', 'num_etapas' => 2]);

        foreach ([1 => ['02-01', '06-30'], 2 => ['08-01', '12-15']] as $sequence => [$start, $end]) {
            LegacyAcademicYearStageFactory::new()->create([
                'escola_ano_letivo_id' => $academicYear,
                'ref_ano' => $scenario->year,
                'ref_ref_cod_escola' => $scenario->school->getKey(),
                'sequencial' => $sequence,
                'ref_cod_modulo' => $stageType,
                'data_inicio' => "{$scenario->year}-{$start}",
                'data_fim' => "{$scenario->year}-{$end}",
                'dias_letivos' => 100,
            ]);
        }

        return $scenario;
    }

    /**
     * Mais uma série do curso, já vinculada à escola.
     */
    public function grade(string $name, array $attributes = []): LegacyGrade
    {
        $grade = LegacyGradeFactory::new()->create($attributes + [
            'ref_cod_curso' => $this->course,
            'nm_serie' => $name,
        ]);

        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $this->school,
            'ref_cod_serie' => $grade,
            'anos_letivos' => '{' . $this->year . '}',
        ]);

        return $grade;
    }

    /**
     * A regra de avaliação da série (a primeira, se nenhuma for dada) no ano
     * do cenário.
     */
    public function evaluationRule(array $attributes = [], ?LegacyGrade $grade = null): LegacyEvaluationRule
    {
        $rule = LegacyEvaluationRuleFactory::new()->create($attributes + ['instituicao_id' => $this->institution]);

        LegacyEvaluationRuleGradeYearFactory::new()->create([
            'regra_avaliacao_id' => $rule,
            'regra_avaliacao_diferenciada_id' => null,
            'serie_id' => $grade ?? $this->grade,
            'ano_letivo' => $this->year,
        ]);

        return $rule;
    }

    /**
     * Um componente curricular da série (a primeira, se nenhuma for dada),
     * oferecido pela escola no ano do cenário.
     */
    public function discipline(string $name, int $workload = 80, ?LegacyGrade $grade = null): LegacyDiscipline
    {
        $grade ??= $this->grade;
        $discipline = LegacyDisciplineFactory::new()->create(['instituicao_id' => $this->institution, 'nome' => $name]);

        LegacyDisciplineAcademicYearFactory::new()->create([
            'componente_curricular_id' => $discipline,
            'ano_escolar_id' => $grade,
            'carga_horaria' => $workload,
            'anos_letivos' => '{' . $this->year . '}',
        ]);

        LegacySchoolGradeDisciplineFactory::new()->create([
            'ref_ref_cod_escola' => $this->school,
            'ref_ref_cod_serie' => $grade,
            'ref_cod_disciplina' => $discipline,
            'anos_letivos' => '{' . $this->year . '}',
        ]);

        return $discipline;
    }

    public function schoolClass(array $attributes = []): LegacySchoolClass
    {
        return LegacySchoolClassFactory::new()->create($attributes + [
            'ref_ref_cod_escola' => $this->school,
            'ref_ref_cod_serie' => $this->grade,
            'ref_cod_curso' => $this->course,
            'ref_cod_instituicao' => $this->institution,
            'ano' => $this->year,
        ]);
    }

    /**
     * O turno com o código fixo que as telas comparam com as constantes
     * `clsPmieducarTurma::TURNO_*` (1 matutino, 2 vespertino, 3 noturno,
     * 4 integral). As factories de turno criam códigos sequenciais.
     */
    public function period(int $id): LegacyPeriod
    {
        $names = [1 => 'Matutino', 2 => 'Vespertino', 3 => 'Noturno', 4 => 'Integral'];

        return LegacyPeriod::query()->find($id) ?? LegacyPeriodFactory::new()->create(['id' => $id, 'nome' => $names[$id]]);
    }

    public function student(string $name, array $attributes = []): LegacyStudent
    {
        return LegacyStudentFactory::new()->create($attributes + [
            'ref_idpes' => LegacyIndividualFactory::new()->create([
                'idpes' => LegacyPersonFactory::new()->create(['nome' => $name, 'tipo' => 'F']),
            ]),
        ]);
    }

    /**
     * Um aluno com esse nome, matriculado na série do cenário e enturmado na
     * turma dada.
     */
    public function enrolled(string $name, LegacySchoolClass $schoolClass, array $enrollment = [], array $registration = []): LegacyEnrollment
    {
        return $this->enroll(
            $this->registration($registration + ['ref_cod_aluno' => $this->student($name)]),
            $schoolClass,
            $enrollment
        );
    }

    public function registration(array $attributes = []): LegacyRegistration
    {
        return LegacyRegistrationFactory::new()->create($attributes + [
            'ref_ref_cod_escola' => $this->school,
            'ref_ref_cod_serie' => $this->grade,
            'ref_cod_curso' => $this->course,
            'ano' => $this->year,
            'ativo' => 1,
            'ultima_matricula' => 1,
        ]);
    }

    public function enroll(LegacyRegistration $registration, LegacySchoolClass $schoolClass, array $attributes = []): LegacyEnrollment
    {
        return LegacyEnrollmentFactory::new()->create($attributes + [
            'ref_cod_matricula' => $registration,
            'ref_cod_turma' => $schoolClass,
            'turno_id' => $schoolClass->turma_turno_id,
            'ativo' => 1,
        ]);
    }
}
