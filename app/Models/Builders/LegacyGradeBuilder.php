<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacyGradeBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
     *
     * @param array $filters
     *
     * @return Collection
     */
    public function getResource(array $filters = []): Collection
    {
        $this->active()->orderByNameAndCourse()->filter($filters);
        //description será usada em getNameAttribute, mas não aparece no recurso
        return $this->setExcept(['description'])->resource(['id', 'name']);
    }

    /**
     * Filtra por nome
     *
     * @param string $name
     *
     * @return LegacyGradeBuilder
     */
    public function whereName(string $name): self
    {
        return $this->whereRaw('unaccent(nm_serie) ~* unaccent(?)', $name);
    }

    /**
     * Ordena por nome e curso
     *
     * @return LegacyGradeBuilder
     */
    public function orderByNameAndCourse(): self
    {
        return $this->orderBy('nm_serie')->orderBy('ref_cod_curso');
    }

    /**
     * Filtra por Séries não presentes na escola
     *
     * @param int $school_exclude
     *
     * @return LegacyGradeBuilder
     */
    public function whereSchoolExclude(int $school_exclude): self
    {
        return $this->whereDoesntHave('schools', function ($q) use ($school_exclude) {
            $q->where('cod_escola', $school_exclude);
        });
    }

    /**
     * Filtra por séries presentes na escola
     *
     * @param int $school
     *
     * @return LegacyGradeBuilder
     */
    public function whereSchool(int $school): self
    {
        return $this->whereHas('schools', function ($q) use ($school) {
            $q->where('cod_escola', $school);
        });
    }

    /**
     * Filtra diferentes series
     *
     * @param int $serie_exclude
     *
     * @return LegacyGradeBuilder
     */
    public function whereGradeExclude(int $serie_exclude): self
    {
        return $this->where('cod_serie', '<>', $serie_exclude);
    }

    /**
     * Filtra por Curso
     *
     * @param int $course
     *
     * @return LegacyGradeBuilder
     */
    public function whereCourse(int $course): self
    {
        return $this->where('ref_cod_curso', $course);
    }

    /**
     * Filtra por ativos
     *
     * @return LegacyGradeBuilder
     */
    public function active(): self
    {
        return $this->where('serie.ativo', 1);
    }
}
