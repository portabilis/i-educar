<?php

namespace App\Models\Builders;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class LegacyCourseBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        //filtros
        $this->active()->orderByName()->filter($filters);
        //query específica obtem valores passados pelos filtros
        $this->whereNotIsStandardCalendar($this->filterEqualTo('not_pattern', '1'));
        //description será usada em getNameAttribute, mas não aparece no recurso
        return $this->setExcept(['description'])->resource(['id', 'name', 'is_standard_calendar', 'steps']);
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
}
