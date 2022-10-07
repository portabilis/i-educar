<?php

namespace Database\Factories;

use App\Models\LegacySchoolGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolGradeFactory extends Factory
{
    protected $model = LegacySchoolGrade::class;

    public function definition(): array
    {
        $schoolCourse = LegacySchoolCourseFactory::new()->create();

        return [
            'ref_cod_escola' => $schoolCourse->school,
            'ref_cod_serie' => fn () => LegacyGradeFactory::new()->create([
                'ref_cod_curso' => $schoolCourse->course,
            ]),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->unique()->make(),
            'data_cadastro' => now(),
            'ativo' => 1,
            'anos_letivos' => $schoolCourse->anos_letivos,
        ];
    }
}
