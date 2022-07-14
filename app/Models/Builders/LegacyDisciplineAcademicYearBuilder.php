<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class LegacyDisciplineAcademicYearBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return AnonymousResourceCollection
     */
    public function getResource(array $filters = []): AnonymousResourceCollection
    {
        //filtros
        $this->distinctDiscipline()->with('discipline:id,nome')->filter($filters);
        //name é uma parametro adicional, que não está na query, mas é passado para o recurso sendo aplicado em getNameAttribute
        $resource = $this->resource(['id', 'workload'], ['name']);

        return JsonResource::collection($resource);
    }

    /**
     * Filtra por Curso
     *
     * @param int|null $course
     * @return $this
     */
    public function filterCourse(int $course = null): self
    {
        return $this->when($course, fn($q) => $q->whereCourse($course));
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
