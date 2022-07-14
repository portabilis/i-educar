<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class LegacyCourseBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return AnonymousResourceCollection
     */
    public function getResource(array $filters = []): AnonymousResourceCollection
    {
        //filtros
        $this->active()->orderByName()->filter($filters);
        //query específica obtem valores passados pelos filtros
        $this->whereNotIsStandardCalendar($this->filterEqualTo('not_pattern', '1'));
        //description será usada em getNameAttribute, mas não aparece no recurso
        $resource = $this->setExcept(['description'])->resource(['id', 'name', 'is_standard_calendar', 'steps']);

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
}
