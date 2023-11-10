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
    public function activeBySituation(?int $situation): self
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
            $q->orWhere('falecido', true);
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

    public function addJoinViewSituacaoRelatorios(int $situation): self
    {
        return $this->join('relatorio.view_situacao_relatorios', function ($join) use ($situation) {
            $join->on('view_situacao_relatorios.cod_matricula', 'ref_cod_matricula');
            $join->on('view_situacao_relatorios.cod_turma', 'ref_cod_turma');
            $join->on('view_situacao_relatorios.sequencial', 'matricula_turma.sequencial');
            $join->where('view_situacao_relatorios.cod_situacao', $situation);
        })->addSelect([
            'view_situacao_relatorios.texto_situacao',
        ]);
    }

    public function wherePeriod(int $period): self
    {
        return $this->where(function ($q) use ($period) {
            $q->where('turno_id', $period);
            $q->orWhere(function ($q) use ($period) {
                $q->whereNull('turno_id');
                $q->whereHas('schoolClass', fn ($q) => $q->where('turma_turno_id', $period));
            });
        });
    }
}
