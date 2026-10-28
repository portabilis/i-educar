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
    public function whereSchool(int $school, $year = null): self
    {
        return $this->whereExists(function ($q) use ($school, $year) {
            $q->selectRaw('1')
                ->from('pmieducar.escola_serie')
                ->whereColumn('pmieducar.escola_serie.ref_cod_serie', 'pmieducar.serie.cod_serie')
                ->where('pmieducar.escola_serie.ativo', 1)
                ->where('pmieducar.escola_serie.ref_cod_escola', $school)
                ->when($year, fn ($q) => $q->whereRaw('? = ANY(pmieducar.escola_serie.anos_letivos)', $year));
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
        return $this->whereExists(function ($q) use ($institution) {
            $q->selectRaw('1')
                ->from('pmieducar.curso')
                ->whereColumn('pmieducar.curso.cod_curso', 'pmieducar.serie.ref_cod_curso')
                ->where('pmieducar.curso.ref_cod_instituicao', $institution);
        });
    }

    /**
     * Filtra por ano letivo
     */
    public function whereAcademicYear(int $year): self
    {
        return $this->whereExists(function ($q) use ($year) {
            $q->selectRaw('1')
                ->from('pmieducar.escola_serie')
                ->whereColumn('pmieducar.escola_serie.ref_cod_serie', 'pmieducar.serie.cod_serie')
                ->where('pmieducar.escola_serie.ativo', 1)
                ->whereRaw('? = ANY(pmieducar.escola_serie.anos_letivos)', $year);
        });
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
