<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacyDisciplineAcademicYearBuilder extends LegacyBuilder
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
        //filtros
        $this->distinctDiscipline()->with('discipline:id,nome')->filter($filters);
        //name é uma parametro adicional, que não está na query, mas é passado para o recurso sendo aplicado em getNameAttribute
        return $this->resource(['id', 'workload'], ['name']);
    }

    /**
     * Filtra somente os distintos por id
     *
     * @return LegacyDisciplineAcademicYearBuilder
     */
    public function distinctDiscipline(): self
    {
        return $this->distinct('componente_curricular_id');
    }

    /**
     * Filtra por série
     *
     * @param int $grade
     *
     * @return LegacyDisciplineAcademicYearBuilder
     */
    public function whereGrade(int $grade): self
    {
        return $this->where('ano_escolar_id', $grade);
    }

    /**
     * Filtra por curso
     *
     * @param int $course
     *
     * @return LegacyDisciplineAcademicYearBuilder
     */
    public function whereCourse(int $course): self
    {
        return $this->whereHas('grade', function ($q) use ($course) {
            $q->whereCourse($course);
        });
    }
}
