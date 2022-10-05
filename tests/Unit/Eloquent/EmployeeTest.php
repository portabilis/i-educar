<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\EmployeeInep;
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
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Employee::class;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->employee = EmployeeFactory::new()->create();
        $this->employee->inep = EmployeeInepFactory::new()->create([
            'cod_servidor' => $this->employee->cod_servidor,
        ]);
    }

    public function testRelationshipInep()
    {
        $this->assertInstanceOf(EmployeeInep::class, $this->employee->inep);
    }
}
