<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacySchoolClassBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        $this->active()->orderByName()->filter($filters);
        //year é usado na query, mas não aparece no recurso
        return $this->setExcept(['year'])->resource(['id', 'name']);
    }

    /**
     * Filtra por Instituição
     *
     * @param int $institution
     * @return $this
     */
    public function filterInstitution(int $institution): self
    {
        return $this->whereInstitution($institution);
    }

    /**
     * Filtra por Escola
     *
     * @param int $school
     * @return $this
     */
    public function filterSchool(int $school): self
    {
        return $this->whereSchool($school);
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

    /**
     * Filtra por anos letivos em progresso
     *
     * @return $this
     */
    public function filterInProgress(): self
    {
        return $this->whereInProgress();
    }

    /**
     * Filtra por ano letivo e em progresso
     *
     * @param int $year
     * @return $this
     */
    public function filterYear(int $year): self
    {
        return $this->whereInProgressYear($year);
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

    /**
     * Filtra por Serie
     *
     * @param int $grade
     * @return LegacySchoolClassBuilder
     */
    public function whereGrade(int $grade): self
    {
        return $this->where(function ($q) use($grade){
            $q->whereHas('grades',function ($q) use($grade){
                $q->where('cod_serie',$grade);
            });
            $q->orWhere('ref_ref_cod_serie',$grade);
        });
    }

    /**
     * Filtra por Escola
     *
     * @param int $school
     * @return LegacySchoolClassBuilder
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_ref_cod_escola', $school);
    }

    /**
     * Filtra por ano e em progresso
     *
     * @param int $year
     * @return LegacySchoolClassBuilder
     */
    public function whereInProgressYear(int $year): self
    {
        return $this->whereHas('academic_years',function ($q) use($year){
            $q->inProgress();
            $q->whereYear($year);
        });
    }

    /**
     * Filtra por ano escolar em progresso
     *
     * @return LegacySchoolClassBuilder
     */
    public function whereInProgress(): self
    {
        return $this->whereHas('academic_years',function ($q){
            $q->inProgress();
        });
    }

    /**
     * Filtra por Curso
     *
     * @param int $course
     * @return LegacySchoolClassBuilder
     */
    public function whereCourse(int $course): self
    {
        return $this->where('ref_cod_curso', $course);
    }

    /**
     * Ordena por nome
     *
     * @param string $direction
     * @return LegacySchoolClassBuilder
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->orderBy('nm_turma',$direction);
    }

    /**
     * Filtra por Instituição
     *
     * @param int $institution
     * @return LegacySchoolClassBuilder
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('ref_cod_instituicao', $institution);
    }
}
