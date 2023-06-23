<?php

namespace Tests\Unit\View;

use App\Models\Registration;
use App\Models\RegistrationStatus;
use App\Models\Student;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyStudentFactory;
use Tests\ViewTestCase;

class RegistrationTest extends ViewTestCase
{
    protected $relations = [
        'student' => Student::class,
    ];

    protected function getViewModelName(): string
    {
        return Registration::class;
    }

    public function testStatusDescription(): void
    {
        $expected = (new RegistrationStatus())->getDescriptiveValues()[(int) $this->model->status];
        $this->assertEquals($expected, $this->model->status_description);
    }

    public function testDoesntHaveFather(): void
    {
        //registration with father
        $individual2 = LegacyIndividualFactory::new()->father()->create();
        $student2 = LegacyStudentFactory::new()->create(['ref_idpes' => $individual2]);
        $this->factory->forView($student2->cod_aluno)->make();

        $collection = $this->instanceNewViewModel()->doesntHaveFather()->get();
        $found = array_intersect_key($this->model->getAttributes(), $collection->first()->getAttributes());

        $this->assertCount(1, $collection);
        $this->assertEquals($this->model->getAttributes(), $found);
    }

    public function testStudentIsActive(): void
    {
        $student = LegacyStudentFactory::new()->inactive()->create();
        $registration = $this->factory->forView($student->cod_aluno)->make();

        $collection = $this->instanceNewViewModel()->studentIsActive()->get();

        $this->assertCount(1, $collection);
    }

    public function testSchool(): void
    {
        $this->makeNewModel();
        $collection = $this->instanceNewViewModel()->school($this->model->school_id)->get();

        $this->assertCount(1, $collection);
    }

    public function testYear(): void
    {
        $registration = LegacyRegistrationFactory::new()->create(['ano' => $this->model->year - 1]);
        $collection = $this->instanceNewViewModel()->year($this->model->year)->get();

        $this->assertCount(1, $collection);
    }

    public function testInProgress(): void
    {
        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 1]);
        $collection = $this->instanceNewViewModel()->inProgress()->get();

        $this->assertCount(1, $collection);
    }
}
