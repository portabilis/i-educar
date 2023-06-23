<?php

namespace Database\Factories\Exporter;

use App\Models\Exporter\Enrollment;
use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyEvaluationRuleGradeYearFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolCourseFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStudentBenefitFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Seeders\DefaultRelatorioSituacaoMatriculaTableSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        $count = DB::table('relatorio.situacao_matricula')->count();
        if ($count === 0) {
            $seed = new DefaultRelatorioSituacaoMatriculaTableSeeder();
            $seed->run();
        }
        $institution = LegacyInstitutionFactory::new()->current();
        $school = LegacySchoolFactory::new()->create([
            'ref_cod_instituicao' => $institution->id,
        ]);
        $person = LegacyPersonFactory::new()->create();
        LegacyIndividualFactory::new()->create([
            'idpes' => $person,
            'ativo' => 1,
        ]);
        $student = LegacyStudentFactory::new()->create([
            'ref_idpes' => $person,
            'ativo' => 1,
        ]);
        $benefit = LegacyBenefitFactory::new()->create();
        LegacyStudentBenefitFactory::new()->create([
            'aluno_beneficio_id' => $benefit,
            'aluno_id' => $student,
        ]);
        $course = LegacyCourseFactory::new()->create([
            'ref_cod_instituicao' => $institution->id,
        ]);
        $grade = LegacyGradeFactory::new()->create([
            'ref_cod_curso' => $course->id,
        ]);
        LegacySchoolCourseFactory::new()->create([
            'ref_cod_escola' => $school->id,
            'ref_cod_curso' => $course->id,
        ]);
        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $school->id,
            'ref_cod_serie' => $grade->id,
        ]);
        $registration = LegacyRegistrationFactory::new()->create([
            'ref_cod_aluno' => $student,
            'ref_ref_cod_serie' => $grade->id,
            'ref_cod_curso' => $course->id,
            'ref_ref_cod_escola' => $school->id,
            'ativo' => 1,
        ]);
        LegacyEvaluationRuleGradeYearFactory::new()->create([
            'serie_id' => $grade->id,
            'ano_letivo' => now()->year,
        ]);
        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_serie' => $grade->id,
            'ref_cod_curso' => $course->id,
            'ref_ref_cod_escola' => $school->id,
            'ref_cod_instituicao' => $institution->id,
        ]);
        LegacyEnrollmentFactory::new()->active()->create([
            'ref_cod_matricula' => $registration->id,
            'ref_cod_turma' => $schoolClass->id,
        ]);
        $instance = new $this->model();

        return $instance->query()->find($person->id)->getAttributes();
    }
}
