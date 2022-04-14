<?php

namespace Database\Factories;

use App\Models\LegacyDisciplineAcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacyDisciplineAcademicYearFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacyDisciplineAcademicYear::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'componente_curricular_id' => LegacyDisciplineFactory::new()->create(),
            'ano_escolar_id' => LegacySchoolFactory::new()->create(),
            'carga_horaria' => 100,
            'tipo_nota' => $this->faker->randomElement([1, 2]),
            'anos_letivos' => '{' . now()->year . '}',
        ];
    }
}
