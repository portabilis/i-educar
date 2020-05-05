<?php

namespace App\Models\Exporter;

use App\Models\Exporter\Builders\StudentEloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
     * @return StudentEloquentBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new StudentEloquentBuilder($query);
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
                'grade' => 'Série',
                'course' => 'Curso',
                'period' => 'Turno',
                'school_class_stage' => 'Etapa Educacenso',
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
