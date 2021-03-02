<?php

namespace App\Support\Database;

trait JoinableBuilder
{
    public function joinColumns($table, $columns)
    {
        $model = $this->getModel();

        return array_map(function ($column) use ($model, $table) {
            $alias = $model->alias("{$table}.{$column}");

            return "{$table}.{$column} as {$alias}";
        }, $columns);
    }

    public function select($columns = ['*'])
    {
        $model = $this->getModel();
        $table = $model->getTable();

        $columns = array_map(function ($column) use ($model, $table) {
            $alias = $model->alias($column);

            return "{$table}.{$column} as {$alias}";
        }, $columns);

        return parent::select($columns);
    }
}
