<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\EmployeeInep;
use App\Models\EmployeeWithdrawal;
use App\Models\LegacyDiscipline;
use Database\Factories\EmployeeFactory;
use Database\Factories\EmployeeInepFactory;
use Tests\EloquentTestCase;

class EmployeeWithdrawalTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return EmployeeWithdrawal::class;
    }
}
