<?php

namespace App\Models\Builders;

class LegacySchoolYearLockBuilder extends LegacyBuilder
{
    /**
     * Filtra por instituição
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('pmieducar.bloqueio_ano_letivo.ref_cod_instituicao', $institution);
    }

    /**
     * Filtra por ano
     */
    public function whereYear(int $year): self
    {
        return $this->where('pmieducar.bloqueio_ano_letivo.ref_ano', $year);
    }
}
