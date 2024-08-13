<?php

namespace App\Models\Builders;

class LegacyRoleBuilder extends LegacyBuilder
{
    public function ativo(): self
    {
        return $this->where('ativo', 1);
    }

    public function professor(): self
    {
        return $this->where('professor', 1);
    }
}
