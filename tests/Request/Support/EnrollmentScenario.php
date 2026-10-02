<?php

namespace Tests\Request\Support;

use App\Models\LegacyCourse;
use App\Models\LegacyEnrollment;
use App\Models\LegacyGrade;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Models\LegacyUser;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolAcademicYearFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;

/**
 * A cadeia escola > curso > série que as telas de turma, matrícula e
 * enturmação exigem, montada na instituição de um usuário e com o ano letivo
 * da escola em andamento.
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
        $scenario = new self($year ?? now()->year);
        $institution = ['ref_cod_instituicao' => $user->ref_cod_instituicao];

        $scenario->institution = $user->ref_cod_instituicao;
        $scenario->school = LegacySchoolFactory::new()->withName('Escola Aurora')->create($institution);
        $scenario->course = LegacyCourseFactory::new()->create($institution + ['nm_curso' => 'Ensino Fundamental']);
        $scenario->grade = $scenario->grade('Primeiro Ano');

        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $scenario->school,
            'ref_cod_curso' => $scenario->course,
            'anos_letivos' => '{' . $scenario->year . '}',
        ]);

        LegacySchoolAcademicYearFactory::new()->create([
            'ref_cod_escola' => $scenario->school,
            'ano' => $scenario->year,
            'andamento' => 1,
        ]);

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
