<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class LegacyGradeBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return AnonymousResourceCollection
     */
    public function getResource(array $filters = []): AnonymousResourceCollection
    {
        $this->active()->orderByNameAndCourse()->filter($filters);
        //description será usada em getNameAttribute, mas não aparece no recurso
        $resource = $this->setExcept(['description'])->resource(['id', 'name']);

        return JsonResource::collection($resource);
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
     * Filtra removendo séries da lista
     *
     * @param int|null $grade_exclude
     * @return $this
     */
    public function filterGradeExclude(int $grade_exclude = null): self
    {

        return $this->when($grade_exclude, fn($q) => $q->whereNotGrade($grade_exclude));
    }

    /**
     * Filtra removendo escolas da lisa
     *
     * @param int|null $school_exclude
     * @return $this
     */
    public function filterSchoolExclude(int $school_exclude = null): self
    {
        return $this->when($school_exclude, fn($q) => $q->whereNotSchool($school_exclude));
    }

}
