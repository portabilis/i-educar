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
                if ($situation === 'with_cpf') {
                    $query->whereNotNull('cpf');
                    $query->where('cpf', '<>', 0);
                }

                if ($situation === 'justified_absence') {
                    $query->where(fn ($cpf) => $cpf->whereNull('cpf')->orWhere('cpf', 0));
                    $query->whereHas('documentAbsenceReason', function ($reason) {
                        $reason->withTrashed();
                        $reason->where('from_system', false);
                    });
                }

                if ($situation === 'unjustified_absence') {
                    $query->where(fn ($cpf) => $cpf->whereNull('cpf')->orWhere('cpf', 0));
                    $query->where(function ($absence) {
                        $absence->whereNull('document_absence_reason_id');
                        $absence->orWhereHas('documentAbsenceReason', function ($reason) {
                            $reason->withTrashed();
                            $reason->where('from_system', true);
                        });
                    });
                }
            }
        );
    }

    public function whereAbsenceReason(int $reasonId): self
    {
        return $this->whereHas(
            'individual',
            function ($query) use ($reasonId) {
                $query->where(fn ($cpf) => $cpf->whereNull('cpf')->orWhere('cpf', 0));
                $query->where('document_absence_reason_id', $reasonId);
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
