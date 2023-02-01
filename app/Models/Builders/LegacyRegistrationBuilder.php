<?php

namespace App\Models\Builders;

class LegacyRegistrationBuilder extends LegacyBuilder
{
    /**
     * Filtra por Escola
     *
     * @param int $school
     *
     * @return LegacyRegistrationBuilder
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_ref_cod_escola', $school);
    }

    /**
     * Filtra por Turma
     *
     * @param int $schoolClass
     *
     * @return LegacyRegistrationBuilder
     */
    public function whereSchoolClass(int $schoolClass): self
    {
        return $this->whereHas('enrollments', static fn ($q) => $q->where('ref_cod_turma', $schoolClass));
    }

    /**
     * Filtra por ativo
     *
     * @return LegacyRegistrationBuilder
     */
    public function active(): self
    {
        return $this->where($this->model->getTable().'.ativo', 1);
    }

    /**
     * Filtra por ano
     *
     * @param int $year
     *
     * @return $this
     */
    public function whereYearEq(int $year): self
    {
        return $this->where($this->model->getTable().'ano', $year);
    }
}
