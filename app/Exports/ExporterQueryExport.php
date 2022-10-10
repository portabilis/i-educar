<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomQuerySize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExporterQueryExport implements FromQuery, ShouldQueue, WithCustomQuerySize, WithHeadings
{
    use Exportable;

    public function __construct(private string $connection, private string $model, private array $fields, private array $filters, private int $querySize)
    {
        DB::setDefaultConnection($connection);
    }

    public function querySize(): int
    {
        return $this->querySize;
    }

    public function query()
    {
        return $this->getExportQuery();
    }

    public function getExportQuery(): Builder
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

    public function applyFilters(Builder $query): void
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

    public function newExportQueryBuilder(): Builder
    {
        return $this->newExportModel()->newQuery();
    }

    public function newExportModel(): Model
    {
        $model = $this->model;

        return new $model();
    }

    public function headings(): array
    {
        return array_map(function ($column) {
            return $this->newExportModel()->alias($column);
        }, $this->fields);
    }
}
