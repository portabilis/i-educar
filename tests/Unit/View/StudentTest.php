<?php

namespace Tests\Unit\View;

use App\Models\GuardianType;
use App\Models\Individual;
use App\Models\LogUnification;
use App\Models\Registration;
use App\Models\Religion;
use App\Models\Student;
use App\Models\TransportationProvider;
use Tests\ViewTestCase;

class StudentTest extends ViewTestCase
{
    protected $relations = [
        'individual' => Individual::class,
        'religion' => Religion::class,
        'registrations' => Registration::class,
        'createdBy' => Individual::class,
        'deletedBy' => Individual::class,
        'unification' => LogUnification::class,
    ];

    protected function getViewModelName(): string
    {
        return Student::class;
    }

    public function test_get_guardian_type_attribute(): void
    {
        $this->assertEquals(3, $this->model->guardian_type);
    }

    public function test_get_guardian_type_father_attribute(): void
    {
        $student = $this->factory->father()->make();

        $this->assertEquals(1, $student->guardian_type);
    }

    public function test_get_guardian_type_mother_attribute(): void
    {
        $student = $this->factory->mother()->make();

        $this->assertEquals(2, $student->guardian_type);
    }

    public function test_get_guardian_type_other_attribute(): void
    {
        $student = $this->factory->guardian()->make();

        $this->assertEquals(4, $student->guardian_type);
    }

    public function test_get_null_guardian_type_attribute(): void
    {
        $student = $this->factory->noGuardian()->make();

        $this->assertNull($student->guardian_type);
    }

    public function test_get_guardian_type_description_attribute(): void
    {
        $expected = (new GuardianType)->getDescriptiveValues()[(int) $this->model->guardian_type];
        $this->assertNotNull($this->model->guardian_type_description);
        $this->assertEquals($expected, $this->model->guardian_type_description);
    }

    public function test_get_transportation_provider_description_attribute(): void
    {
        $expected = (new TransportationProvider)->getDescriptiveValues()[(int) $this->model->transportation_provider];

        $this->assertNotNull($this->model->transportation_provider_description);
        $this->assertEquals($expected, $this->model->transportation_provider_description);
    }

    public function test_get_transportation_vehicle_type_description_attribute(): void
    {
        $this->assertNull($this->model->transportation_vehicle_type_description);
        $this->assertEquals(0, $this->model->transportation_vehicle_type_description);
    }
}
