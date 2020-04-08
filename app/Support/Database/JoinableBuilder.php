<?php

namespace App\Support\Database;

trait JoinableBuilder
{
    public function joinColumns($table, $columns)
    {
        return array_map(function ($column) use ($table) {
            $alias = $table === $column ? $table : "{$table}_{$column}";

            return "{$table}.{$column} as {$alias}";
        }, $columns);
    }

    public function select($columns = ['*'])
    {
        $columns = array_map(function ($column) {
            return "{$this->getModel()->getTable()}.{$column}";
        }, $columns);

        return parent::select($columns);
    }
}
