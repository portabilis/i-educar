<?php

namespace App\Models\Builders;

class EmployeeAllocationBuilder extends LegacyBuilder
{
    /**
     * Filtra por Escola
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_cod_escola', $school);
    }

    /**
     * Filtra por Servidor
     */
    public function whereEmployee(int $employee): self
    {
        return $this->where('ref_cod_servidor', $employee);
    }

    /**
     * Filtra por Instituição
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('ref_ref_cod_instituicao', $institution);
    }
}
