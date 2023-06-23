<?php

namespace App\Models\Builders;

class LegacyInstitutionBuilder extends LegacyBuilder
{
    /**
     * Filtra por Instituição
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('cod_instituicao', $institution);
    }

    /**
     * Filtra por nome
     */
    public function whereName(string $name): self
    {
        return $this->whereRaw('unaccent(nm_instituicao) ~* unaccent(?)', $name);
    }

    /**
     * Ordena por nome
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->orderBy('nm_instituicao', $direction);
    }

    /**
     * Filtra por ativo
     */
    public function active(): self
    {
        return $this->where($this->model->getTable().'.ativo', 1);
    }
}
