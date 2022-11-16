<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEnrollment;
use App\Models\LegacyPeriod;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyUser;
use Tests\EloquentTestCase;

class LegacyEnrollmentTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'registration' => LegacyRegistration::class,
        'schoolClass' => LegacySchoolClass::class,
        'period' => LegacyPeriod::class,
        'createdBy' => LegacyUser::class,
        'updatedBy' => LegacyUser::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEnrollment::class;
    }

    /** @test */
    public function attributes()
    {
        $this->assertEquals($this->model->ref_cod_matricula, $this->model->registration_id);
        $this->assertEquals($this->model->registration->student->person->nome, $this->model->student_name);
        $this->assertEquals($this->model->data_enturmacao, $this->model->date);
        $this->assertEquals($this->model->date_departed, $this->model->data_exclusao);
        $this->assertEquals($this->model->ref_cod_turma, $this->model->school_class_id);
        $this->assertEquals($this->model->registration->student->cod_aluno, $this->model->getStudentId());
    }
}
