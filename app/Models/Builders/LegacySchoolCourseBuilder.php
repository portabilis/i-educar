<?php

namespace App\Models\Builders;

class LegacySchoolCourseBuilder extends LegacyBuilder
{
    /**
     * Filtra por ativo
     */
    public function active(): self
    {
        return $this->where('ativo', 1);
    }

    /**
     * Filtra por Escola
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_cod_escola', $school);
    }

    /**
     * Filtra por Curso
     */
    public function whereCourse(int $course): self
    {
        return $this->where('ref_cod_curso', $course);
    }

    /**
     * Ordena por Escola e Curso
     */
    public function orderBySchoolCourse(): self
    {
        return $this->orderBy('ref_cod_escola')->orderBy('ref_cod_curso');
    }
}
