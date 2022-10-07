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
     *
     * @param string $relation
     * @param array  $columns
     *
     * @return array|null
     */
    public function getLegacyExportedColumns(string $relation, array $columns): array|null
    {
        //obtem a lista de colunas e nome legados
        $legacy = $this->getLegacyColumns();
        //obtem somente as columas do relacionamento
        $legacyColumns = $legacy[$relation];
        //filtra somente as keys
        $keys = array_keys($legacyColumns);
        //usa as key filtradas para verificar se existem na seleção do exportador
        $filterColumns = array_intersect($keys, $columns);
        //gera novas colunas com os atributos legados e seus aliases visiveis no arquivo exportado
        $newColumns = [];

        foreach ($filterColumns as $column) {
            if (array_key_exists($column, $legacyColumns)) {
                $newColumns[$column] = $legacyColumns[$column];
            }
        }

        //se não encontrar, deve-se retornar null, para a query não adicionar o join da relação
        //carregando assim só o necessário
        if (empty($newColumns)) {
            return null;
        }

        return $newColumns;
    }
}
