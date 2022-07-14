<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
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
}
