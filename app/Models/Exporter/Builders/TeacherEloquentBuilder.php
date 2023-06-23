<?php

namespace App\Models\Exporter\Builders;

use App\Support\Database\JoinableBuilder;
use iEducar\Modules\Servidores\Model\FuncaoExercida;
use iEducar\Modules\Servidores\Model\TipoVinculo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class TeacherEloquentBuilder extends Builder
{
    use JoinableBuilder;

    /**
     * @param array $columns
     * @return TeacherEloquentBuilder
     */
    public function person($columns)
    {
        $this->addSelect(
            $this->joinColumns('person', $columns)
        );

        return $this->join('exporter_person as person', function (JoinClause $join) {
            $join->on('exporter_teacher.id', '=', 'person.id');
        });
    }

    /**
     * @return TeacherEloquentBuilder
     */
    public function disabilities()
    {
        $this->addSelect(
            $this->joinColumns('disabilities', ['disabilities'])
        );

        return $this->leftJoin('exporter_disabilities as disabilities', function (JoinClause $join) {
            $join->on('exporter_teacher.id', '=', 'disabilities.person_id');
        });
    }

    /**
     * @return TeacherEloquentBuilder
     */
    public function phones()
    {
        $this->addSelect(
            $this->joinColumns('phones', ['phones'])
        );

        return $this->leftJoin('exporter_phones as phones', function (JoinClause $join) {
            $join->on('exporter_teacher.id', '=', 'phones.person_id');
        });
    }

    /**
     * @param array $columns
     * @return TeacherEloquentBuilder
     */
    public function place($columns)
    {
        $this->addSelect(
            $this->joinColumns('place', $columns)
        );

        return $this->leftJoin('person_has_place', function (JoinClause $join) {
            $join->on('exporter_teacher.id', '=', 'person_has_place.person_id');
        })->leftJoin('addresses as place', function (JoinClause $join) {
            $join->on('person_has_place.place_id', '=', 'place.id');
        });
    }

    /**
     * @return TeacherEloquentBuilder
     */
    public function disciplines()
    {
        $this->addSelect(
            $this->joinColumns('disciplines', ['disciplines'])
        );

        return $this->leftJoin('exporter_teacher_disciplines as disciplines', function (JoinClause $join) {
            $join->on('exporter_teacher.pivot_id', '=', 'disciplines.pivot_id');
        });
    }

    public function allocations($columns)
    {

        if (in_array('funcao_exercida', $columns)) {
            unset($columns[array_search('funcao_exercida', $columns)]);

            $this->addSelect(DB::raw('
                CASE allocations.funcao_exercida
                    WHEN ' . FuncaoExercida::DOCENTE . ' THEN \'Docente\'::varchar
                    WHEN ' . FuncaoExercida::AUXILIAR_EDUCACIONAL . ' THEN \'Auxiliar/Assistente educacional\'::varchar
                    WHEN ' . FuncaoExercida::MONITOR_ATIVIDADE_COMPLEMENTAR . ' THEN \'Profissional/Monitor de atividade complementar\'::varchar
                    WHEN ' . FuncaoExercida::INTERPRETE_LIBRAS . ' THEN \'Tradutor Intérprete de LIBRAS\'::varchar
                    WHEN ' . FuncaoExercida::DOCENTE_TITULAR_EAD . ' THEN \'Docente titular - Coordenador de tutoria (de módulo ou disciplina) - EAD\'::varchar
                    WHEN ' . FuncaoExercida::DOCENTE_TUTOR_EAD . ' THEN \'Docente tutor - Auxiliar (de módulo ou disciplina) - EAD\'::varchar
                    WHEN ' . FuncaoExercida::GUIA_INTERPRETE_LIBRAS . ' THEN \'Guia-Intérprete\'::varchar
                    WHEN ' . FuncaoExercida::APOIO_ALUNOS_DEFICIENCIA . ' THEN \'Profissional de apoio escolar para aluno(a)s com deficiência (Lei 13.146/2015)\'::varchar
                    WHEN ' . FuncaoExercida::INSTRUTOR_EDUCACAO_PROFISSIONAL . ' THEN \'Instrutor da Educação Profissional\'::varchar
                    ELSE \'Não Informado\'::varchar
                END AS "Função Exercida"
            '));
        }

        if (in_array('tipo_vinculo', $columns)) {
            unset($columns[array_search('tipo_vinculo', $columns)]);

            $this->addSelect(DB::raw('
                CASE allocations.tipo_vinculo
                    WHEN ' . TipoVinculo::EFETIVO . ' THEN \'Concursado/efetivo/estável\'::varchar
                    WHEN ' . TipoVinculo::TEMPORARIO . ' THEN \'Contrato temporário\'::varchar
                    WHEN ' . TipoVinculo::TERCEIRIZADO . ' THEN \'Contrato terceirizado\'::varchar
                    WHEN ' . TipoVinculo::CLT . ' THEN \'Contrato CLT\'::varchar
                    ELSE \'Não Informado\'::varchar
                END AS "Tipo Vínculo"
            '));
        }

        $this->addSelect(
            $this->joinColumns('pt', $columns)
        );

        return $this->leftJoin('modules.professor_turma as allocations', function (JoinClause $join) {
            $join->on('allocations.servidor_id', '=', 'exporter_teacher.cod_servidor')
                ->whereRaw('allocations.ano = exporter_teacher.year')
                ->whereRaw('allocations.turma_id = exporter_teacher.school_class_id');
        });
    }
}
