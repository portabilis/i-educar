<?php

namespace Database\Factories;

use App\Models\LegacySchoolClassTeacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolClassTeacherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolClassTeacher::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ano' => now()->year,
            'instituicao_id' => LegacyInstitutionFactory::new()->current(),
            'turma_id' => LegacySchoolClassFactory::new()->create(),
            'servidor_id' => EmployeeFactory::new()->create(),
            'funcao_exercida' => 1,
            'tipo_vinculo' => 2,
            'turno_id' => LegacyPeriodFactory::new()->create()
        ];
    }
}
