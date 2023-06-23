<?php

namespace Database\Factories\Exporter;

use App\Models\Exporter\Teacher;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyCourseFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyPersonFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolClassTeacherFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        $person = LegacyPersonFactory::new()->create();
        LegacyIndividualFactory::new()->create([
            'idpes' => $person->idpes,
            'ativo' => 1,
        ]);
        EmployeeFactory::new()->create([
            'cod_servidor' => $person,
        ]);
        $school = LegacySchoolFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $school,
            'ref_cod_serie' => $grade,
        ]);
        $course = LegacyCourseFactory::new()->create();
        $schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_serie' => $grade,
            'ref_cod_curso' => $course,
            'ref_ref_cod_escola' => $school,
        ]);
        LegacySchoolClassTeacherFactory::new()->create([
            'turma_id' => $schoolClass,
            'servidor_id' => $person,
        ]);
        $instance = new $this->model();

        return $instance->query()->find($person->id)->getAttributes();
    }
}
