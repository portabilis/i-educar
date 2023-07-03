<?php

namespace App\Models\Builders;

class DistrictBuilder extends LegacyBuilder
{
    /**
     * @return $this
     */
    public function whereName(string $name): self
    {
        return $this->whereRaw('unaccent(name) ~* unaccent(?)', $name);
    }

    /**
     * Filtra pelo Id do País
     *
     *
     * @return $this
     */
    public function whereCountryId(int $countryId): self
    {
        return $this->whereHas('city.state', function ($q) use ($countryId) {
            $q->where('country_id', $countryId);
        });
    }
}
