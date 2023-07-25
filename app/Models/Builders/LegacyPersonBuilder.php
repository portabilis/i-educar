<?php

namespace App\Models\Builders;

class LegacyPersonBuilder extends LegacyBuilder
{
    public function whereName(string $name): self
    {
        return $this->whereRaw('slug ~* unaccent(?)', $name);
    }

    public function whereCpf(string $cpf): self
    {
        return $this->whereHas(
            'individual',
            function ($query) use ($cpf) {
                $query->when($cpf, fn ($q) => $q->where('cpf', $cpf));
            }
        );
    }

    public function active(): self
    {
        return $this->whereHas(
            'individual',
            function ($query) {
                $query->where('ativo', 1);
            }
        );
    }

    public function whereDeficiencyTypes(string $deficiencyTypes): self
    {
        return $this->whereHas('deficiencies', fn ($q) => $q->whereIn('deficiency_type_id', explode(',', $deficiencyTypes)));
    }
}
