<?php

namespace Database\Factories;

use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
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

    public function withEnrollment(LegacySchoolClass $schoolClass): static
    {
        return $this->state([
            'ref_ref_cod_serie' => $schoolClass->grade_id,
            'ref_ref_cod_escola' => $schoolClass->school_id,
            'ref_cod_curso' => $schoolClass->course_id,
        ]);
    }
}
