<?php

namespace App\Models\Exporter;

use App\Models\Exporter\Builders\EnrollmentEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class Stage extends Model
{
    /**
     * @var string
     */
    protected $table = 'exporter_stages';

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
            'Etapas' => [
                'school_name' => 'Escola',
                'school_class' => 'Turma',
                'stage_name' => 'Tipo de etapa',
                'stage_number' => 'Etapa',
                'stage_start_date' => 'Data início',
                'stage_end_date' => 'Data fim',
                'stage_type' => 'Padrão/Turma',
                'posted_data' => 'Possui lançamentos'
            ],
        ];
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Calendário letivo';
    }

    public function getDescription()
    {
        return 'Exportação de todos os calendários letivos do ano filtrado para identificação das datas de início e fim das etapas e existência de lançamentos.';
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
