<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class LegacyDisciplineAcademicYearBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        //filtros
        $this->distinctDiscipline()->with('discipline:id,nome')->filter($filters);
        //name é uma parametro adicional, que não está na query, mas é passado para o recurso sendo aplicado em getNameAttribute
        return $this->resource(['id', 'workload'], ['name']);
    }

    /**
     * Filtra por Curso
     *
     * @param int $course
     * @return $this
     */
    public function filterCourse(int $course): self
    {
        return $this->whereCourse($course);
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
