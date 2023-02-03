<?php

namespace App\Models\Builders;

class DisciplineBuilder extends LegacyBuilder
{
    /**
     * @param string $name
     *
     * @return $this
     */
    public function whereName(string $name): self
    {
        return $this->whereRaw('unaccent(nome) ~* unaccent(?)', $name);
    }
}
