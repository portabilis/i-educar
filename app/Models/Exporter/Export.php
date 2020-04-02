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
    ];

    /**
     * @var array
     */
    protected $casts = [
        'fields' => 'json',
    ];

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
     * @return array
     */
    public function getExportHeading()
    {
        $model = $this->newExportModel();
        $allowed = $model->getAllowedExportedColumns();

        $headers = [];

        foreach ($this->fields as $field) {
            $headers[] = $allowed[$field];
        }

        return $headers;
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

        return $query;
    }
}
