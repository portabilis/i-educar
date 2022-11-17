<?php

namespace App\Models\Builders;

class DistrictBuilder extends LegacyBuilder
{
    /**
     * @param string $name
     *
     * @return $this
     */
    public function whereName(string $name): self
    {
        return $this->whereRaw('unaccent(name) ~* unaccent(?)', $name);
    }

    /**
     * Filtra pelo Id do País
     *
     * @param int $countryId
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
