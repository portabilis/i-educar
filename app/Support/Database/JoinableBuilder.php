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

    /**
     * Obtem dinamicamente filtrado a lista de colunas da relação
     */
    public function getLegacyExportedColumns(string $relation, array $columns): ?array
    {
        $legacy = $this->getLegacyColumns();
        $legacyColumns = $legacy[$relation];
        $keys = array_keys($legacyColumns);
        $filterColumns = array_intersect($keys, $columns);
        $newColumns = [];

        foreach ($filterColumns as $column) {
            if (array_key_exists($column, $legacyColumns)) {
                $newColumns[$column] = $legacyColumns[$column];
            }
        }

        if (empty($newColumns)) {
            return null;
        }

        return $newColumns;
    }
}
