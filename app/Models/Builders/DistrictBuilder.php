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
        return $this->whereRaw('name ~* unaccent(?)', $name);
    }
}
