<?php

namespace App\Models\Builders;

class LegacyAbsenceDelayBuilder extends LegacyBuilder
{
    /**
     * Filtra por Instituição
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('ref_ref_cod_instituicao', $institution);
    }

    /**
     * Filtra por Escola
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_cod_escola', $school);
    }

    /**
     * Filtra por Servidor
     */
    public function whereEmployee(int $employee): self
    {
        return $this->where('ref_cod_servidor', $employee);
    }

    /**
     * Filtra por Servidor
     */
    public function whereJustified(int $justified): self
    {
        return $this->where('justificada', $justified);
    }

    /**
     * Filtra por Data
     */
    public function whereDateBetween($start, $end): self
    {
        return $this->whereBetween('data_falta_atraso', [
            $start,
            $end,
        ]);
    }
}
