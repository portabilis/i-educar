<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacyGradeBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
     */
    public function getResource(array $filters = []): Collection
    {
        $this->active()->orderByNameAndCourse()->filter($filters);

        //description será usada em getNameAttribute, mas não aparece no recurso
        return $this->setExcept(['description'])->resource(['id', 'name']);
    }

    /**
     * Filtra por nome
     */
    public function whereName(string $name): self
    {
        return $this->whereRaw('unaccent(nm_serie) ~* unaccent(?)', $name);
    }

    /**
     * Ordena por nome e curso
     */
    public function orderByNameAndCourse(): self
    {
        return $this->orderBy('nm_serie')->orderBy('ref_cod_curso');
    }

    /**
     * Filtra por Séries não presentes na escola
     */
    public function whereSchoolExclude(int $school_exclude): self
    {
        return $this->whereDoesntHave('schools', function ($q) use ($school_exclude) {
            $q->where('cod_escola', $school_exclude);
        });
    }

    /**
     * Filtra por séries presentes na escola
     */
    public function whereSchool(int $school): self
    {
        return $this->whereHas('schools', function ($q) use ($school) {
            $q->where('cod_escola', $school);
        });
    }

    /**
     * Filtra diferentes series
     */
    public function whereGradeExclude(int $serie_exclude): self
    {
        return $this->where('cod_serie', '<>', $serie_exclude);
    }

    /**
     * Filtra por Curso
     */
    public function whereCourse(int $course): self
    {
        return $this->where('ref_cod_curso', $course);
    }

    /**
     * Filtra por ativos
     */
    public function active(): self
    {
        return $this->where('serie.ativo', 1);
    }

    public function whereActive(int $active): self
    {
        return $this->where('serie.ativo', $active);
    }
}
