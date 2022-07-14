<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class LegacyGradeBuilder extends LegacyBuilder
{

    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        $this->active()->orderByNameAndCourse()->filter($filters);
        //description será usada em getNameAttribute, mas não aparece no recurso
        return $this->setExcept(['description'])->resource(['id', 'name']);
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
     * Filtra removendo séries da lista
     *
     * @param int $grade_exclude
     * @return $this
     */
    public function filterGradeExclude(int $grade_exclude): self
    {
        return $this->whereNotGrade($grade_exclude);
    }

    /**
     * Filtra removendo escolas da lisa
     *
     * @param int $school_exclude
     * @return $this
     */
    public function filterSchoolExclude(int $school_exclude): self
    {
        return $this->whereNotSchool($school_exclude);
    }

}
