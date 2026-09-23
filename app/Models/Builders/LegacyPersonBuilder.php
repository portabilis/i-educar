<?php

namespace App\Models\Builders;

class LegacyPersonBuilder extends LegacyBuilder
{
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

    public function whereCpfSituation(string $situation): self
    {
        return $this->whereHas(
            'individual',
            function ($query) use ($situation) {
                match ($situation) {
                    'with_cpf' => $query->whereNotNull('cpf'),
                    'justified_absence' => $query
                        ->whereNull('cpf')
                        ->whereHas('documentAbsenceReason', fn ($reason) => $reason->withTrashed()->where('from_system', false)),
                    'unjustified_absence' => $query
                        ->whereNull('cpf')
                        ->where(fn ($absence) => $absence
                            ->whereNull('document_absence_reason_id')
                            ->orWhereHas('documentAbsenceReason', fn ($reason) => $reason->withTrashed()->where('from_system', true))),
                    default => $query,
                };
            }
        );
    }

    public function whereAbsenceReason(int $reasonId): self
    {
        return $this->whereHas(
            'individual',
            fn ($query) => $query->whereNull('cpf')->where('document_absence_reason_id', $reasonId)
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
