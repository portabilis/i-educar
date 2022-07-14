<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class LegacyEducationNetworkBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        $this->active()->orderByName()->filter($filters);
        return $this->resource(['id', 'name']);
    }

    /**
     * Filtra por Instituição
     *
     * @param int $institution
     * @return $this
     */
    public function filterInstitution(int $institution): self
    {
        return $this->whereInstitution($institution);
    }
}
