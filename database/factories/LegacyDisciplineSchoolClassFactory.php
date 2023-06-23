<?php

namespace Database\Factories;

use App\Models\LegacyDisciplineSchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineSchoolClassFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyDisciplineSchoolClass::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'componente_curricular_id' => fn () => LegacyDisciplineFactory::new()->create(),
            'ano_escolar_id' => fn () => LegacyGradeFactory::new()->create(),
            'escola_id' => fn () => LegacySchoolFactory::new()->create(),
            'turma_id' => fn () => LegacySchoolClassFactory::new()->create(),
            'carga_horaria' => $this->faker->randomNumber(3),
            'docente_vinculado' => 0,
            'etapas_especificas' => 0,
            'etapas_utilizadas' => '',
        ];
    }
}
