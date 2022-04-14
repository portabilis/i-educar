<?php

namespace Database\Factories;

use App\Models\LegacySchoolCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolCourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolCourse::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'ref_cod_escola' => LegacySchoolFactory::new()->create(),
            'ref_cod_curso' => LegacyCourseFactory::new()->create(),
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'data_cadastro' => now(),
            'ativo' => 1,
            'autorizacao' => $this->faker->sentence,
            'anos_letivos' => '{' . now()->format('Y') . '}',
        ];
    }
}
