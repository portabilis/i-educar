<?php

namespace Database\Factories;

use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

class LegacySchoolGradeFactory extends Factory
{
    protected $model = LegacySchoolGrade::class;

    protected LegacySchoolCourse $schoolCourse;

    public function definition(): array
    {
        $schoolCourse = $this->getSchoolCourse();

        return [
            'ref_cod_escola' => $schoolCourse->school,
            'ref_cod_serie' => fn () => LegacyGradeFactory::new()->create([
                'ref_cod_curso' => $schoolCourse->course,
            ]),
            'ref_usuario_cad' => fn () => LegacyUserFactory::new()->current(),
            'data_cadastro' => now(),
            'ativo' => 1,
            'anos_letivos' => $schoolCourse->anos_letivos,
        ];
    }

    public function useSchoolCourse(LegacySchoolCourse $schoolCourse): static
    {
        $this->schoolCourse = $schoolCourse;

        return $this;
    }

    public function getSchoolCourse()
    {
        if (empty($this->schoolCourse)) {
            return LegacySchoolCourseFactory::new()->create();
        }

        return $this->schoolCourse;
    }
}
