<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\EmployeeAllocation;
use App\Models\EmployeeGraduation;
use App\Models\EmployeeInep;
use App\Models\LegacyEmployeeRole;
use App\Models\LegacyIndividual;
use App\Models\LegacyInstitution;
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
        'employeeAllocations' => EmployeeAllocation::class,
        'employeeRoles' => LegacyEmployeeRole::class,
        'graduations' => EmployeeGraduation::class,
        'schoolingDegree' => LegacySchoolingDegree::class,
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Employee::class;
    }

    /** @test  */
    public function relationshipInep()
    {
        $employee = EmployeeFactory::new()->create();
        $employee->inep = EmployeeInepFactory::new()->create([
            'cod_servidor' => $employee->id,
        ]);
        $this->assertInstanceOf(EmployeeInep::class, $employee->inep);
    }

    /** @test  */
    public function relationshipIndividual()
    {
        $employee = EmployeeFactory::new()->create();
        $employee->individual = LegacyIndividualFactory::new()->create([
            'idpes' => $employee->id,
        ]);
        $this->assertInstanceOf(LegacyIndividual::class, $employee->individual);
    }

    /** @test  */
    public function getIdAttribute()
    {
        $employee = $this->createNewModel();

        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals($employee->id, $employee->id);
        $this->assertIsInt($employee->id);
        $this->assertEquals($employee->cod_servidor, $employee->id);
    }

    /** @test  */
    public function scopeProfessor()
    {
        try {
            Employee::query()
                ->professor()
                ->first();
            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail('Exception thrown due to scope error');
        }
    }

    /** @test  */
    public function scopeLastYear()
    {
        try {
            Employee::query()
                ->lastYear()
                ->first();

            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail('Exception thrown due to scope error');
        }
    }

    /** @test  */
    public function scopeCurrentYear()
    {
        try {
            Employee::query()
                ->currentYear()
                ->first();

            $this->assertTrue(true);
        } catch (\Exception $exception) {
            $this->fail('Exception thrown due to scope error');
        }
    }
}