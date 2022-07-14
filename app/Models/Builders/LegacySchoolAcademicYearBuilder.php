<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class LegacySchoolAcademicYearBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return AnonymousResourceCollection
     */
    public function getResource(array $filters = []): AnonymousResourceCollection
    {
        $this->active()->orderByYear()->filter($filters);
        $resource = $this->resource(['year']);

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
     * Filtra por Anos maiores
     *
     * @param int|null $year
     * @return $this
     */
    public function filterYear(int $year = null): self
    {
        return $this->when($year, fn($q) => $q->whereGteYear($year));
    }
}
