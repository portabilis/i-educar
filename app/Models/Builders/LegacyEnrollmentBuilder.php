<?php

namespace App\Models\Builders;

use App\Models\RegistrationStatus;

class LegacyEnrollmentBuilder extends LegacyBuilder
{
    /**
     * Filtra por ativo
     */
    public function active(): self
    {
        return $this->where('matricula_turma.ativo', 1);
    }

    /**
     * Filtra por ativo por situação
     */
    public function activeBySituation(int|null $situation): self
    {
        if ($situation && !in_array($situation, RegistrationStatus::getStatusInactive(), true)) {
            $this->active();
        }

        return $this->whereValid();
    }

    /**
     * Filtra por não ativo
     */
    public function notActive(): self
    {
        return $this->where('matricula_turma.ativo', 0);
    }

    /**
     * Filtra por validos
     */
    public function whereValid(): self
    {
        return $this->where(function ($q) {
            $q->active();
            $q->orWhere('transferido', true);
            $q->orWhere('remanejado', true);
            $q->orWhere('reclassificado', true);
            $q->orWhere('abandono', true);
            $q->orWhereHas('registration', fn ($q) => $q->where('dependencia', true));
        });
    }

    /**
     * Filtra por Turma
     */
    public function whereSchoolClass(int $schoolClass): self
    {
        return $this->where('ref_cod_turma', $schoolClass);
    }
}
