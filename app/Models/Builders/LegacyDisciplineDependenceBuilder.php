<?php

namespace App\Models\Builders;

class LegacyDisciplineDependenceBuilder extends LegacyBuilder
{
    /**
     * Filtra por matrícula
     */
    public function whereRegistration(int $registration): self
    {
        return $this->where('ref_cod_matricula', $registration);
    }

    /**
     * Filtra por série
     */
    public function whereGrade(int $grade): self
    {
        return $this->where('ref_cod_serie', $grade);
    }

    /**
     * Filtra por escola
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_cod_escola', $school);
    }

    /**
     * Filtra por disciplina
     */
    public function whereDiscipline(int $discipline): self
    {
        return $this->where('ref_cod_disciplina', $discipline);
    }
}
