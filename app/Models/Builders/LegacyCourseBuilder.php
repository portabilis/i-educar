<?php

namespace App\Models\Builders;

use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use Illuminate\Support\Collection;

class LegacyCourseBuilder extends LegacyBuilder
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
        //filtros
        $this->active()->orderByName()->filter($filters);
        //description será usada em getNameAttribute, mas não aparece no recurso
        return $this->setExcept(['description'])->resource(['id', 'name', 'is_standard_calendar', 'steps']);
    }

    /**
     * Filtra por Instituição
     *
     * @param int $institution
     *
     * @return LegacyCourseBuilder
     */
    public function whereInstitution(int $institution): self
    {
        return $this->where('ref_cod_instituicao', $institution);
    }

    /**
     * Ordena por nome
     *
     * @param string $direction
     *
     * @return LegacyCourseBuilder
     */
    public function orderByName(string $direction = 'asc'): self
    {
        return $this->orderBy('nm_curso', $direction);
    }

    /**
     * Filtra por Curso
     *
     * @param int $course
     *
     * @return LegacyCourseBuilder
     */
    public function whereCourse(int $course): self
    {
        return $this->where('cod_curso', $course);
    }

    /**
     * Filtra por  Padrão Ano Escolar
     *
     * @param int $standard_calendar
     *
     * @return LegacyCourseBuilder
     */
    public function whereStandardCalendar(int $standard_calendar): self
    {
        return $this->where('padrao_ano_escolar', $standard_calendar);
    }

    /**
     * Filtra por Escola
     *
     * @param int $school
     *
     * @return LegacyCourseBuilder
     */
    public function whereSchool(int $school, ?int $year = null): self
    {
        return $this->whereHas('schools', function ($q) use ($school, $year) {
            $q->where('cod_escola', $school);
            $q->when($year, function ($q) use ($year) {
                $q->whereRaw("anos_letivos @> ('{{$year}}')");
            });
        });
    }

    public function whereYearEq(int $year): self
    {
        return $this->whereHas('schools', function ($q) use ($year) {
            $q->whereRaw("anos_letivos @> ('{{$year}}')");
        });
    }

    /**
     * Filtra por modalidade
     *
     * @return LegacyCourseBuilder
     */
    public function hasModality(): self
    {
        return $this->where('modalidade_curso', '>', 0);
    }

    /**
     * @return LegacyCourseBuilder
     */
    public function registrationsActiveCurrentYear(): self
    {
        return $this->join('pmieducar.matricula', 'curso.cod_curso', 'matricula.ref_cod_curso')
            ->where('matricula.ano', date('Y'))
            ->where('matricula.ativo', 1);
    }

    /**
     * @return LegacyCourseBuilder
     */
    public function registrationsActiveLastYear(): self
    {
        return $this->join('pmieducar.matricula', 'curso.cod_curso', 'matricula.ref_cod_curso')
            ->where('matricula.ano', date('Y') - 1)
            ->where('matricula.ativo', 1);
    }

    /**
     * Filtra por ativo
     *
     * @return LegacyCourseBuilder
     */
    public function active(): self
    {
        return $this->where('curso.ativo', 1);
    }

    /**
     * Filtra por modalidade Eja
     *
     * @return LegacyCourseBuilder
     */
    public function isEja(): self
    {
        return $this->where('modalidade_curso', ModalidadeCurso::EJA);
    }
}
