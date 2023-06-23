<?php

namespace Database\Factories;

use App\Models\LegacySchoolClassTeacherDiscipline;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolClassTeacherDisciplineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolClassTeacherDiscipline::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'professor_turma_id' => fn () => LegacySchoolClassTeacherFactory::new()->create(),
            'componente_curricular_id' => fn () => LegacyDisciplineFactory::new()->create(),
        ];
    }
}
