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
use Tests\EloquentTestCase;

class EmployeeTest extends EloquentTestCase
{
    protected $relations = [
        'person' => LegacyPerson::class,
        'employeeAllocations' => [EmployeeAllocation::class],
        'employeeRoles' => [LegacyEmployeeRole::class],
        'graduations' => [EmployeeGraduation::class],
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
            'cod_servidor' => $employee->cod_servidor,
        ]);
        $this->assertInstanceOf(EmployeeInep::class, $employee->inep);
    }
}
