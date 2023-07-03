<?php

namespace App\Models\Builders;

class StateBuilder extends LegacyBuilder
{
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
}
