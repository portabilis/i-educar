<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class LegacySchoolGradeDisciplineBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return AnonymousResourceCollection
     */
    public function getResource(array $filters = []): AnonymousResourceCollection
    {
        $this->distinctDiscipline()->with('discipline:id,nome')->filter($filters);
        //não não aparece na query, mas é adicionado no recurso
        $resource = $this->resource(['id', 'workload'], ['name']);

        return JsonResource::collection($resource);
    }

    /**
     * Filtra por Escola
     *
     * @param int|null $school
     * @return $this
     */
    public function filterSchool(int $school = null): self
    {
        return $this->when($school, fn($q) => $q->whereSchool($school));
    }

    /**
     * Filtra por Série
     *
     * @param int|null $grade
     * @return $this
     */
    public function filterGrade(int $grade = null): self
    {
        return $this->when($grade, fn($q) => $q->whereGrade($grade));
    }
}
