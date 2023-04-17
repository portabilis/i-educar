<?php

namespace Database\Factories;

use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyStudent;
use App_Model_MatriculaSituacao;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyRegistrationFactory extends Factory
{
    protected $model = LegacyRegistration::class;

    public function definition(): array
    {
        return [
            'ref_cod_aluno' => fn () => LegacyStudentFactory::new()->create(),
            'ref_ref_cod_serie' => fn () => LegacyGradeFactory::new()->create(),
            'ref_ref_cod_escola' => fn () => LegacySchoolFactory::new()->create(),
            'ref_cod_curso' => fn () => LegacyCourseFactory::new()->create(),
            'data_cadastro' => now(),
            'ano' => now()->year,
            'ref_usuario_cad' => 1,
            'aprovado' => App_Model_MatriculaSituacao::EM_ANDAMENTO,
        ];
    }

    public function withStudent(LegacyStudent $student): static
    {
        return $this->state([
            'ref_cod_aluno' => $student,
        ]);
    }

    public function withEnrollment(LegacySchoolClass $schoolClass): static
    {
        return $this->state([
            'ref_ref_cod_serie' => $schoolClass->grade_id,
            'ref_ref_cod_escola' => $schoolClass->school_id,
            'ref_cod_curso' => $schoolClass->course_id,
        ])->afterCreating(function (LegacyRegistration $registration) use ($schoolClass) {
            LegacyEnrollmentFactory::new()->create([
                'ref_cod_matricula' => $registration,
                'ref_cod_turma' => $schoolClass,
                'turno_id' => $schoolClass->turma_turno_id,
            ]);
        });
    }
}
