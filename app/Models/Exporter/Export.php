<?php

namespace App\Models\Exporter;

use App\Exports\EloquentExporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Export extends Model
{
    /**
     * @var string
     */
    protected $table = 'export';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'model',
        'fields',
        'url',
        'hash',
        'filename',
        'filters',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'fields' => 'json',
        'filters' => 'json',
    ];

    /**
     * @return array
     */
    public function getAllowedExports()
    {
        return [
            1 => new Student(),
            2 => new Teacher(),
            3 => new SocialAssistance(),
            4 => new Stage(),
        ];
    }

    /**
     * @param int $code
     *
     * @return mixed
     */
    public function getExportByCode($code)
    {
        return $this->getAllowedExports()[$code] ?? new Student();
    }

    /**
     * @return EloquentExporter
     */
    public function getExporter()
    {
        return new EloquentExporter($this);
    }

    /**
     * @return Model
     */
    public function newExportModel()
    {
        $model = $this->model;

        return new $model();
    }

    /**
     * @return Builder
     */
    public function newExportQueryBuilder()
    {
        return $this->newExportModel()->newQuery();
    }

    /**
     * @return Builder
     */
    public function getExportQuery()
    {
        $select = [];
        $relations = [];

        foreach ($this->fields as $field) {
            if (!Str::contains($field, '.')) {
                $select[] = $field;

                continue;
            }

            [$relation, $column] = explode('.', $field);

            $relations[$relation][] = $column;
        }

        $query = $this->newExportQueryBuilder()->select($select);

        foreach ($relations as $relation => $columns) {
            $query->{$relation}($columns);
        }

        $this->applyFilters($query);

        return $query;
    }

    /**
     * @param Builder $query
     */
    public function applyFilters(Builder $query)
    {
        foreach ($this->filters as $filter) {
            $column = $filter['column'];
            $operator = $filter['operator'];
            $value = $filter['value'];

            switch ($operator) {
                case '=':
                    $query->whereRaw("{$column} {$operator} {$value}");
                    break;

                case 'in':
                    $value = implode(', ', $value);
                    $query->whereRaw("{$column} {$operator} ({$value})");
                    break;
            }
        }
    }
}
