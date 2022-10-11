<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\EmployeeInep;
use Tests\EloquentTestCase;

class EmployeeInepTest extends EloquentTestCase
{
    public $relations = [
        'employee' => Employee::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EmployeeInep::class;
    }

    public function testGetNumberAttribute()
    {
        $employeeInep = $this->createNewModel();
        $this->assertEquals($employeeInep->cod_docente_inep, $employeeInep->getNumberAttribute());
    }
}
