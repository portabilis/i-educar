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

    protected function getEloquentModelName(): string
    {
        return EmployeeInep::class;
    }

    /** @test */
    public function getNumberAttribute(): void
    {
        $this->assertEquals($this->model->cod_docente_inep, $this->model->number);
    }
}
