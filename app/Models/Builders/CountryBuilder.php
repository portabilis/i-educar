<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class CountryBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
     */
    public function getResource(array $filters = []): Collection
    {
        $this->active()->orderByName()->filter($filters);

        return $this->resource(['id as value', 'name as label']);
    }

    /**
     * Filtra por nome do curso
     *
     *
     * @return $this
     */
    public function whereName(string $name): self
    {
        return $this->whereRaw('unaccent(name) ~* unaccent(?)', $name);
    }

    /**
     * Filtra por ativo
     *
     * @return $this
     */
    public function active(): self
    {
        return $this->whereNull('deleted_at');
    }

    /**
     * Ordena por nome
     *
     *
     * @return $this
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->orderBy('name', $direction);
    }
}
