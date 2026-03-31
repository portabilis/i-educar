<?php

namespace App\Models\Builders;

class LegacyRoleBuilder extends LegacyBuilder
{
    /** @deprecated  */
    public function ativo(): self
    {
        return $this->whereIsActive();
    }

    /** @deprecated */
    public function professor(): self
    {
        return $this->whereIsTeacher();
    }

    public function whereIsActive(): self
    {
        return $this->where('ativo', 1);
    }

    public function whereIsTeacher(): self
    {
        return $this->where('professor', 1);
    }
}
