<?php

namespace Database\Factories;

use App\Models\LegacySchoolGrade;
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
        $schoolCourse = LegacySchoolCourseFactory::new()->create();

        return [
            'ref_cod_escola' => $schoolCourse->school,
            'ref_cod_serie' => LegacyLevelFactory::new()->create([
                'ref_cod_curso' => $schoolCourse->course,
            ]),
            'ref_usuario_cad' => LegacyUserFactory::new()->unique()->make(),
            'data_cadastro' => now(),
            'ativo' => 1,
            'anos_letivos' => $schoolCourse->anos_letivos,
        ];
    }
}
