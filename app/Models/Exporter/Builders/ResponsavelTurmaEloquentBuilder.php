<?php

namespace App\Models\Exporter\Builders;

use App\Support\Database\JoinableBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class ResponsavelTurmaEloquentBuilder extends Builder
{
    use JoinableBuilder;

    /**
     * @param array $columns
     *
     * @return ResponsavelTurmaEloquentBuilder
     */
    public function mother($columns)
    {
        $this->addSelect(
            $this->joinColumns('mother', $columns)
        );

        return $this->leftJoin('exporter_responsaveis_turma as mother', function (JoinClause $join) {
            $join->on('exporter_responsaveis_turma.mother_id', '=', 'mother.id');
        });
    }

    /**
     * @param array $columns
     *
     * @return ResponsavelTurmaEloquentBuilder
     */
    public function father($columns)
    {
        $this->addSelect(
            $this->joinColumns('father', $columns)
        );

        return $this->leftJoin('exporter_responsaveis_turma as father', function (JoinClause $join) {
            $join->on('exporter_responsaveis_turma.father_id', '=', 'father.id');
        });
    }

    /**
     * @param array $columns
     *
     * @return ResponsavelTurmaEloquentBuilder
     */
    public function guardian($columns)
    {
        $this->addSelect(
            $this->joinColumns('guardian', $columns)
        );

        return $this->leftJoin('exporter_responsaveis_turma as guardian', function (JoinClause $join) {
            $join->on('exporter_responsaveis_turma.guardian_id', '=', 'guardian.id');
        });
    }

    /**
     * @return ResponsavelTurmaEloquentBuilder
     */
    public function disabilities()
    {
        $this->addSelect(
            $this->joinColumns('disabilities', ['disabilities'])
        );

        return $this->leftJoin('exporter_disabilities as disabilities', function (JoinClause $join) {
            $join->on('exporter_responsaveis_turma.id', '=', 'disabilities.person_id');
        });
    }

    /**
     * @return ResponsavelTurmaEloquentBuilder
     */
    public function phones()
    {
        $this->addSelect(
            $this->joinColumns('phones', ['phones'])
        );

        return $this->leftJoin('exporter_phones as phones', function (JoinClause $join) {
            $join->on('exporter_responsaveis_turma.id', '=', 'phones.person_id');
        });
    }

    /**
     * @param array $columns
     *
     * @return ResponsavelTurmaEloquentBuilder
     */
    public function place($columns)
    {
        $this->addSelect(
            $this->joinColumns('place', $columns)
        );

        return $this->leftJoin('person_has_place', function (JoinClause $join) {
            $join->on('exporter_responsaveis_turma.id', '=', 'person_has_place.person_id');
        })->leftJoin('addresses as place', function (JoinClause $join) {
            $join->on('person_has_place.place_id', '=', 'place.id');
        });
    }
}
