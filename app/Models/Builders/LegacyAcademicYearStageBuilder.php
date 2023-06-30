<?php

namespace App\Models\Builders;

class LegacyAcademicYearStageBuilder extends LegacyBuilder
{
    /**
     * Filtra por anos maiores e iguais
     */
    public function whereYearGte(int $year): self
    {
        return $this->where('ref_ano', '>=', $year);
    }

    /**
     * Filtra por ano
     */
    public function whereYearEq(int $year): self
    {
        return $this->where('ref_ano', $year);
    }

    /**
     * Filtra por escola
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_ref_cod_escola', $school);
    }

    /**
     * Ordena por Sequencial
     */
    public function orderBySequencial(): self
    {
        return $this->orderBy('sequencial');
    }
}
