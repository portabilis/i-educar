<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;

class EmployeePosgraduateBuilder extends LegacyBuilder
{
    /**
     * Filtra pelo ID do servidor
     */
    public function ofEmployee(int $employeeId): self
    {
        return $this->where('employee_id', $employeeId);
    }
}
