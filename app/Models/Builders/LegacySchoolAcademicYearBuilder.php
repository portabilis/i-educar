<?php

namespace App\Models\Builders;

use App\Models\LegacySchoolAcademicYear;
use Illuminate\Support\Collection;

class LegacySchoolAcademicYearBuilder extends LegacyBuilder
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
        $this->active()->orderByYear()->filter($filters);

        return $this->get(['ano'])->map(fn ($item) => ['year'=> $item->year]);
    }

    /**
     * Filtra por anos maiores
     *
     * @param int $year
     *
     * @return LegacySchoolAcademicYearBuilder
     */
    public function whereYearGte(int $year): self
    {
        return $this->where('ano', '>=', $year);
    }

    /**
     * Filtra por Instituição
     *
     * @param int $school
     *
     * @return LegacySchoolAcademicYearBuilder
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_cod_escola', $school);
    }

    /**
     * Filtra por ano letivos em andamento
     *
     * @return LegacySchoolAcademicYearBuilder
     */
    public function inProgress(): self
    {
        return $this->where('escola_ano_letivo.andamento', LegacySchoolAcademicYear::IN_PROGRESS);
    }

    /**
     * Filtra por ano letivos que não estão em andamento
     *
     * @return LegacySchoolAcademicYearBuilder
     */
    public function notInProgress(): self
    {
        return $this->where('escola_ano_letivo.andamento', LegacySchoolAcademicYear::FINALIZED);
    }

    /**
     * Ano atual
     *
     * @return $this
     */
    public function currentYear(): self
    {
        return $this->where('escola_ano_letivo.ano', date('Y'));
    }

    /**
     * Ano anterior
     *
     * @return $this
     */
    public function lastYear(): self
    {
        return $this->where('escola_ano_letivo.ano', date('Y') - 1);
    }

    /**
     * Ativo
     *
     * @return $this
     */
    public function active(): self
    {
        return $this->where('escola_ano_letivo.ativo', 1);
    }

    /**
     * Ordena por Ano
     *
     * @param string $direction
     *
     * @return LegacySchoolAcademicYearBuilder
     */
    public function orderByYear(string $direction = 'desc'): self
    {
        return $this->orderBy('ano', $direction);
    }

    /**
     * Filtra pelo ano
     *
     * @param int $year
     *
     * @return LegacySchoolAcademicYearBuilder
     */
    public function whereYearEq(int $year): self
    {
        return $this->where('escola_ano_letivo.ano', $year);
    }
}
