<?php

namespace Database\Factories;

use App\Models\LegacyLevel;
use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGrade;
use App\Models\LegacyUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolGradeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LegacySchoolGrade::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $schoolCourse = LegacySchoolCourse::factory()->create();

        return [
            'ref_cod_escola' => $schoolCourse->school,
            'ref_cod_serie' => LegacyLevel::factory()->create([
                'ref_cod_curso' => $schoolCourse->course,
            ]),
            'ref_usuario_cad' => LegacyUser::factory()->unique()->make(),
            'data_cadastro' => now(),
            'ativo' => 1,
            'anos_letivos' => $schoolCourse->anos_letivos,
        ];
    }
}
