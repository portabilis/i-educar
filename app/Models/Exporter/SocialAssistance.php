<?php

namespace App\Models\Exporter;

use App\Models\Exporter\Builders\EnrollmentEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class SocialAssistance extends Model
{
    /**
     * @var string
     */
    protected $table = 'exporter_social_assistance';

    /**
     * @var Collection
     */
    protected $alias;

    /**
     * @param Builder $query
     *
     * @return EnrollmentEloquentBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new EnrollmentEloquentBuilder($query);
    }

    /**
     * @return array
     */
    public function getExportedColumnsByGroup()
    {
        return [
            'Aluno' => [
                'name' => 'Nome',
                'cpf' => 'CPF',
                'date_of_birth' => 'Data de nascimento',
                'nis' => 'NIS (PIS/PASEP)',
            ],
            'Escola' => [
                'school' => 'Escola',
                'school_inep' => 'Código INEP',
                'period' => 'Turno',
                'school_class_stage' => 'Etapa Educacenso',
                'attendance_type' => 'Tipo de atendimento da turma',
            ],
        ];
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Dados de escolaridade - Assistência Social';
    }

    public function getDescription()
    {
        return 'Os dados exportados serão contabilizados por quantidade de matrículas dos(as) alunos(as), duplicando o(a) aluno(a) caso o mesmo possua mais de uma matrícula no ano filtrado. Opção utilizada para integração com sistemas de Assistência social que coletem dados de escolaridade das famílias atendidas.';
    }

    /**
     * @param string $column
     *
     * @return string
     */
    public function alias($column)
    {
        if (empty($this->alias)) {
            $this->alias = collect($this->getExportedColumnsByGroup())->flatMap(function ($item) {
                return $item;
            });
        }

        return $this->alias->get($column, $column);
    }
}
