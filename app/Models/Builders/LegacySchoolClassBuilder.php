<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class LegacySchoolClassBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return AnonymousResourceCollection
     */
    public function getResource(array $filters = []): AnonymousResourceCollection
    {
        $this->active()->orderByName()->filter($filters);
        //year é usado na query, mas não aparece no recurso
        $resource = $this->setExcept(['year'])->resource(['id', 'name']);

        return JsonResource::collection($resource);
    }

    /**
     * Filtra por Instituição
     *
     * @param int|null $institution
     * @return $this
     */
    public function filterInstitution(int $institution = null): self
    {
        return $this->when($institution, fn($q) => $q->whereInstitution($institution));
    }

    /**
     * Filtra por Escola
     *
     * @param int|null $school
     * @return $this
     */
    public function filterSchool(int $school = null): self
    {
        return $this->when($school, fn($q) => $q->whereSchool($school));
    }

    /**
     * Filtra por Curso
     *
     * @param int|null $course
     * @return $this
     */
    public function filterCourse(int $course = null): self
    {
        return $this->when($course, fn($q) => $q->whereCourse($course));
    }

    /**
     * Filtra por Série
     *
     * @param int|null $grade
     * @return $this
     */
    public function filterGrade(int $grade = null): self
    {
        return $this->when($grade, fn($q) => $q->whereGrade($grade));
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
     * @param int|null $year
     * @return $this
     */
    public function filterYear(int $year = null): self
    {
        return $this->when($year, fn($q) => $q->whereInProgressYear($year));
    }
}
