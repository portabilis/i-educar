<?php

namespace App\Models\Builders;

use App\Models\Enums\PersonLinkType;

class LegacyPersonBuilder extends LegacyBuilder
{
    public function whereLinkType(?string $linkType): self
    {
        $relation = match (PersonLinkType::tryFrom($linkType ?? '')) {
            PersonLinkType::STUDENT => 'student',
            PersonLinkType::EMPLOYEE => 'employee',
            PersonLinkType::MOTHER => 'childrenAsMother',
            PersonLinkType::FATHER => 'childrenAsFather',
            PersonLinkType::RESPONSIBLE => 'dependentsAsResponsible',
            default => null,
        };

        if (!$relation) {
            return $this;
        }

        return in_array($relation, ['student', 'employee'], true)
            ? $this->whereHas($relation, fn ($query) => $query->active())
            : $this->whereHas($relation);
    }

    public function whereName(string $name): self
    {
        return $this->whereRaw('f_unaccent(nome) ILIKE f_unaccent(?)', ["%{$name}%"]);
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
