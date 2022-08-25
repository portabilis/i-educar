<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacySchoolGradeDisciplineBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     *
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        $this->distinctDiscipline()->with('discipline:id,nome')->filter($filters);
        //não não aparece na query, mas é adicionado no recurso
        return $this->resource(['id', 'workload'], ['name']);
    }

    /**
     * Filtra somente os distintos por id
     *
     * @return LegacySchoolGradeDisciplineBuilder
     */
    public function distinctDiscipline(): self
    {
        return $this->distinct('ref_cod_disciplina');
    }

    /**
     * Filtra por escola
     *
     * @param int $grade
     *
     * @return LegacySchoolGradeDisciplineBuilder
     */
    public function whereGrade(int $grade): self
    {
        return $this->where('ref_ref_cod_serie', $grade);
    }

    /**
     * Filtra por escola
     *
     * @param int $school
     *
     * @return LegacySchoolGradeDisciplineBuilder
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_ref_cod_escola', $school);
    }
}
