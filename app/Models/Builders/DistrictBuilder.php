<?php

namespace App\Models\Builders;

class DistrictBuilder extends LegacyBuilder
{
    /**
     * @param string $name
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
}
