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
    private LegacyEnrollment $legacyEnrollment;

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

    public function setUp(): void
    {
        parent::setUp();

        $this->legacyEnrollment = $this->createNewModel();
    }

    /** @test */
    public function getRegistrationIdAttribute()
    {
        $this->assertEquals($this->legacyEnrollment->getRegistrationIdAttribute(), $this->legacyEnrollment->ref_cod_matricula);
    }

    /** @test */
    public function getStudentNameAttribute()
    {
        $this->assertEquals($this->legacyEnrollment->getStudentNameAttribute(), $this->legacyEnrollment->registration->student->person->nome);
    }

    /** @test */
    public function getDateAttribute()
    {
        $this->assertEquals($this->legacyEnrollment->getDateAttribute(), $this->legacyEnrollment->data_enturmacao);
    }

    /** @test */
    public function getDateDepartedAttribute()
    {
        $this->assertEquals($this->legacyEnrollment->getDateDepartedAttribute(), $this->legacyEnrollment->data_exclusao);
    }

    /** @test */
    public function getSchoolClassIdAttribute()
    {
        $this->assertEquals($this->legacyEnrollment->getSchoolClassIdAttribute(), $this->legacyEnrollment->ref_cod_turma);
    }
}
