<?php

namespace App\Models\Builders;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class CountryBuilder extends LegacyBuilder
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
        return $this->resource(['id as value', 'name as label']);
    }

    /**
     * Filtra por nome do curso
     *
     * @param string $name
     *
     * @return $this
     */
    public function whereName(string $name): self
    {
        return $this->whereRaw('unaccent(name) ~* unaccent(?)', $name);
    }

    /**
     * Filtra por nome e id do país
     *
     * @param string $search
     * @return $this
     */
    public function whereSearch(string $search): self
    {
        return $this->where(static function ($q) use ($search) {
            $q->whereName($search);
            $q->when(is_numeric($search), static function ($q) use ($search) {
                $q->orWhere(static fn($q) => $q->whereKey($search));
            });
        });
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
     * @param string $direction
     *
     * @return $this
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->orderBy('name', $direction);
    }
}
