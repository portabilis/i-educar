<?php

namespace App\Models\Builders;

class LegacyDisciplineExemptionBuilder extends LegacyBuilder
{
    public function whereYearEq(int $year): self
    {
        return $this->whereHas('registration', static function ($query) use ($year) {
            $query->where('ano', $year);
        });
    }

    public function whereCourse(int $course): self
    {
        return $this->whereHas('registration', function ($registrationQuery) use ($course) {
            $registrationQuery->where('ref_cod_curso', $course);
        });
    }

    public function whereSchools(...$schools): self
    {
        return $this->whereIn('ref_cod_escola', $schools);
    }

    public function whereGrade(int $grade): self
    {
        return $this->where('ref_cod_serie', $grade);
    }

    public function whereDiscipline(int $discipline): self
    {
        return $this->where('ref_cod_disciplina', $discipline);
    }

    public function orderByCreatedAt(string $direction = 'desc'): self
    {
        return $this->orderBy('data_cadastro', $direction);
    }
}
