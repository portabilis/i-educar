<?php

namespace App\Models\Builders;

use Illuminate\Support\Collection;

class LegacySchoolGradeDisciplineBuilder extends LegacyBuilder
{
    /**
     * Retorna o recurso para os selects dos formulários
     */
    public function getResource(array $filters = []): Collection
    {
        $this->distinctDiscipline()->with('discipline:id,nome')->filter($filters);

        // ref_cod_disciplina entra na query por ser a chave do relacionamento, mas não é retornada
        return $this->setExcept(['ref_cod_disciplina'])
            ->resource(['ref_cod_disciplina as id', 'workload'], ['name']);
    }

    /**
     * Filtra somente os distintos por id
     */
    public function distinctDiscipline(): self
    {
        return $this->distinct('ref_cod_disciplina');
    }

    /**
     * Filtra por série
     */
    public function whereGrade(int $grade): self
    {
        return $this->where('ref_ref_cod_serie', $grade);
    }

    /**
     * Filtra por escola
     */
    public function whereSchool(int $school): self
    {
        return $this->where('ref_ref_cod_escola', $school);
    }

    /**
     * Filtra por Disciplina
     */
    public function whereDiscipline(int $discipline): self
    {
        return $this->where('ref_cod_disciplina', $discipline);
    }

    /**
     * Filtra os ativos
     *
     * @return LegacySchoolGradeDisciplineBuilder
     */
    public function active()
    {
        return $this->where('ativo', 1);
    }

    /**
     * Filtra por ano letivo
     *
     *
     * @return LegacySchoolGradeDisciplineBuilder
     */
    public function whereYearEq(int $year)
    {
        return $this->whereRaw("anos_letivos @> ('{{$year}}')");
    }
}
