<?php

namespace App\Models\Builders;

class EmployeeGraduationBuilder extends LegacyBuilder
{
    /**
     * Filtra pelo ID do servidor
     */
    public function ofEmployee(?int $employeeId): self
    {
        return $this->where('employee_id', $employeeId);
    }
}
