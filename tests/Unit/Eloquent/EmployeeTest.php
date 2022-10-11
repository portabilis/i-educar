<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\EmployeeAllocation;
use App\Models\EmployeeGraduation;
use App\Models\EmployeeInep;
use App\Models\LegacyEmployeeRole;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacySchoolingDegree;
use Database\Factories\EmployeeFactory;
use Database\Factories\EmployeeInepFactory;
use Database\Factories\LegacyIndividualFactory;
use Tests\EloquentTestCase;

class EmployeeTest extends EloquentTestCase
{
    protected $relations = [
        'person' => LegacyPerson::class,
        'employeeAllocations' => [EmployeeAllocation::class],
        'employeeRoles' => [LegacyEmployeeRole::class],
        'graduations' => [EmployeeGraduation::class],
        'schoolingDegree' => LegacySchoolingDegree::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Employee::class;
    }

    public function testRelationshipInep()
    {
        $employee = EmployeeFactory::new()->create();
        $employee->inep = EmployeeInepFactory::new()->create([
            'cod_servidor' => $employee->id,
        ]);
        $this->assertInstanceOf(EmployeeInep::class, $employee->inep);
    }

    public function testRelationshipIndividual()
    {
        $employee = EmployeeFactory::new()->create();
        $employee->individual = LegacyIndividualFactory::new()->create([
            'idpes' => $employee->id,
        ]);
        $this->assertInstanceOf(LegacyIndividual::class, $employee->individual);
    }

    public function testGetIdAttribute()
    {
        $employee = $this->createNewModel();

        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals($employee->id, $employee->getIdAttribute());
        $this->assertIsInt($employee->getIdAttribute());
        $this->assertEquals($employee->cod_servidor, $employee->id);
    }

    public function testScopeProfessor()
    {
        try {
            Employee::query()
                ->professor()
                ->first();
            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail("Exception thrown due to scope error");
        }
    }

    public function testScopeLastYear()
    {
        try {
            Employee::query()
                ->lastYear()
                ->first();

            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail("Exception thrown due to scope error");
        }
    }

    public function testScopeCurrentYear()
    {
        try {
            Employee::query()
                ->currentYear()
                ->first();

            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail("Exception thrown due to scope error");
        }
    }
}
