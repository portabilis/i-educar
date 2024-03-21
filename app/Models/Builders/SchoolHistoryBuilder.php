<?php

namespace App\Models\Builders;

use App\Models\LegacySchoolHistory;
use App\Models\SchoolHistoryStatus;

class SchoolHistoryBuilder extends LegacyBuilder
{
    public function active(): self
    {
        return $this->where('historico_escolar.ativo', 1);
    }

    public function whereDependency(bool $dependency): self
    {
        if ($dependency) {
            return $this->where('dependencia', true);
        }

        return $this->where(function ($query) {
            $query->whereNull('dependencia');
            $query->orWhere('dependencia', false);
        });
    }

    public function whereNotExtraCurricular(bool $extra): self
    {
        return $this->where('extra_curricular', $extra);
    }

    public function onlyGradeNumeric(): self
    {
        return $this->whereRaw('isnumeric(substring(nm_serie, 1, 1)) = ?', true);
    }

    public function whereCourseEja(): self
    {
        return $this->where('historico_grade_curso_id', LegacySchoolHistory::GRADE_EJA);
    }

    public function whereStartAfter(int $start): self
    {
        return $this->where('ano', '>=', $start);
    }

    public function whereEndBefore(int $end): self
    {
        return $this->where('ano', '<=', $end);
    }

    public function whereCourses(string $coursesNames): self
    {
        return $this->whereRaw("nm_curso in ($coursesNames)");
    }

    public function validStatusEja(): LegacyBuilder
    {
        return $this->whereIn('aprovado', [
            SchoolHistoryStatus::APPROVED,
            SchoolHistoryStatus::REPROVED,
            SchoolHistoryStatus::ONGOING,
            SchoolHistoryStatus::REPROVED_BY_ABSENCE,
        ]);
    }

    public function validStatus(): LegacyBuilder
    {
        return $this->whereIn('aprovado', [
            SchoolHistoryStatus::APPROVED,
            SchoolHistoryStatus::APPROVED_WITH_DEPENDENCY,
            SchoolHistoryStatus::APPROVED_BY_BOARD,
            SchoolHistoryStatus::ONGOING,
        ]);
    }
}
