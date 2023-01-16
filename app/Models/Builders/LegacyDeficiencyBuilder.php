<?php

namespace App\Models\Builders;

class LegacyDeficiencyBuilder extends LegacyBuilder
{
    public function whereName(string $name): self
    {
        return $this->whereRaw('unaccent(nm_deficiencia) ~* unaccent(?)', $name);
    }
}
