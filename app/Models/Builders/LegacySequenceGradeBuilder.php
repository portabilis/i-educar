<?php

namespace App\Models\Builders;

class LegacySequenceGradeBuilder extends LegacyBuilder
{
    /**
     * Filtra por Instituição
     */
    public function whereInstitution(int $institution): self
    {
        return $this->whereHas('gradeOrigin.course', static fn ($q) => $q->whereInstitution($institution));
    }

    /**
     * Filtra por Serie de Origem
     */
    public function whereGradeOrigin(int $grade): self
    {
        return $this->where('ref_serie_origem', $grade);
    }

    /**
     * Filtra por Serie de Destino
     */
    public function whereGradeDestiny(int $grade): self
    {
        return $this->where('ref_serie_destino', $grade);
    }

    /**
     * Filtra por Curso de Origem
     */
    public function whereCourseOrigin(int $course): self
    {
        return $this->whereHas('gradeOrigin', static fn ($q) => $q->whereCourse($course));
    }

    /**
     * Filtra por Curso de Destino
     */
    public function whereCourseDestiny(int $course): self
    {
        return $this->whereHas('gradeDestiny', static fn ($q) => $q->whereCourse($course));
    }

    /**
     * Filtra por ativo
     */
    public function active(): self
    {
        return $this->where('ativo', 1);
    }
}
