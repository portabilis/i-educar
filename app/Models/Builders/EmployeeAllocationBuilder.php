<?php

namespace App\Models\Builders;

class EmployeeAllocationBuilder extends LegacyBuilder
{
    /**
     * Filtra por Ano
     */
    public function whereYearEq(int $year): self
    {
        return $this->where('ano', $year);
    }

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
        return $this->where($this->model->getTable() . '.ref_ref_cod_instituicao', $institution);
    }

    /**
     * Filtra por ativo
     */
    public function active(): self
    {
        return $this->where($this->model->getTable() . '.ativo', 1);
    }
}
