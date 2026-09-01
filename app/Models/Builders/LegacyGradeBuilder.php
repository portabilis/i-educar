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

        // description será usada em getNameAttribute, mas não aparece no recurso
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
     * Filtra por trecho do nome
     */
    public function whereNameLike(string $name): self
    {
        return $this->whereRaw("unaccent(nm_serie) ILIKE unaccent('%' || ? || '%')", $name);
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
     * Filtra por séries presentes na escola, no ano letivo quando informado
     */
    public function whereSchool(int $school, ?int $year = null): self
    {
        return $this->whereHas('schools', function ($q) use ($school, $year) {
            $q->where('cod_escola', $school);
            $q->when($year !== null, fn ($q) => $q->whereRaw('? = ANY(escola_serie.anos_letivos)', $year));
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
     * Filtra por Instituição
     */
    public function whereInstitution(int $institution): self
    {
        return $this->whereHas('course', fn ($q) => $q->whereInstitution($institution));
    }

    /**
     * Filtra por ano letivo
     */
    public function whereAcademicYear(int $year): self
    {
        return $this->whereHas('schools', fn ($q) => $q->whereRaw('? = ANY(escola_serie.anos_letivos)', $year));
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
