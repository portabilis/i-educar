<?php

namespace App\Models\Builders;

class LegacyAcademicYearStageBuilder extends LegacyBuilder
{
    /**
     * Filtra por anos maiores
     *
     * @param int $year
     *
     * @return LegacyAcademicYearStageBuilder
     */
    public function whereYearGte(int $year): self
    {
        return $this->where('ref_ano', '>=', $year);
    }

    /**
     * Filtra por ano
     *
     * @param int $year
     *
     * @return LegacyAcademicYearStageBuilder
     */
    public function whereYearEq(int $year): self
    {
        return $this->where('ref_ano', $year);
    }

    /**
     * Filtra por escola
     *
     * @param int $school
     *
     * @return LegacyAcademicYearStageBuilder
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_ref_cod_escola', $school);
    }

    /**
     * Ordena por Sequencial
     *
     * @return LegacyAcademicYearStageBuilder
     */
    public function orderBySequencial(): self
    {
        return $this->orderBy('sequencial');
    }
}
