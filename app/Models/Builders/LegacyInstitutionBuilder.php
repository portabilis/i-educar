<?php

namespace App\Models\Builders;

class LegacyInstitutionBuilder extends LegacyBuilder
{
    /**
     * Filtra por Instituição
     *
     * @param int $institution
     *
     * @return LegacyInstitutionBuilder
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('cod_instituicao', $institution);
    }

    /**
     * Filtra por nome
     *
     * @param string $name
     *
     * @return LegacyInstitutionBuilder
     */
    public function whereName(string $name): self
    {
        return $this->whereRaw('unaccent(nm_instituicao) ~* unaccent(?)', $name);
    }

    /**
     * Ordena por nome
     *
     * @param string $direction
     *
     * @return LegacyInstitutionBuilder
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->orderBy('nm_instituicao', $direction);
    }

    /**
     * Filtra por ativo
     *
     * @return LegacyInstitutionBuilder
     */
    public function active(): self
    {
        return $this->where($this->model->getTable().'.ativo', 1);
    }
}
