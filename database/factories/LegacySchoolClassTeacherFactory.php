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
     */
    public function definition(): array
    {
        return [
            'ano' => now()->year,
            'instituicao_id' => fn () => LegacyInstitutionFactory::new()->current(),
            'turma_id' => fn () => LegacySchoolClassFactory::new()->create(),
            'servidor_id' => EmployeeFactory::new()->create(),
            'funcao_exercida' => 1,
            'tipo_vinculo' => 2,
            'turno_id' => fn () => LegacyPeriodFactory::new()->create(),
        ];
    }
}
