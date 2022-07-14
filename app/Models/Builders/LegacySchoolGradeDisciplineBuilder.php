<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class LegacySchoolGradeDisciplineBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        $this->distinctDiscipline()->with('discipline:id,nome')->filter($filters);
        //não não aparece na query, mas é adicionado no recurso
        return $this->resource(['id', 'workload'], ['name']);
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
     * Filtra por Série
     *
     * @param int $grade
     * @return $this
     */
    public function filterGrade(int $grade): self
    {
        return $this->whereGrade($grade);
    }
}
