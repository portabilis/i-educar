<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacyDisciplineAcademicYearBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
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
     */
    public function distinctDiscipline(): self
    {
        return $this->distinct('componente_curricular_id');
    }

    /**
     * Filtra por série
     */
    public function whereGrade(int $grade): self
    {
        return $this->where('ano_escolar_id', $grade);
    }

    /**
     * Filtra por curso
     */
    public function whereCourse(int $course): self
    {
        return $this->whereHas('grade', function ($q) use ($course) {
            $q->whereCourse($course);
        });
    }

    /**
     * Filtra por ano letivo
     *
     *
     * @return LegacySchoolGradeDisciplineBuilder
     */
    public function whereYearEq(int $year)
    {
        return $this->whereRaw("anos_letivos @> ('{{$year}}')");
    }

    /**
     * Filtra por Disciplina
     *
     *
     * @return LegacySchoolGradeDisciplineBuilder
     */
    public function whereDiscipline(int $discipline): self
    {
        return $this->where('componente_curricular_id', $discipline);
    }
}
