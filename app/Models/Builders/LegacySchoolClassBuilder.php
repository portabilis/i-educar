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
     *
     * @param array $filters
     *
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        $this->active()->orderByName()->filter($filters);

        return $this->setExcept(['year'])->resource(['id', 'name']);
    }

    /**
     * Filtra por ativo
     *
     * @return LegacySchoolClassBuilder
     */
    public function active(): self
    {
        return $this->where('ativo', 1);
    }

    public function whereActive(int $active): self
    {
        return $this->where('ativo', $active);
    }

    /**
     * Filtra por Serie
     *
     * @param int $grade
     *
     * @return LegacySchoolClassBuilder
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
     *
     * @param int $school
     *
     * @return LegacySchoolClassBuilder
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_ref_cod_escola', $school);
    }

    /**
     * Filtra por acesso escola
     *
     * @param int $user
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
     *
     * @param int $year
     *
     * @return LegacySchoolClassBuilder
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
     *
     * @return LegacySchoolClassBuilder
     */
    public function whereInProgress(): self
    {
        return $this->whereHas('academicYears', function ($q) {
            $q->inProgress();
        });
    }

    /**
     * Filtra por Curso
     *
     * @param int $course
     *
     * @return LegacySchoolClassBuilder
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
     *
     * @param string $direction
     *
     * @return LegacySchoolClassBuilder
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->orderBy('nm_turma', $direction);
    }

    /**
     * Filtra por Instituição
     *
     * @param int $institution
     *
     * @return LegacySchoolClassBuilder
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('ref_cod_instituicao', $institution);
    }

    /**
     * Filtra por ano
     *
     * @param int $year
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
     * @param int $shift_id
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
     * @param bool $visible
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
     * @param string $name
     *
     * @return $this
     */
    public function whereName(string $name): self
    {
        $name = str_replace(['(',')', '[', ']'], '', $name);

        return $this->whereRaw('unaccent(nm_turma) ~* unaccent(?)', preg_replace("/\([^)]+\)/", '', $name));
    }
}
