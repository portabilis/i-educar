<?php

namespace App\Models\Builders;

class LegacyEmployeeRoleBuilder extends LegacyBuilder
{
    /**
     * Filtra por Instituição
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('ref_ref_cod_instituicao', $institution);
    }

    /**
     * Filtra por Servidor
     */
    public function whereEmployee(int $employee): self
    {
        return $this->where('ref_cod_servidor', $employee);
    }

    /**
     * Filtra por Função
     */
    public function whereRole(int $role): self
    {
        return $this->where('ref_cod_funcao', $role);
    }
}
