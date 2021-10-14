<?php

namespace Database\Factories;

use App\Models\LegacyCourse;
use App\Models\LegacySchool;
use App\Models\LegacySchoolCourse;
use App\Models\LegacyUser;
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
            'ref_cod_escola' => LegacySchool::factory()->create(),
            'ref_cod_curso' => LegacyCourse::factory()->create(),
            'ref_usuario_cad' => LegacyUser::factory()->unique()->make(),
            'data_cadastro' => now(),
            'ativo' => 1,
            'autorizacao' => $this->faker->sentence,
            'anos_letivos' => '{' . now()->format('Y') . '}',
        ];
    }
}
