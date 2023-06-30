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

    public function testGetGuardianTypeAttribute(): void
    {
        $this->assertEquals(3, $this->model->guardian_type);
    }

    public function testGetGuardianTypeFatherAttribute(): void
    {
        $student = $this->factory->father()->make();

        $this->assertEquals(1, $student->guardian_type);
    }

    public function testGetGuardianTypeMotherAttribute(): void
    {
        $student = $this->factory->mother()->make();

        $this->assertEquals(2, $student->guardian_type);
    }

    public function testGetGuardianTypeOtherAttribute(): void
    {
        $student = $this->factory->guardian()->make();

        $this->assertEquals(4, $student->guardian_type);
    }

    public function testGetNullGuardianTypeAttribute(): void
    {
        $student = $this->factory->noGuardian()->make();

        $this->assertNull($student->guardian_type);
    }

    public function testGetGuardianTypeDescriptionAttribute(): void
    {
        $expected = (new GuardianType())->getDescriptiveValues()[(int) $this->model->guardian_type];
        $this->assertNotNull($this->model->guardian_type_description);
        $this->assertEquals($expected, $this->model->guardian_type_description);
    }

    public function testGetTransportationProviderDescriptionAttribute(): void
    {
        $expected = (new TransportationProvider())->getDescriptiveValues()[(int) $this->model->transportation_provider];

        $this->assertNotNull($this->model->transportation_provider_description);
        $this->assertEquals($expected, $this->model->transportation_provider_description);
    }

    public function testGetTransportationVehicleTypeDescriptionAttribute(): void
    {
        $this->assertNull($this->model->transportation_vehicle_type_description);
        $this->assertEquals(0, $this->model->transportation_vehicle_type_description);
    }
}
