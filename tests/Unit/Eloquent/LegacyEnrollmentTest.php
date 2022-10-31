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
    public function getRegistrationIdAttribute()
    {
        $this->assertEquals($this->model->ref_cod_matricula, $this->model->registration_id);
    }

    /** @test */
    public function getStudentNameAttribute()
    {
        $this->assertEquals($this->model->registration->student->person->nome, $this->model->student_name);
    }

    /** @test */
    public function getDateAttribute()
    {
        $this->assertEquals($this->model->data_enturmacao, $this->model->date);
    }

    /** @test */
    public function getDateDepartedAttribute()
    {
        $this->assertEquals($this->model->date_departed, $this->model->data_exclusao);
    }

    /** @test */
    public function getSchoolClassIdAttribute()
    {
        $this->assertEquals($this->model->ref_cod_turma, $this->model->school_class_id);
    }

    /** @test */
    public function getStudentId()
    {
        $this->assertEquals($this->model->registration->student->cod_aluno, $this->model->getStudentId());
    }
}
