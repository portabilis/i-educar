<?php

namespace App\Models\Builders;

class LegacyEnrollmentBuilder extends LegacyBuilder
{
    /**
     * Filtra por ativo
     *
     * @return LegacyEnrollmentBuilder
     */
    public function active(): self
    {
        return $this->where('matricula_turma.ativo', 1);
    }

    /**
     * Filtra por não ativo
     *
     * @return LegacyEnrollmentBuilder
     */
    public function notActive(): self
    {
        return $this->where('matricula_turma.ativo', 1);
    }

    /**
     * Filtra por validos
     *
     * @return LegacyEnrollmentBuilder
     */
    public function valid(): self
    {
        return $this->where(function ($q) {
            $q->active();
            $q->orWhere(static function ($q) {
                $q->notActive();
                $q->where(function ($q) {
                    $q->where('transferido', true);
                    $q->orWhere('remanejado', true);
                    $q->orWhere('reclassificado', true);
                    $q->orWhere('abandono', true);
                });
            });
        });
    }
}
