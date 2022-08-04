<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacyEducationNetworkBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     *
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        $this->active()->orderByName()->filter($filters);

        return $this->resource(['id', 'name']);
    }

    /**
     * Ordena por nome
     *
     * @param string $direction
     *
     * @return LegacyEducationNetworkBuilder
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->orderBy('nm_rede', $direction);
    }

    /**
     * Filtra por ativo
     *
     * @return LegacyEducationNetworkBuilder
     */
    public function active(): self
    {
        return $this->where('ativo', 1);
    }

    /**
     * Filtra por Instituição
     *
     * @param int $institution
     *
     * @return LegacyEducationNetworkBuilder
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('ref_cod_instituicao', $institution);
    }
}
