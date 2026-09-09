<?php

namespace App\Models\Builders;

class DocumentAbsenceReasonBuilder extends LegacyBuilder
{
    public function whereName(string $name): self
    {
        return $this->whereRaw('f_unaccent(name) ILIKE f_unaccent(?)', ["%{$name}%"]);
    }
}
