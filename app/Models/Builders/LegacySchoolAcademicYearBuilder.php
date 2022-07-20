<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacySchoolAcademicYearBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        $this->active()->orderByYear()->filter($filters);
        return $this->resource(['year']);
    }

    /**
     * Filtra por Escola
     *
     * @param int $school
     * @return $this
     */
    public function filterSchool(int $school): self
    {
        return $this->whereSchool($school);
    }

    /**
     * Filtra por anos maiores
     *
     * @param int $year
     * @return $this
     */
    public function filterYear(int $year): self
    {
        return $this->whereGteYear($year);
    }

    /**
     * Filtra por anos maiores
     *
     * @param int $year
     * @return LegacySchoolAcademicYearBuilder
     */
    public function whereGteYear(int $year): self
    {
        return $this->where('ano', '>=', $year);
    }
}
