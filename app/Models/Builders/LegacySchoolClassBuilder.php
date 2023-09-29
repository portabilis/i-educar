<?php

namespace App\Models\Builders;

use App\Models\DeficiencyType;
use Illuminate\Support\Collection;

class LegacySchoolClassBuilder extends LegacyBuilder
{
    public function whereDifferentStudents($schoolClass): self
    {
        return $this->whereKey($schoolClass)
            ->whereHas('enrollments', function ($q) {
                $q->whereValid();
                $q->whereHas('registration', function ($q) {
                    $q->active();
                    $q->whereHas('student.individual.deficiency', fn ($q) => $q->where('desconsidera_regra_diferenciada', false)->where('deficiency_type_id', DeficiencyType::DEFICIENCY));
                });
            });
    }

    /**
     * Retorna o recurso para os selects dos formulários
     */
    public function getResource(array $filters = []): Collection
    {
        $this->active()->orderByName()->filter($filters);

        return $this->setExcept(['year'])->resource(['id', 'name']);
    }

    /**
     * Filtra por ativo
     */
    public function active(): self
    {
        return $this->where('turma.ativo', 1);
    }

    /**
     * Filtra por visível
     */
    public function visible(): self
    {
        return $this->where('visivel', true);
    }

    public function whereActive(int $active): self
    {
        return $this->where('ativo', $active);
    }

    /**
     * Filtra por Serie
     */
    public function whereGrade(int $grade): self
    {
        return $this->where(function ($q) use ($grade) {
            $q->where('ref_ref_cod_serie', $grade);
            $q->orWhereHas('grades', function ($q) use ($grade) {
                $q->where('cod_serie', $grade);
            });
        });
    }

    /**
     * Filtra por Escola
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_ref_cod_escola', $school);
    }

    /**
     * Filtra por Turno
     */
    public function wherePeriod(int $period): self
    {
        return $this->where('turma_turno_id', $period);
    }

    /**
     * Filtra por acesso escola
     *
     *
     * @return $this
     */
    public function whereSchoolUser(int $user): self
    {
        return $this->whereHas('school.schoolUsers', function ($q) use ($user) {
            $q->where('ref_cod_usuario', $user);
        });
    }

    /**
     * Filtra por ano e em progresso
     */
    public function whereInProgressYear(int $year): self
    {
        return $this->whereHas('academicYears', function ($q) use ($year) {
            $q->inProgress();
            $q->whereYearEq($year);
        });
    }

    /**
     * Filtra por ano escolar em progresso
     */
    public function whereInProgress(): self
    {
        return $this->whereHas('academicYears', function ($q) {
            $q->inProgress();
        });
    }

    /**
     * Filtra por Curso
     */
    public function whereCourse(int $course): self
    {
        return $this->where(function ($q) use ($course) {
            $q->where('ref_cod_curso', $course);
            $q->orWhereHas('grades', function ($q) use ($course) {
                $q->where('ref_cod_curso', $course);
            });
        });
    }

    /**
     * Ordena por nome
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->orderBy('nm_turma', $direction);
    }

    /**
     * Filtra por Instituição
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('ref_cod_instituicao', $institution);
    }

    /**
     * Filtra por ano
     *
     *
     * @return $this
     */
    public function whereYearEq(int $year): self
    {
        return $this->where('ano', $year);
    }

    /**
     * Filtra por turno
     *
     *
     * @return $this
     */
    public function whereShift(int $shift_id): self
    {
        return $this->where('turma_turno_id', $shift_id);
    }

    /**
     * Filtra visibilidade
     *
     *
     * @return $this
     */
    public function whereVisible(bool $visible): self
    {
        return $this->where('visivel', $visible);
    }

    /**
     * Filtra por nome do curso
     *
     *
     * @return $this
     */
    public function whereName(string $name): self
    {
        $name = str_replace(['(', ')', '[', ']'], '', $name);

        return $this->whereRaw('unaccent(nm_turma) ~* unaccent(?)', preg_replace("/\([^)]+\)/", '', $name));
    }

    /**
     * Filtra dia da semana
     *
     *
     * @return $this
     */
    public function whereDayWeek(string $dayWeek): self
    {
        return $this->whereRaw("dias_semana && ('{{$dayWeek}}')");
    }
}
